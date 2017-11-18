<?php

/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0
 */

namespace alfredoramos\imgur\acp;

use Imgur\Exception\AuthException;

class main_module
{

	/** @var string $u_action */
	public $u_action;

	/** @var string $tpl_name */
	public $tpl_name;

	/** @var string $page_title */
	public $page_title;

	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\request\request $request */
	protected $request;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\user $user */
	protected $user;

	/** @var \phpbb\log\log $log */
	protected $log;

	/** @var \Imgur\Client $imgur */
	protected $imgur;


	/**
	 * ACP module constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		global $phpbb_container;

		$this->config = $phpbb_container->get('config');
		$this->template = $phpbb_container->get('template');
		$this->request = $phpbb_container->get('request');
		$this->language = $phpbb_container->get('language');
		$this->user = $phpbb_container->get('user');
		$this->log = $phpbb_container->get('log');
		$this->imgur = $phpbb_container->get('alfredoramos.imgur.j0k3r.imgur.client');
	}

	/**
	 * Main module method.
	 *
	 * @param integer	$id
	 * @param string	$mode
	 *
	 * @return void
	 */
	public function main($id, $mode)
	{
		add_form_key('alfredoramos_imgur');

		switch ($mode)
		{
			case 'settings':

				$this->tpl_name = 'acp_imgur_settings';
				$this->page_title = $this->language->lang('ACP_IMGUR');

				// Load additional language keys
				$this->language->add_lang('acp/database');

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
							adm_back_link($this->u_action),
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
						trim($this->request->variable('imgur_client_id', '')),
						false
					);

					// Client secret
					$this->config->set(
						'imgur_client_secret',
						trim($this->request->variable('imgur_client_secret', '')),
						false
					);

					// Album
					$this->config->set(
						'imgur_album',
						trim($this->request->variable('imgur_album', '')),
						false
					);

					// PIN
					$pin = trim($this->request->variable('imgur_pin', ''));

					if (!empty($pin))
					{
						try
						{
							$this->imgur->requestAccessToken($pin, 'pin');
						}
						catch (AuthException $ex)
						{
							trigger_error(
								$ex->getMessage() . adm_back_link($this->u_action),
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
						'LOG_IMGUR_DATA'
					);

					// Confirm dialog
					trigger_error(
						$this->language->lang('ACP_IMGUR_SETTINGS_SAVED') .
						adm_back_link($this->u_action)
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

			break;

			case 'output':

				$this->tpl_name = 'acp_imgur_output_settings';
				$this->page_title = $this->language->lang('ACP_IMGUR_OUTPUT');

				// Value contracts
				$contract = [
					// Output type
					'types'	=> ['url', 'image', 'thumbnail', 'custom'],

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
							adm_back_link($this->u_action),
							E_USER_WARNING
						);
					}

					// Output helper
					$output = [];

					// Output type
					$output['type'] = trim($this->request->variable('imgur_output_type', ''));
					$output['type'] = in_array($output['type'], $contract['types']) ? $output['type'] : $contract['types'][1];

					// Thumbnail size
					$output['size'] = trim($this->request->variable('imgur_thumbnail_size', ''));
					$output['size'] = in_array($output['size'], $contract['sizes']) ? $output['size'] : $contract['sizes'][0];

					// Custom output template
					$output['template'] = trim(strip_tags($this->request->variable('imgur_output_template', '')));

					// Output template must contain the '{URL}' token
					if ($output['type'] === 'custom' && !(strpos($output['template'], '{URL}') > -1))
					{
						trigger_error(
							$this->language->lang('ERROR_TEMPLATE_MISSING_TOKEN', '{URL}') .
							adm_back_link($this->u_action),
							E_USER_WARNING
						);
					}

					// Update config data
					$this->config->set('imgur_output_type', $output['type'], false);
					$this->config->set('imgur_thumbnail_size', $output['size'], false);
					$this->config->set('imgur_output_template', $output['template'], false);

					// Admin log
					$this->log->add(
						'admin',
						$this->user->data['user_id'],
						$this->user->ip,
						'LOG_IMGUR_DATA'
					);

					// Confirm dialog
					trigger_error(
						$this->language->lang('ACP_IMGUR_SETTINGS_SAVED') .
						adm_back_link($this->u_action)
					);
				}

				// Assign template variables
				$this->template->assign_vars([
					'IMGUR_OUTPUT_TYPE'		=> $this->config['imgur_output_type'],
					'IMGUR_THUMBNAIL_SIZE'	=> $this->config['imgur_thumbnail_size'],
					'IMGUR_OUTPUT_TEMPLATE'	=> $this->config['imgur_output_template']
				]);

			break;

			default:

				trigger_error(
					$this->language->lang('NO_MODE') .
					adm_back_link($this->u_action),
					E_USER_WARNING
				);

			break;
		}

		// Assign global template variables
		$this->template->assign_var('U_ACTION', $this->u_action);
	}

}
