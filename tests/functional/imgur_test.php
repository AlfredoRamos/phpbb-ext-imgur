<?php

/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\imgur\tests\functional;

use phpbb_functional_test_case;

/**
 * @group functional
 */
class imgur_test extends phpbb_functional_test_case
{
	static protected function setup_extensions()
	{
		return ['alfredoramos/imgur'];
	}

	public function setUp()
	{
		parent::setUp();

		$db = $this->get_db();
		$sql = 'UPDATE ' . CONFIG_TABLE . '
			SET  ' . $db->sql_build_array('UPDATE',
				['config_value' => 'invalid_access_token']
			) . '
			WHERE ' . $db->sql_build_array('UPDATE',
				['config_name' => 'imgur_access_token']
			);
		$db->sql_query($sql);
		$db->sql_close();
		unset($db);

		$this->add_lang_ext('alfredoramos/imgur', 'posting');

		$this->login();
	}

	public function test_acp_form_settings()
	{
		$this->admin_login();

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
		$this->admin_login();

		$crawler = self::request('GET', sprintf(
			'adm/index.php?i=-alfredoramos-imgur-acp-main_module&mode=output&sid=%s',
			$this->sid
		));

		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();

		$this->assertSame(1, $crawler->filter('#imgur_output_settings')->count());

		$this->assertTrue($form->has('imgur_output_type'));
		$this->assertSame('image', $form->get('imgur_output_type')->getValue());

		$this->assertTrue($form->has('imgur_thumbnail_size'));
		$this->assertSame('t', $form->get('imgur_thumbnail_size')->getValue());
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

		$this->assertSame(1, $elements['upload']->count());
		$this->assertContains(
			$this->lang('IMGUR_UPLOAD'),
			$elements['upload']->text()
		);

		$this->assertSame(4, $elements['fields']->count());
	}
}
