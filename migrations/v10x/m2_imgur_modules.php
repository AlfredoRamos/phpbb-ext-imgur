<?php

/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0
 */

namespace alfredoramos\imgur\migrations\v10x;

use phpbb\db\migration\migration;

class m2_imgur_modules extends migration
{

	/**
	 * Add Imgur ACP settings.
	 *
	 * @return array
	 */
	public function update_data()
	{
		return [
			[
				'module.add',
				[
					'acp',
					'ACP_CAT_DOT_MODS',
					'ACP_IMGUR'
				]
			],
			[
				'module.add',
				[
					'acp',
					'ACP_IMGUR',
					[
						'module_basename' => '\alfredoramos\imgur\acp\main_module',
						'modes'	=> ['settings']
					]
				]
			]
		];
	}

}
