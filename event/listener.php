<?php

/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GNU GPL-2.0
 */

namespace alfredoramos\imgur\event;

use phpbb\template\template;
use phpbb\routing\helper as routing_helper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\routing\helper $routing_helper */
	protected $routing_helper;

	/**
	 * Listener constructor.
	 *
	 * @param \phpbb\template\template	$template
	 * @param \phpbb\routing\helper		$routing_helper;
	 *
	 * @return void
	 */
	public function __construct(template $template, routing_helper $routing_helper)
	{
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
		$this->template->assign_var(
			'IMGUR_UPLOAD_URL',
			$this->routing_helper->route('alfredoramos_imgur_upload')
		);
	}

}
