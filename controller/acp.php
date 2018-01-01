<?php

/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0
 */

namespace alfredoramos\imgur\controller;

use phpbb\config\config;
use phpbb\template\template;
use phpbb\request\request;
use phpbb\language\language;
use phpbb\user;
use phpbb\log\log;
use Imgur\Client as ImgurClient;
use Imgur\Exception\AuthException as ImgurAuthException;

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

	/**
	 * Controller constructor.
	 *
	 * @param \phpbb\config\config		$config
	 * @param \phpbb\template\template	$template
	 * @param \phpbb\request\request	$request
	 * @param \phpbb\language\language	$language
	 * @param \phpbb\user				$user
	 * @param \Imgur\Client				$imgur
	 *
	 * @return void
	 */
	public function __construct(config $config, template $template, request $request, language $language, user $user, log $log, ImgurClient $imgur)
	{
		$this->config = $config;
		$this->template = $template;
		$this->request = $request;
		$this->language = $language;
		$this->user = $user;
		$this->log = $log;
		$this->imgur = $imgur;
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

		// Load additional language keys
		$this->language->add_lang('acp/database');

		// Set Imgur API data
		if (!empty($this->config['imgur_client_id']) && !empty($this->config['imgur_client_secret']))
		{
			$this->imgur->setOption('client_id', $this->config['imgur_client_id']);
			$this->imgur->setOption('client_secret', $this->config['imgur_client_secret']);
		}

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
				'scope'				=> '',
				'refresh_token'		=> '',
				'account_id'		=> 0,
				'account_username'	=> '',
				'created_at'		=> 0
			];

			// Client ID
			$this->config->set(
				'imgur_client_id',
				$this->request->variable('imgur_client_id', ''),
				false
			);

			// Client secret
			$this->config->set(
				'imgur_client_secret',
				$this->request->variable('imgur_client_secret', ''),
				false
			);

			// Album
			$this->config->set(
				'imgur_album',
				$this->request->variable('imgur_album', ''),
				false
			);

			// PIN
			$pin = $this->request->variable('imgur_pin', '');

			if (!empty($pin))
			{
				try
				{
					$this->imgur->requestAccessToken($pin, 'pin');
				}
				catch (ImgurAuthException $ex)
				{
					trigger_error(
						$ex->getMessage() . adm_back_link($u_action),
						E_USER_WARNING
					);
				}

				// Replace token values
				$token = array_replace($token, $this->imgur->getAccessToken());
			}

			// Update token
			foreach ($token as $key => $value)
			{
				// The scope column can be null, and the configuration
				// table does not accept null values
				if ($key == 'scope')
				{
					$value = empty($value) ? '' : $value;
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
			trigger_error(
				$this->language->lang('ACP_IMGUR_SETTINGS_SAVED') .
				adm_back_link($u_action)
			);
		}

		// Assign template variables
		$this->template->assign_vars([
			'IMGUR_CLIENT_ID'		=> $this->config['imgur_client_id'],
			'IMGUR_CLIENT_SECRET'	=> $this->config['imgur_client_secret'],
			'IMGUR_ALBUM'			=> $this->config['imgur_album']
		]);

		// Show	authorization URL if the token
		// has not been generated
		if (!empty($this->config['imgur_client_id']) && empty($this->config['imgur_access_token']))
		{
			$this->template->assign_var(
				'IMGUR_AUTH_URL',
				$this->imgur->getAuthenticationUrl('pin')
			);
		}

		// Show album download URL
		if (!empty($this->config['imgur_album']))
		{
			$this->template->assign_var(
				'IMGUR_ALBUM_DOWNLOAD_URL',
				sprintf('https://imgur.com/a/%s/zip', $this->config['imgur_album'])
			);
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

		// Value contracts
		$contract = [
			// Output type
			'types'	=> ['text', 'url', 'image', 'thumbnail'],

			// Thumbnail sizes
			'sizes'	=> ['t', 'm']
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

			// Output helper
			$output = [];

			// Output type
			$output['type'] = $this->request->variable('imgur_output_type', '');
			$output['type'] = in_array($output['type'], $contract['types']) ? $output['type'] : $contract['types'][2];

			// Thumbnail size
			$output['size'] = $this->request->variable('imgur_thumbnail_size', '');
			$output['size'] = in_array($output['size'], $contract['sizes']) ? $output['size'] : $contract['sizes'][0];

			// Update config data
			$this->config->set('imgur_output_type', $output['type'], false);
			$this->config->set('imgur_thumbnail_size', $output['size'], false);

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
				$this->language->lang('ACP_IMGUR_SETTINGS_SAVED') .
				adm_back_link($u_action)
			);
		}

		// Assign template variables
		$this->template->assign_vars([
			'IMGUR_OUTPUT_TYPE'		=> $this->config['imgur_output_type'],
			'IMGUR_THUMBNAIL_SIZE'	=> $this->config['imgur_thumbnail_size']
		]);

		// Assign allowed output types
		foreach ($contract['types'] as $type)
		{
			$this->template->assign_block_vars('IMGUR_OUTPUT_TYPES', [
				'KEY' => $type,
				'NAME' => $this->language->lang(sprintf(
					'ACP_IMGUR_OUTPUT_%s',
					strtoupper($type)
				))
			]);
		}

		// Assign allowed thumbnail sizes
		foreach ($contract['sizes'] as $size)
		{
			$this->template->assign_block_vars('IMGUR_THUMBNAIL_SIZES', [
				'KEY' => $size,
				'NAME' => $this->language->lang(sprintf(
					'ACP_IMGUR_THUMBNAIL_%s',
					($size === 'm') ? 'MEDIUM' : 'SMALL'
				))
			]);
		}
	}

}
