<?php

/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0
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

		$this->login();
		$this->add_lang_ext('alfredoramos/imgur', 'acp/info_acp_imgur');
	}

	public function test_imgur_input()
	{
		$crawler = self::request('GET', sprintf(
			'posting.php?mode=post&f=2&sid=%s',
			$this->sid
		));

		$this->assertSame(1, $crawler->filter(
			'#postingbox #format-buttons .imgur-button'
		)->count());
		$this->assertSame(1, $crawler->filter(
			'#postingbox #imgur-image'
		)->count());
	}

	public function test_acp_form_settings()
	{
		$this->admin_login();

		$crawler = self::request('GET', sprintf(
			'adm/index.php?i=-alfredoramos-imgur-acp-main_module&mode=settings&sid=%s',
			$this->sid
		));

		$form = $crawler->selectButton('Submit')->form();

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

		$this->assertTrue($form->has('imgur_output_template'));
		$this->assertSame('', $form->get('imgur_output_template')->getValue());

		$form->setValues([
			'imgur_output_type'		=> 'custom',
			'imgur_thumbnail_size'	=> 'm',
			'imgur_output_template'	=> '[x]{INVALID}[/x]'
		]);

		$crawler = self::submit($form);

		$this->assertContainsLang(
			'ERROR_TEMPLATE_MISSING_TOKEN',
			$crawler->filter('.main')->text()
		);
	}
}
