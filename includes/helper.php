<?php

/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\imgur\includes;

use phpbb\language\language;

class helper
{
	/** @var \phpbb\language\language */
	protected $language;

	/**
	 * Helper constructor
	 *
	 * @param \phpbb\language\language $language
	 *
	 * @return void
	 */
	public function __construct(language $language)
	{
		$this->language = $language;
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
			if (empty($value))
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
}
