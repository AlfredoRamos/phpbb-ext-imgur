<?php

/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0
 */

namespace alfredoramos\imgur\migrations\v10x;

use phpbb\db\migration\migration;

class m1_imgur_data extends migration
{

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
				['imgur_client_id', '', true]
			],
			[
				'config.add',
				['imgur_client_secret', '', true]
			],
			[
				'config.add',
				['imgur_access_token', '', true]
			],
			[
				'config.add',
				['imgur_expires_in', 0, true]
			],
			[
				'config.add',
				['imgur_token_type', '', true]
			],
			[
				'config.add',
				['imgur_scope', '', true]
			],
			[
				'config.add',
				['imgur_refresh_token', '', true]
			],
			[
				'config.add',
				['imgur_account_id', 0, true]
			],
			[
				'config.add',
				['imgur_account_username', '', true]
			],
			[
				'config.add',
				['imgur_created_at', 0, true]
			],
			[
				'config.add',
				['imgur_album', '', true]
			]
		];
	}

}
