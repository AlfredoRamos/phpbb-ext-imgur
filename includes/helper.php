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
use phpbb\extension\manager as extension_manager;

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

	/** @var \phpbb\extension\manager */
	protected $extension_manager;

	/**
	 * Helper constructor
	 *
	 * @param \phpbb\config\config		$config
	 * @param \phpbb\template\template	$template
	 * @param \phpbb\routing\helper		$routing_helper
	 * @param \phpbb\language\language	$language
	 * @param \phpbb\extension\manager	$extension_manager
	 *
	 * @return void
	 */
	public function __construct(config $config, template $template, routing_helper $routing_helper, language $language, extension_manager $extension_manager)
	{
		$this->config = $config;
		$this->template = $template;
		$this->routing_helper = $routing_helper;
		$this->language = $language;
		$this->extension_manager = $extension_manager;
	}

	/**
	 * Assign global template variables.
	 *
	 * @return void
	 */
	public function assign_template_variables()
	{
		// Assign global template variables
		$this->template->assign_vars([
			'IMGUR_UPLOAD_URL'	=> vsprintf('%1$s/%2$s', [
				$this->routing_helper->route('alfredoramos_imgur_upload'),
				generate_link_hash('imgur_upload')
			]),
			'SHOW_IMGUR_BUTTON'	=> !empty($this->config['imgur_access_token']),
			'IMGUR_OUTPUT_TYPE' => $this->config['imgur_output_type'],
			'IMGUR_THUMBNAIL_SIZE'	=> $this->config['imgur_thumbnail_size']
		]);

		// Allowed output types
		$types = $this->allowed_imgur_values('types');

		// Assign allowed output types
		foreach ($types as $type)
		{
			$this->template->assign_block_vars('IMGUR_ALLOWED_OUTPUT_TYPES', [
				'KEY' => $type,
				'NAME' => $this->language->lang(sprintf(
					'IMGUR_OUTPUT_%s',
					strtoupper($type)
				)),
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
	 * Allowed imgur values for output.
	 *
	 * @param array $settings (optional)
	 *
	 * @return array
	 */
	public function allowed_imgur_values($key = '', $extras = true)
	{
		// Allowed values
		$data = [
			// Output types
			'types' => ['text', 'url', 'image', 'thumbnail'],

			// Thumbnail sizes
			'sizes'	=> ['t', 'm']
		];

		// Value casting
		$key = trim($key);
		$extras = (bool) $extras;

		// Add support for Markdown extension
		if ($this->extension_manager->is_enabled('alfredoramos/markdown') && $extras)
		{
			$data['types'] = array_merge($data['types'], [
				'markdown_image',
				'markdown_thumbnail'
			]);
		}

		// Get specific key
		if (!empty($key) && !empty($data[$key]))
		{
			return $data[$key];
		}

		// Return whole array
		return $data;
	}
}
