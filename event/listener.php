<?php

/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GNU GPL-2.0
 */

namespace alfredoramos\imgur\event;

use phpbb\config\config;
use phpbb\template\template;
use phpbb\routing\helper as routing_helper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{

	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\routing\helper $routing_helper */
	protected $routing_helper;

	/**
	 * Listener constructor.
	 *
	 * @param \phpbb\config\config		$config
	 * @param \phpbb\template\template	$template
	 * @param \phpbb\routing\helper		$routing_helper
	 *
	 * @return void
	 */
	public function __construct(config $config, template $template, routing_helper $routing_helper)
	{
		$this->config = $config;
		$this->template = $template;
		$this->routing_helper = $routing_helper;
	}

	/**
	 * Assign functions defined in this class to event listeners in the core.
	 *
	 * @return array
	 */
	static public function getSubscribedEvents()
	{
		return [
			'core.user_setup_after'	=> 'user_setup_after'
		];
	}

	/**
	 * Assign upload URL to a template variable.
	 *
	 * @param object	$event
	 *
	 * @return void
	 */
	public function user_setup_after($event)
	{
		$this->template->assign_vars([
			'IMGUR_UPLOAD_URL'	=> $this->routing_helper->route('alfredoramos_imgur_upload'),
			'SHOW_IMGUR_BUTTON'	=> !empty($this->config['imgur_access_token'])
		]);
	}

}
