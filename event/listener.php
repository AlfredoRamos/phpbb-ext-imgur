<?php

/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0
 */

namespace alfredoramos\imgur\event;

use phpbb\config\config;
use phpbb\template\template;
use phpbb\routing\helper as routing_helper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\routing\helper */
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
		$this->template->assign_vars([
			'IMGUR_UPLOAD_URL'	=> $this->routing_helper->route('alfredoramos_imgur_upload', [
				'hash' => generate_link_hash('imgur_upload')
			]),
			'SHOW_IMGUR_BUTTON'	=> !empty($this->config['imgur_access_token']),
			'IMGUR_OUTPUT_TYPE' => $this->config['imgur_output_type'],
			'IMGUR_THUMBNAIL_SIZE'	=> $this->config['imgur_thumbnail_size']
		]);
	}

}
