<?php

/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\imgur\tests\functional;

trait functional_test_case_trait
{
	protected $db;

	static protected function setup_extensions()
	{
		return ['alfredoramos/imgur'];
	}

	protected function init_imgur_api()
	{
		$db = $this->get_db();
		$sql = 'UPDATE ' . CONFIG_TABLE . '
			SET  ' . $db->sql_build_array(
			'UPDATE',
			['config_value' => 'invalid_access_token']
		) . '
			WHERE ' . $db->sql_build_array(
			'UPDATE',
			['config_name' => 'imgur_access_token']
		);
		$db->sql_query($sql);
		$db->sql_close();
	}
}
