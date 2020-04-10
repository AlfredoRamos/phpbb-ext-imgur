<?php

/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\imgur\includes;

use phpbb\config\config;
use phpbb\template\template;
use phpbb\routing\helper as routing_helper;
use phpbb\language\language;

class helper
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\routing\helper */
	protected $routing_helper;

	/** @var \phpbb\language\language */
	protected $language;

	/**
	 * Helper constructor
	 *
	 * @param \phpbb\config\config		$config
	 * @param \phpbb\template\template	$template
	 * @param \phpbb\routing\helper		$routing_helper
	 * @param \phpbb\language\language	$language
	 *
	 * @return void
	 */
	public function __construct(config $config, template $template, routing_helper $routing_helper, language $language)
	{
		$this->config = $config;
		$this->template = $template;
		$this->routing_helper = $routing_helper;
		$this->language = $language;
	}

	/**
	 * Assign global template variables.
	 *
	 * @return void
	 */
	public function assign_template_variables()
	{
		// Enabled output values
		$enabled = $this->enabled_imgur_values('types');

		// Helper
		$data = [
			'access_token' => $this->config['imgur_access_token'],
			'output_type' => $this->config['imgur_output_type'],
			'thumbnail_size' => $this->config['imgur_thumbnail_size']
		];

		// Assign global template variables
		$this->template->assign_vars([
			'IMGUR_UPLOAD_URL' => vsprintf('%1$s/%2$s', [
				$this->routing_helper->route('alfredoramos_imgur_upload'),
				generate_link_hash('imgur_upload')
			]),
			'SHOW_IMGUR_BUTTON' => !empty($data['access_token']),
			'IMGUR_OUTPUT_TYPE' => $data['output_type'],
			'IMGUR_THUMBNAIL_SIZE' => $data['thumbnail_size'],
			'IMGUR_ALLOWED_OUTPUT_TYPES' => implode(',', $enabled)
		]);

		// Assign enabled output types
		foreach ($enabled as $type)
		{
			$this->template->assign_block_vars('IMGUR_ENABLED_OUTPUT_TYPES', [
				'KEY' => $type,
				'NAME' => $this->language->lang(sprintf('IMGUR_OUTPUT_%s', strtoupper($type))),
				'DEFAULT' => ($data['output_type'] === $type)
			]);
		}
	}

	/**
	 * Validate form fields with given filters.
	 *
	 * @param array $fields		Pair of field name and value
	 * @param array $filters	Filters that will be passed to filter_var_array()
	 * @param array $errors		Array of message errors
	 *
	 * @return bool
	 */
	public function validate(&$fields = [], &$filters = [], &$errors = [])
	{
		if (empty($fields) || empty($filters))
		{
			return false;
		}

		// Filter fields
		$data = filter_var_array($fields, $filters, false);

		// Invalid fields helper
		$invalid = [];

		// Validate fields
		foreach ($data as $key => $value)
		{
			// Remove and generate error if field did not pass validation
			// Not using empty() because an empty string can be a valid value
			if (!isset($value) || $value === false)
			{
				$invalid[] = $this->language->lang(
					sprintf('ACP_%s', strtoupper($key))
				);
				unset($fields[$key]);
				continue;
			}
		}

		if (!empty($invalid))
		{
			$errors[]['message'] = $this->language->lang(
				'ACP_IMGUR_VALIDATE_INVALID_FIELDS',
				implode(', ', $invalid)
			);
		}

		// Validation check
		return empty($errors);
	}

	/**
	 * Get Imgur token stored in database.
	 *
	 * @return array
	 */
	public function imgur_token()
	{
		return [
			'access_token'		=> $this->config['imgur_access_token'],
			'expires_in'		=> (int) $this->config['imgur_expires_in'],
			'token_type'		=> $this->config['imgur_token_type'],
			'refresh_token'		=> $this->config['imgur_refresh_token'],
			'account_id'		=> (int) $this->config['imgur_accound_id'],
			'account_username'	=> $this->config['imgur_account_username'],
			'scope'				=> empty($this->config['imgur_scope']) ? null : $this->config['imgur_scope'],
			'created_at'		=> (int) $this->config['imgur_created_at']
		];
	}

	/**
	 * Remove empty items from an array, recursively.
	 *
	 * @param array		$data
	 * @param integer	$max_depth
	 * @param integer	$depth
	 *
	 * @return array
	 */
	public function filter_empty_items($data = [], $max_depth = 5, $depth = 0)
	{
		if (empty($data))
		{
			return [];
		}

		// Cast values
		$depth = abs($depth) + 1;
		$max_depth = abs($max_depth);
		$max_depth = !empty($max_depth) ? $max_depth : 5;

		// Do not go deeper, return data as is
		if ($depth > $max_depth)
		{
			return $data;
		}

		// Remove empty elements
		foreach ($data as $key => $value)
		{
			if (empty($value))
			{
				unset($data[$key]);
			}

			if (!empty($data[$key]) && is_array($data[$key]))
			{
				$data[$key] = $this->filter_empty_items($data[$key], $max_depth, $depth);
			}
		}

		// Return a copy
		return $data;
	}

	/**
	 * Enabled imgur values for output.
	 *
	 * @param string $kind (optional)
	 *
	 * @return array
	 */
	public function enabled_imgur_values($kind = '')
	{
		// Helper
		$types = explode(',', trim($this->config['imgur_enabled_output_types']));
		$types = $this->filter_empty_items($types);

		// Fallback to allowed values
		if (empty($types))
		{
			$types = $this->allowed_imgur_values('types');
			$this->config->set('imgur_enabled_output_types', implode(',', $types), false);
		}

		// Enabled options
		$enabled = [
			'types' => $types
		];

		// Value casting
		$kind = trim($kind);

		// Get specific kind
		if (!empty($kind) && !empty($enabled[$kind]))
		{
			return $enabled[$kind];
		}

		// Return whole array
		return $enabled;
	}

	/**
	 * Allowed imgur values for output.
	 *
	 * @param string $kind (optional)
	 *
	 * @return array
	 */
	public function allowed_imgur_values($kind = '')
	{
		// Allowed values
		$allowed = [
			// Output types
			'types' => ['text', 'url', 'image', 'thumbnail'],

			// Thumbnail sizes
			'sizes'	=> ['t', 'm', 'l', 'h', 's', 'b']
		];

		// Value casting
		$kind = trim($kind);

		// Get specific kind
		if (!empty($kind) && !empty($allowed[$kind]))
		{
			return $allowed[$kind];
		}

		// Return whole array
		return $allowed;
	}
}
