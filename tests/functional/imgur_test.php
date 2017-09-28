<?php

/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GNU GPL-2.0
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

	public function test_imgur_input()
	{
		$db = $this->get_db();
		$sql = 'UPDATE ' . CONFIG_TABLE . '
			SET config_value = "invalid_access_token"
			WHERE config_name = "imgur_access_token"';
		$db->sql_query($sql);

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
