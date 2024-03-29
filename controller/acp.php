<?php

/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\imgur\controller;

use phpbb\config\config;
use phpbb\template\template;
use phpbb\request\request;
use phpbb\controller\helper as controller_helper;
use phpbb\language\language;
use phpbb\user;
use phpbb\log\log;
use Imgur\Client as ImgurClient;
use alfredoramos\imgur\includes\helper;

class acp
{
	/** @var config */
	protected $config;

	/** @var template */
	protected $template;

	/** @var request */
	protected $request;

	/** @var controller_helper */
	protected $controller_helper;

	/** @var language */
	protected $language;

	/** @var user */
	protected $user;

	/** @var log */
	protected $log;

	/** @var ImgurClient */
	protected $imgur;

	/** @var helper */
	protected $helper;

	/**
	 * Controller constructor.
	 *
	 * @param config			$config
	 * @param template			$template
	 * @param request			$request
	 * @param controller_helper	$controller_helper
	 * @param language			$language
	 * @param user				$user
	 * @param log				$log
	 * @param ImgurClient		$imgur
	 * @param includes_helper	$helper
	 *
	 * @return void
	 */
	public function __construct(config $config, template $template, request $request, controller_helper $controller_helper, language $language, user $user, log $log, ImgurClient $imgur, helper $helper)
	{
		$this->config = $config;
		$this->template = $template;
		$this->request = $request;
		$this->controller_helper = $controller_helper;
		$this->language = $language;
		$this->user = $user;
		$this->log = $log;
		$this->imgur = $imgur;
		$this->helper = $helper;
	}

	/**
	 * Settings mode page.
	 *
	 * @param string $u_action
	 *
	 * @return void
	 */
	public function settings_mode($u_action = '')
	{
		if (empty($u_action)) {
			return;
		}

		// Validation errors
		$errors = [];

		$this->imgur->setHttpClient($this->helper->get_imgur_http_client());

		// Set Imgur API data
		if (!empty($this->config['imgur_client_id']) && !empty($this->config['imgur_client_secret'])) {
			$this->imgur->setOption('client_id', $this->config['imgur_client_id']);
			$this->imgur->setOption('client_secret', $this->config['imgur_client_secret']);
		}

		// Field filters
		$filters = [
			'imgur_client_id' => [
				'filter' => FILTER_VALIDATE_REGEXP,
				'options' => [
					'regexp' => '#^[a-fA-F0-9]{15}$#'
				]
			],
			'imgur_client_secret' => [
				'filter' => FILTER_VALIDATE_REGEXP,
				'options' => [
					'regexp' => '#^[a-fA-F0-9]{40}$#'
				]
			],
			'imgur_album' => [
				'filter' => FILTER_VALIDATE_REGEXP,
				'options' => [
					'regexp' => '#^(?:[a-zA-Z0-9]{5,10})?$#'
				]
			]
		];

		// Request form data
		if ($this->request->is_set_post('submit')) {
			if (!check_form_key('alfredoramos_imgur')) {
				trigger_error($this->language->lang('FORM_INVALID') . adm_back_link($u_action), E_USER_WARNING);
			}

			// Imgur token
			$token = [
				'access_token'		=> '',
				'expires_in'		=> 0,
				'token_type'		=> '',
				'refresh_token'		=> '',
				'account_id'		=> 0,
				'account_username'	=> '',
				'scope'				=> null
			];

			// Form data
			$fields = [
				'imgur_client_id' => $this->request->variable('imgur_client_id', ''),
				'imgur_client_secret' => $this->request->variable('imgur_client_secret', ''),
				'imgur_album' => $this->request->variable('imgur_album', '')
			];

			// Validation check
			if ($this->helper->validate($fields, $filters, $errors)) {
				// Save configuration
				foreach ($fields as $key => $value) {
					$this->config->set($key, $value, false);
				}

				// Clear token
				foreach ($token as $key => $value) {
					// Scope can be NULL
					// Configuration table does not allow NULL values
					if ($key === 'scope') {
						$value = trim($value);
					}

					$this->config->set(sprintf('imgur_%s', $key), $value, false);
				}

				// Admin log
				$this->log->add(
					'admin',
					$this->user->data['user_id'],
					$this->user->ip,
					'LOG_IMGUR_DATA',
					false,
					[$this->language->lang('ACP_IMGUR_API_SETTINGS')]
				);

				// Confirm dialog
				trigger_error($this->language->lang('CONFIG_UPDATED') . adm_back_link($u_action));
			}
		}

		// Assign template variables
		$this->template->assign_vars([
			'IMGUR_CLIENT_ID'		=> $this->config['imgur_client_id'],
			'IMGUR_CLIENT_SECRET'	=> $this->config['imgur_client_secret'],
			'IMGUR_ALBUM'			=> $this->config['imgur_album']
		]);

		// Assign register URL
		if (
			empty($this->config['imgur_client_id']) ||
			empty($this->config['imgur_client_secret'])
		) {
			$this->template->assign_var('IMGUR_REGISTER_URL', 'https://api.imgur.com/oauth2/addclient');
		}

		// Assign authorize URL
		if (
			!empty($this->config['imgur_client_id']) &&
			!empty($this->config['imgur_client_secret']) &&
			empty($this->config['imgur_access_token'])
		) {
			$this->template->assign_var('IMGUR_AUTHORIZE_URL', $this->imgur->getAuthenticationUrl('token'));
		}

		// Assign album download URL
		if (!empty($this->config['imgur_album'])) {
			$this->template->assign_var(
				'IMGUR_ALBUM_DOWNLOAD_URL',
				sprintf('https://imgur.com/a/%s/zip', $this->config['imgur_album'])
			);
		}

		// Assign album validate URL
		if (
			!empty($this->config['imgur_access_token']) &&
			!empty($this->config['imgur_album'])
		) {
			$this->template->assign_var(
				'IMGUR_ALBUM_VALIDATE_URL',
				$this->controller_helper->route('alfredoramos_imgur_album', [
					'hash' => generate_link_hash('imgur_album')
				])
			);
		}

		// Assign validation errors
		foreach ($errors as $error) {
			$this->template->assign_block_vars('VALIDATION_ERRORS', ['MESSAGE' => $error['message']]);
		}
	}

	/**
	 * Output mode page.
	 *
	 * @param string $u_action
	 *
	 * @return void
	 */
	public function output_mode($u_action = '')
	{
		if (empty($u_action)) {
			return;
		}

		// Load additional language keys
		$this->language->add_lang('acp/permissions');
		$this->language->add_lang('posting', 'alfredoramos/imgur');

		// Allowed values
		$allowed = $this->helper->allowed_imgur_values();

		// Helper for thumbnails sizes
		$thumbnails = [
			// Keep image proportions
			['t', 'm', 'l', 'h'],

			// Do not keep image proportions
			['s', 'b'],
		];

		// Enabled values
		$enabled = $this->helper->enabled_imgur_values();

		// Validation errors
		$errors = [];

		// Field filters
		$filters = [
			'imgur_output_type' => [
				'filter' => FILTER_VALIDATE_REGEXP,
				'options' => [
					'regexp' => '#^(?:' . implode('|', $allowed['types']) . ')$#'
				]
			],
			'imgur_thumbnail_size' => [
				'filter' => FILTER_VALIDATE_REGEXP,
				'options' => [
					'regexp' => '#^[' . implode($allowed['sizes']) . ']$#'
				]
			]
		];

		// Request form data
		if ($this->request->is_set_post('submit')) {
			if (!check_form_key('alfredoramos_imgur')) {
				trigger_error($this->language->lang('FORM_INVALID') . adm_back_link($u_action), E_USER_WARNING);
			}

			// Form data
			$fields = [
				'imgur_output_type' => $this->request->variable('imgur_output_type', ''),
				'imgur_thumbnail_size' => $this->request->variable('imgur_thumbnail_size', ''),
				'imgur_enabled_output_types' => $this->helper->filter_empty_items(
					$this->request->variable('imgur_enabled_output_types', [0 => ''])
				)
			];

			// Update filters by given output
			if (!empty($fields['imgur_enabled_output_types'])) {
				// Data helper
				$data = [
					'field' => 'imgur_enabled_output_types',
					'contract' => 'types',
					'filter' => 'imgur_output_type'
				];

				$data['regexp'] = '#^(?:' . implode('|', $fields[$data['field']]) . ')$#';
				$data['diff'] = array_diff($fields[$data['field']], $allowed[$data['contract']]);

				// Can't set as default a disabled option
				if (!in_array($fields[$data['filter']], $fields[$data['field']], true)) {
					// Set as default the first available
					$fields[$data['filter']] = $fields[$data['field']][0];
				}

				// Enabled (input) values must be in the allowed values
				if (!empty($data['diff'])) {
					$errors[]['message'] = $this->language->lang(
						'ACP_IMGUR_VALIDATE_VALUES_NOT_ALLOWED',
						$this->language->lang('ACP_' . strtoupper($data['filter'])),
						implode(',', $data['diff'])
					);
				} else {
					// Update validation regexp
					$filters[$data['filter']]['options']['regexp'] = $data['regexp'];
				}

				// Convert enabled values (array) to string
				if (is_array($fields[$data['field']])) {
					$fields[$data['field']] = implode(',', $fields[$data['field']]);
				}
			}

			// Validation check
			if ($this->helper->validate($fields, $filters, $errors)) {
				// Save configuration
				foreach ($fields as $key => $value) {
					$this->config->set($key, $value, false);
				}

				// Admin log
				$this->log->add(
					'admin',
					$this->user->data['user_id'],
					$this->user->ip,
					'LOG_IMGUR_DATA',
					false,
					[$this->language->lang('OUTPUT_SETTINGS')]
				);

				// Confirm dialog
				trigger_error($this->language->lang('CONFIG_UPDATED') . adm_back_link($u_action));
			}
		}

		// Assign template variables
		$this->template->assign_vars([
			'IMGUR_OUTPUT_TYPE'		=> $this->config['imgur_output_type'],
			'IMGUR_THUMBNAIL_SIZE'	=> $this->config['imgur_thumbnail_size']
		]);

		// Assign allowed output types
		foreach ($allowed['types'] as $type) {
			$this->template->assign_block_vars('IMGUR_OUTPUT_TYPES', [
				'KEY' => $type,
				'NAME' => $this->language->lang(sprintf('IMGUR_OUTPUT_%s', strtoupper($type))),
				'EXPLAIN' => $this->language->lang(sprintf('ACP_IMGUR_OUTPUT_%s_EXPLAIN', strtoupper($type))),
				'ENABLED' => in_array($type, $enabled['types'], true)
			]);
		}

		// Assign allowed thumbnail sizes
		foreach ($allowed['sizes'] as $size) {
			switch ($size) {
				case 't':
					$name = 'SMALL';
					break;

				case 'm':
					$name = 'MEDIUM';
					break;

				case 'l':
					$name = 'LARGE';
					break;

				case 'h':
					$name = 'HUGE';
					break;

				case 's':
					$name = 'SMALL_SQUARE';
					break;

				case 'b':
					$name = 'BIG_SQUARE';
					break;

				default:
					$name = '';
					break;
			}

			// Invalid o not yet supported size
			if (empty($name)) {
				continue;
			}

			$this->template->assign_block_vars('IMGUR_THUMBNAIL_SIZES', [
				'KEY' => $size,
				'NAME' => $this->language->lang(sprintf('ACP_IMGUR_THUMBNAIL_%s', $name)),
				'EXPLAIN' => $this->language->lang(sprintf('ACP_IMGUR_THUMBNAIL_%s_EXPLAIN', $name)),
				'KEEP_PROPORTIONS' => in_array($size, $thumbnails[0], true)
			]);
		}

		// Assign validation errors
		foreach ($errors as $error) {
			$this->template->assign_block_vars('VALIDATION_ERRORS', ['MESSAGE' => $error['message']]);
		}
	}
}
