<?php

/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0
 */

namespace alfredoramos\imgur\acp;

class main_info
{

	/**
	 * Set up ACP module.
	 *
	 * @return array
	 */
	public function module()
	{
		return [
			'filename'	=> '\alfredoramos\imgur\acp\main_module',
			'title'		=> 'ACP_IMGUR',
			'modes'		=> [
				'settings'	=> [
					'title'	=> 'SETTINGS',
					'auth'	=> 'ext_alfredoramos/imgur && acl_a_board',
					'cat'	=> ['ACP_IMGUR']
				],
				'output'	=> [
					'title'	=> 'OUTPUT',
					'auth'	=> 'ext_alfredoramos/imgur && acl_a_board',
					'cat'	=> ['ACP_IMGUR']
				]
			]
		];
	}

}
