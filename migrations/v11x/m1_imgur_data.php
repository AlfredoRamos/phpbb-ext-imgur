<?php

/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0
 */

namespace alfredoramos\imgur\migrations\v11x;

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
		return ['\alfredoramos\imgur\migrations\v10x\m1_imgur_data'];
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
				['imgur_output_type', 'image', true]
			],
			[
				'config.add',
				['imgur_thumbnail_size', 't', true]
			],
			[
				'config.add',
				['imgur_output_format', '', true]
			]
		];
	}

	/**
	 * Remove Imgur configuration.
	 *
	 * @return array
	 */
	public function revert_data()
	{
		return [
			[
				'config.remove',
				[
					'imgur_output_type',
					'imgur_thumbnail_size',
					'imgur_output_format'
				]
			]
		];
	}

}
