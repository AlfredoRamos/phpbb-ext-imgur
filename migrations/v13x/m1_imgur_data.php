<?php

/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\imgur\migrations\v13x;

use phpbb\db\migration\migration;

class m1_imgur_data extends migration
{
	/**
	 * Migration dependencies.
	 *
	 * @return array
	 */
	static public function depends_on()
	{
		return ['\alfredoramos\imgur\migrations\v12x\m1_imgur_data'];
	}

	/**
	 * Add Imgur configuration.
	 *
	 * @return array
	 */
	public function update_data()
	{
		return [
			[
				'config.add',
				['imgur_scope', '', true]
			],
			[
				'config.add',
				['imgur_enabled_output_types', '', true]
			]
		];
	}
}
