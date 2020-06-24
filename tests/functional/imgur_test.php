<?php

/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\imgur\tests\functional;

/**
 * @group functional
 */
class imgur_test extends abstract_functional_test_case
{
	public function setUp(): void
	{
		parent::setUp();

		$this->add_lang_ext('alfredoramos/imgur', 'posting');
	}

	public function test_imgur_input()
	{
		$crawler = self::request('GET', sprintf(
			'posting.php?mode=post&f=2&sid=%s',
			$this->sid
		));

		$elements = [
			'button' => $crawler->filter('#postingbox #format-buttons .imgur-button'),
			'input' => $crawler->filter('#postingbox #imgur-image'),
			'select' => $crawler->filter('#postingbox #format-buttons .imgur-output-select'),
			'tab' => $crawler->filter('#postform #imgur-panel-tab > a'),
			'dropzone' => $crawler->filter('#postform #imgur-panel #imgur-drop-zone'),
			'drophelp' => $crawler->filter('#postform #imgur-panel #imgur-drop-zone > .imgur-drop-zone-desc'),
			'upload' => $crawler->filter('#postform #imgur-panel .imgur-upload-button'),
			'fields' => $crawler->filter('#postform #imgur-panel .imgur-output-fields dl')
		];

		$this->assertSame(1, $elements['button']->count());
		$this->assertContains(
			$this->lang('IMGUR_BUTTON_EXPLAIN'),
			$elements['button']->attr('title')
		);

		$this->assertSame(1, $elements['input']->count());
		$this->assertSame(1, preg_match(
			'#^/app\.php/imgur/upload/\w+$#',
			$elements['input']->attr('data-ajax-action')
		));
		$this->assertSame('image', $elements['input']->attr('data-output-type'));
		$this->assertSame('t', $elements['input']->attr('data-thumbnail-size'));

		$this->assertSame(1, $elements['select']->count());

		$this->assertSame(1, $elements['tab']->count());
		$this->assertContains(
			$this->lang('IMGUR_TAB'),
			$elements['tab']->text()
		);

		$this->assertSame(1, $elements['dropzone']->count());
		$this->assertSame(1, $elements['drophelp']->count());

		$this->assertSame(1, $elements['upload']->count());
		$this->assertContains(
			$this->lang('IMGUR_UPLOAD'),
			$elements['upload']->text()
		);

		$this->assertSame(4, $elements['fields']->count());
	}
}
