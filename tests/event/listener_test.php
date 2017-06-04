<?php

/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GNU GPL-2.0
 */

namespace alfredoramos\imgur\tests\event;

use phpbb_test_case;
use phpbb\config\config;
use phpbb\template\template;
use phpbb\routing\helper as routing_helper;
use alfredoramos\imgur\event\listener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener_test extends phpbb_test_case
{

	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\routing\helper $routing_helper */
	protected $routing_helper;

	public function setUp()
	{
		parent::setUp();

		$this->config = $this->getMockBuilder(config::class)
			->disableOriginalConstructor()->getMock();
		$this->template = $this->getMockBuilder(template::class)->getMock();
		$this->routing_helper = $this->getMockBuilder(routing_helper::class)
			->disableOriginalConstructor()->getMock();
	}

	public function test_instance()
	{
		$this->assertInstanceOf(
			EventSubscriberInterface::class,
			new listener($this->config, $this->template, $this->routing_helper)
		);
	}

	public function test_suscribed_events()
	{
		$this->assertSame(
			[
				'core.user_setup',
				'core.user_setup_after'
			],
			array_keys(listener::getSubscribedEvents())
		);
	}

}
