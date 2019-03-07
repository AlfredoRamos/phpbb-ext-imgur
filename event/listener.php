<?php

/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\imgur\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use alfredoramos\imgur\includes\helper;

class listener implements EventSubscriberInterface
{
	/** @var \alfredoramos\imgur\includes\helper */
	protected $helper;

	/**
	 * Listener constructor.
	 *
	 * @param \alfredoramos\imgur\includes\helper $helper;
	 *
	 * @return void
	 */
	public function __construct(helper $helper)
	{
		$this->helper = $helper;
	}

	/**
	 * Assign functions defined in this class to event listeners in the core.
	 *
	 * @return array
	 */
	static public function getSubscribedEvents()
	{
		return [
			'core.user_setup'		=> 'user_setup',
			'core.user_setup_after'	=> 'user_setup_after'
		];
	}

	/**
	 * Load language files.
	 *
	 * @param object $event
	 *
	 * @return void
	 */
	public function user_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = [
			'ext_name'	=> 'alfredoramos/imgur',
			'lang_set'	=> 'posting'
		];
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	 * Assign upload URL to a template variable.
	 *
	 * @param object $event
	 *
	 * @return void
	 */
	public function user_setup_after($event)
	{
		$this->helper->assign_template_variables();
	}
}
