<?php

/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\imgur\controller;

use phpbb\config\config;
use phpbb\template\template;
use phpbb\request\request;
use phpbb\language\language;
use phpbb\user;
use phpbb\log\log;
use Imgur\Client as ImgurClient;
use alfredoramos\imgur\includes\helper;

class acp
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \Imgur\Client */
	protected $imgur;

	/** @var \alfredoramos\imgur\includes\helper */
	protected $helper;

	/**
	 * Controller constructor.
	 *
	 * @param \phpbb\config\config					$config
	 * @param \phpbb\template\template				$template
	 * @param \phpbb\request\request				$request
	 * @param \phpbb\language\language				$language
	 * @param \phpbb\user							$user
	 * @param \Imgur\Client							$imgur
	 * @param \alfredoramos\imgur\includes\helper	$helper
	 *
	 * @return void
	 */
	public function __construct(config $config, template $template, request $request, language $language, user $user, log $log, ImgurClient $imgur, helper $helper)
	{
		$this->config = $config;
		$this->template = $template;
		$this->request = $request;
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
		if (empty($u_action))
		{
			return;
		}

		// Set Imgur API data
		if (!empty($this->config['imgur_client_id']) && !empty($this->config['imgur_client_secret']))
		{
			$this->imgur->setOption('client_id', $this->config['imgur_client_id']);
			$this->imgur->setOption('client_secret', $this->config['imgur_client_secret']);
		}

		// Validation errors
		$errors = [];

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
					'regexp' => '#^[a-zA-Z0-9]{5,10}$#'
				]
			]
		];

		// Request form data
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('alfredoramos_imgur'))
			{
				trigger_error(
					$this->language->lang('FORM_INVALID') .
					adm_back_link($u_action),
					E_USER_WARNING
				);
			}

			// Imgur token
			$token = [
				'access_token'		=> '',
				'expires_in'		=> 0,
				'token_type'		=> '',
				'refresh_token'		=> '',
				'account_id'		=> 0,
				'account_username'	=> ''
			];

			// Form data
			$fields = [
				'imgur_client_id' => $this->request->variable('imgur_client_id', ''),
				'imgur_client_secret' => $this->request->variable('imgur_client_secret', ''),
				'imgur_album' => $this->request->variable('imgur_album', '')
			];

			// Validation check
			if ($this->helper->validate($fields, $filters, $errors))
			{
				// Save configuration
				foreach ($fields as $key => $value)
				{
					$this->config->set($key, $value, false);
				}

				// Clear token
				foreach ($token as $key => $value)
				{
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
				trigger_error(
					$this->language->lang('CONFIG_UPDATED') .
					adm_back_link($u_action)
				);
			}
		}

		// Assign template variables
		$this->template->assign_vars([
			'IMGUR_CLIENT_ID'		=> $this->config['imgur_client_id'],
			'IMGUR_CLIENT_SECRET'	=> $this->config['imgur_client_secret'],
			'IMGUR_ALBUM'			=> $this->config['imgur_album']
		]);

		// Assign register URL
		if (empty($this->config['imgur_client_id']) ||
			empty($this->config['imgur_client_secret']))
		{
			$this->template->assign_var(
				'IMGUR_REGISTER_URL',
				'https://api.imgur.com/oauth2/addclient'
			);
		}

		// Assign authorize URL
		if (!empty($this->config['imgur_client_id']) &&
			!empty($this->config['imgur_client_secret']) &&
			empty($this->config['imgur_access_token']
		))
		{
			$this->template->assign_var(
				'IMGUR_AUTHORIZE_URL',
				$this->imgur->getAuthenticationUrl()
			);
		}

		// Assign album download URL
		if (!empty($this->config['imgur_album']))
		{
			$this->template->assign_var(
				'IMGUR_ALBUM_DOWNLOAD_URL',
				sprintf('https://imgur.com/a/%s/zip', $this->config['imgur_album'])
			);
		}

		// Assign validation errors
		foreach ($errors as $key => $value)
		{
			$this->template->assign_block_vars('VALIDATION_ERRORS', [
				'MESSAGE' => $value['message']
			]);
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
		if (empty($u_action))
		{
			return;
		}

		// Load additional language keys
		$this->language->add_lang('posting', 'alfredoramos/imgur');

		// Markdown options are optional and can be deleted latter,
		// so they shouldn't be choices to set them as default values
		$contracts = $this->helper->allowed_imgur_values(null, false);

		// Validation errors
		$errors = [];

		// Field filters
		$filters = [
			'imgur_output_type' => [
				'filter' => FILTER_VALIDATE_REGEXP,
				'options' => [
					'regexp' => '#^(?:' . implode('|', $contracts['types']) . ')$#'
				]
			],
			'imgur_thumbnail_size' => [
				'filter' => FILTER_VALIDATE_REGEXP,
				'options' => [
					'regexp' => '#^[' . implode($contracts['sizes']) . ']$#'
				]
			]
		];

		// Request form data
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('alfredoramos_imgur'))
			{
				trigger_error(
					$this->language->lang('FORM_INVALID') .
					adm_back_link($u_action),
					E_USER_WARNING
				);
			}

			// Form data
			$fields = [
				'imgur_output_type' => $this->request->variable('imgur_output_type', ''),
				'imgur_thumbnail_size' => $this->request->variable('imgur_thumbnail_size', '')
			];

			// Validation check
			if ($this->helper->validate($fields, $filters, $errors))
			{
				// Save configuration
				foreach ($fields as $key => $value)
				{
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
				trigger_error(
					$this->language->lang('CONFIG_UPDATED') .
					adm_back_link($u_action)
				);
			}
		}

		// Assign template variables
		$this->template->assign_vars([
			'IMGUR_OUTPUT_TYPE'		=> $this->config['imgur_output_type'],
			'IMGUR_THUMBNAIL_SIZE'	=> $this->config['imgur_thumbnail_size']
		]);

		// Assign allowed output types
		foreach ($contracts['types'] as $type)
		{
			$this->template->assign_block_vars('IMGUR_OUTPUT_TYPES', [
				'KEY' => $type,
				'NAME' => $this->language->lang(sprintf(
					'IMGUR_OUTPUT_%s',
					strtoupper($type)
				))
			]);
		}

		// Assign allowed thumbnail sizes
		foreach ($contracts['sizes'] as $size)
		{
			$this->template->assign_block_vars('IMGUR_THUMBNAIL_SIZES', [
				'KEY' => $size,
				'NAME' => $this->language->lang(sprintf(
					'ACP_IMGUR_THUMBNAIL_%s',
					($size === 'm') ? 'MEDIUM' : 'SMALL'
				))
			]);
		}

		// Assign validation errors
		foreach ($errors as $key => $value)
		{
			$this->template->assign_block_vars('VALIDATION_ERRORS', [
				'MESSAGE' => $value['message']
			]);
		}
	}
}
