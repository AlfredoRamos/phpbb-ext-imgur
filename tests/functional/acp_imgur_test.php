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
class acp_imgur_test extends abstract_functional_test_case
{
	public function setUp()
	{
		parent::setUp();

		$this->add_lang_ext('alfredoramos/imgur', 'acp/info_acp_settings');

		$this->admin_login();
	}

	public function test_acp_form_settings()
	{
		$crawler = self::request('GET', sprintf(
			'adm/index.php?i=-alfredoramos-imgur-acp-main_module&mode=settings&sid=%s',
			$this->sid
		));

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();

		$this->assertSame(1, $crawler->filter('#imgur_settings')->count());

		$this->assertTrue($form->has('imgur_client_id'));
		$this->assertSame('', $form->get('imgur_client_id')->getValue());

		$this->assertTrue($form->has('imgur_client_secret'));
		$this->assertSame('', $form->get('imgur_client_secret')->getValue());

		$this->assertTrue($form->has('imgur_album'));
		$this->assertSame('', $form->get('imgur_album')->getValue());
	}

	public function test_acp_form_output()
	{
		$crawler = self::request('GET', sprintf(
			'adm/index.php?i=-alfredoramos-imgur-acp-main_module&mode=output&sid=%s',
			$this->sid
		));

		$allowed = [
			'types' => ['text', 'url', 'image', 'thumbnail'],
			'sizes' => ['t', 'm', 'l', 'h', 's', 'b']
		];

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();

		$this->assertSame(1, $crawler->filter('#imgur_output_settings')->count());

		$this->assertTrue($form->has('imgur_enabled_output_types'));
		$this->assertSame(4, count($form->get('imgur_enabled_output_types')));

		foreach ($allowed['types'] as $key => $value)
		{
			$selector = sprintf('#imgur_output_settings #imgur_output_type_%s', $value);
			$this->assertSame(1, $crawler->filter($selector)->count());
		}

		$this->assertTrue($form->has('imgur_output_type'));
		$this->assertSame('image', $form->get('imgur_output_type')->getValue());

		$this->assertTrue($form->has('imgur_thumbnail_size'));
		$this->assertSame('t', $form->get('imgur_thumbnail_size')->getValue());
	}
}
