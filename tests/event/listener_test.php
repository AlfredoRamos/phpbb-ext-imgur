<?php

/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\imgur\tests\event;

use alfredoramos\imgur\event\listener;
use alfredoramos\imgur\includes\helper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @group event
 */
class listener_test extends \phpbb_test_case
{
	/** @var \alfredoramos\imgur\includes\helper */
	protected $helper;

	protected function setUp(): void
	{
		parent::setUp();
		$this->helper = $this->getMockBuilder(helper::class)
			->disableOriginalConstructor()->getMock();
	}

	public function test_instance()
	{
		$this->assertInstanceOf(
			EventSubscriberInterface::class,
			new listener($this->helper)
		);
	}

	public function test_subscribed_events()
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
