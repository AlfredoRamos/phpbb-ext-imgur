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
use phpbb\event\dispatcher_interface as dispatcher;

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

	/** @var \phpbb\event\dispatcher_interface */
	protected $dispatcher;

	/**
	 * Helper constructor
	 *
	 * @param \phpbb\config\config				$config
	 * @param \phpbb\template\template			$template
	 * @param \phpbb\routing\helper				$routing_helper
	 * @param \phpbb\language\language			$language
	 * @param \phpbb\event\dispatcher_interface	$dispatcher
	 *
	 * @return void
	 */
	public function __construct(config $config, template $template, routing_helper $routing_helper, language $language, dispatcher $dispatcher)
	{
		$this->config = $config;
		$this->template = $template;
		$this->routing_helper = $routing_helper;
		$this->language = $language;
		$this->dispatcher = $dispatcher;
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

		// Assign global template variables
		$this->template->assign_vars([
			'IMGUR_UPLOAD_URL' => vsprintf('%1$s/%2$s', [
				$this->routing_helper->route('alfredoramos_imgur_upload'),
				generate_link_hash('imgur_upload')
			]),
			'SHOW_IMGUR_BUTTON' => !empty($this->config['imgur_access_token']),
			'IMGUR_OUTPUT_TYPE' => $this->config['imgur_output_type'],
			'IMGUR_THUMBNAIL_SIZE' => $this->config['imgur_thumbnail_size'],
			'IMGUR_ALLOWED_OUTPUT_TYPES' => implode(',', $enabled)
		]);

		// Assign enabled output types
		foreach ($enabled as $type)
		{
			$this->template->assign_block_vars('IMGUR_ENABLED_OUTPUT_TYPES', [
				'KEY' => $type,
				'NAME' => $this->language->lang(sprintf('IMGUR_OUTPUT_%s', strtoupper($type))),
				'DEFAULT' => $this->config['imgur_output_type'] === $type
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
	 * @param integer	$depth
	 * @param integer	$max_depth
	 *
	 * @return array
	 */
	public function filter_empty_items($data = [], $depth = 0, $max_depth = 5)
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
				$data[$key] = $this->filter_empty_items($data[$key], $depth);
			}
		}

		// Return a copy
		return $data;
	}

	/**
	 * Enabled imgur values for output.
	 *
	 * @param string	$kind		(optional)
	 *
	 * @return array
	 */
	public function enabled_imgur_values($kind = '')
	{
		$kind = trim($kind);

		// Enabled options
		$enabled = [
			'types' => explode(',', trim($this->config['imgur_enabled_output_types']))
		];

		// Remove empty options
		$enabled = $this->filter_empty_items($enabled);

		// Allowed options
		if (empty($enabled['types']))
		{
			$allowed = $this->allowed_imgur_values(null, false);
		}
		else
		{
			$allowed = $this->allowed_imgur_values();
		}

		// Check if there are deleted options
		// from disabled/deleted extensions
		foreach ($allowed as $key => $value)
		{
			// Administrator must not disable all options
			if (!empty($value) && empty($enabled[$key]))
			{
				$enabled[$key] = $value;
			}

			// Get difference between both arrays
			$diff = array_diff($enabled[$key], $value);

			// It doesn't have leftovers
			if (empty($diff))
			{
				continue;
			}

			// Get intersection between both arrays
			// Not using array_intersect() to use already stored values
			$same = array_filter(
				$enabled[$key],
				function($v) use ($diff)
				{
					return !in_array($v, $diff, true);
				}
			);

			// It only has deleted options
			if (empty($same))
			{
				$same = $value;
			}

			// Remove empty values
			$same = $this->filter_empty_items($same);

			// Configuration name
			// Currently only custom output types are allowed
			$name = ($key === 'types') ? 'imgur_enabled_output_types' : '';

			// Update configuration
			if (!empty($name) && !empty($same))
			{
				$this->config->set($name, implode(',', $same), false);
			}
		}

		// Fallback output type
		if (!in_array($this->config['imgur_output_type'], $enabled['types'], true))
		{
			// Try image output first, if not found use the first available
			$type = array_search('image', $enabled['types']);
			$type = ($type !== false) ? $enabled['types'][$type] : $enabled['types'][0];

			// Update fallback value
			$this->config->set('imgur_output_type', $type, false);
		}

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
	 * @param string	$kind			(optional)
	 * @param bool		$extras			(optional)
	 * @param bool		$extras_only	(optional)
	 *
	 * @return array
	 */
	public function allowed_imgur_values($kind = '', $extras = true, $extras_only = false)
	{
		// Allowed values
		$allowed = [
			// Output types
			'types' => ['text', 'url', 'image', 'thumbnail'],

			// Thumbnail sizes
			'sizes'	=> ['t', 'm', 'l', 'h', 's', 'b',]
		];

		// Value casting
		$kind = trim($kind);
		$extras = (bool) $extras;
		$extras_only = (bool) $extras_only;

		// $extras_only implies $extras
		$extras = $extras_only ? ($extras || $extras_only) : $extras;

		// Extra values
		$data = [
			'types' => [],
			'sizes' => []
		];

		/**
		 * Append allowed values.
		 *
		 * @event alfredoramos.imgur.allowed_values_append
		 *
		 * @var array	data		List of allowed values.
		 * @var bool	extras		Whether extra values will be returned.
		 * @var bool	extras_only	Wether only extra values will be returned.
		 *
		 * @since 1.3.0
		 */
		$vars = ['data', 'extras', 'extras_only'];
		extract($this->dispatcher->trigger_event('alfredoramos.imgur.allowed_values_append', compact($vars)));

		// Add extra values
		if ($extras && (!empty($data['types']) || !empty($data['sizes'])))
		{
			$allowed = array_merge_recursive($allowed, $data);
		}

		// Get only extra values
		if ($extras && $extras_only)
		{
			return $this->filter_empty_items($data);
		}

		// Get specific kind
		if (!empty($kind) && !empty($allowed[$kind]))
		{
			return $allowed[$kind];
		}

		// Return whole array
		return $allowed;
	}
}
