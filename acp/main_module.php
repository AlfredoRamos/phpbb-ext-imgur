<?php

/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0
 */

namespace alfredoramos\imgur\acp;

class main_module
{

	/** @var string */
	public $u_action;

	/** @var string */
	public $tpl_name;

	/** @var string */
	public $page_title;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \alfredoramos\imgur\controller\acp */
	protected $acp_controller;

	/**
	 * ACP module constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		global $phpbb_container;

		$this->template = $phpbb_container->get('template');
		$this->language = $phpbb_container->get('language');
		$this->acp_controller = $phpbb_container->get('alfredoramos.imgur.acp.controller');
	}

	/**
	 * Main module method.
	 *
	 * @param string $id
	 * @param string $mode
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
				$this->acp_controller->settings_mode($this->u_action);
			break;

			case 'output':
				$this->tpl_name = 'acp_imgur_output_settings';
				$this->page_title = $this->language->lang('ACP_IMGUR_OUTPUT');
				$this->acp_controller->output_mode($this->u_action);
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
