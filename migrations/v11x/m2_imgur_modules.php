<?php

/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0
 */

namespace alfredoramos\imgur\migrations\v11x;

use phpbb\db\migration\migration;

class m2_imgur_modules extends migration
{

	/**
	 * Migration dependencies.
	 *
	 * @return array
	 */
	static public function depends_on()
	{
		return ['\alfredoramos\imgur\migrations\v10x\m2_imgur_modules'];
	}

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
					'ACP_IMGUR',
					[
						'module_basename' => '\alfredoramos\imgur\acp\main_module',
						'modes'	=> ['output']
					]
				]
			]
		];
	}

}
