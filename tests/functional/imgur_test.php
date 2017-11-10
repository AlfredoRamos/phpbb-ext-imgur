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
	public function setUp() {
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
	}

	static protected function setup_extensions()
	{
		return ['alfredoramos/imgur'];
	}

	public function test_imgur_input()
	{
		$this->login();
		$crawler = self::request('GET', sprintf(
			'posting.php?mode=post&f=2&sid=%s',
			$this->sid
		));

		$this->assertEquals(1, $crawler->filter(
			'#postingbox #format-buttons .imgur-button'
		)->count());
		$this->assertEquals(1, $crawler->filter(
			'#postingbox #imgur-image'
		)->count());
	}
}
