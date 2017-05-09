<?php

/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GNU GPL-2.0
 */

namespace alfredoramos\imgur\acp;
use Exception;

class main_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	protected $config;
	protected $template;
	protected $request;
	protected $language;
	protected $imgur;

	public function __construct()
	{
		global $phpbb_container;

		$this->config = $phpbb_container->get('config');
		$this->template = $phpbb_container->get('template');
		$this->request = $phpbb_container->get('request');
		$this->language = $phpbb_container->get('language');
		$this->imgur = $phpbb_container->get('j0k3r.imgur_api.imgur_client');
	}

	public function main($id, $mode)
	{
		$this->tpl_name = 'acp_imgur_settings';
		$this->page_title = $this->language->lang('ACP_IMGUR');
		add_form_key('alfredoramos_imgur');

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
				trigger_error('FORM_INVALID');
			}

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
				catch (Exception $ex)
				{
					trigger_error(
						$ex->getMessage() . adm_back_link($this->u_action),
						E_USER_WARNING
					);
				}

				foreach ($this->imgur->getAccessToken() as $key => $value)
				{
					$this->config->set( sprintf('imgur_%s', $key), $value, false);
				}
			}

			// Confirm dialog
			trigger_error(
				$this->language->lang('ACP_IMGUR_SETTINGS_SAVED') .
				adm_back_link($this->u_action)
			);
		}

		// Assign template variables
		$this->template->assign_vars([
			'U_ACTION'	=> $this->u_action,
			'IMGUR_CLIENT_ID' => $this->config['imgur_client_id'],
			'IMGUR_CLIENT_SECRET' => $this->config['imgur_client_secret'],
			'IMGUR_ALBUM' => $this->config['imgur_album']
		]);

		if (!empty($this->config['imgur_client_id']) && !empty($this->config['imgur_client_secret']))
		{
			$this->template->assign_var(
				'IMGUR_AUTH_URL',
				$this->imgur->getAuthenticationUrl('pin')
			);
		}
	}
}
