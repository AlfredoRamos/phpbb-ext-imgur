<?php

/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\imgur\tests\functional;

use phpbb_functional_test_case;

class abstract_functional_test_case extends phpbb_functional_test_case
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
	}
}
