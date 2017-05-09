<?php

/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GNU GPL-2.0
 */

/**
 * @ignore
 */
if (!defined('IN_PHPBB')) {
	exit;
}

/**
 * @ignore
 */
if (empty($lang) || !is_array($lang)) {
	$lang = [];
}

$lang = array_merge($lang, [
	// ACP
	'ACP_IMGUR'	=> 'Imgur settings',
	'ACP_IMGUR_CLIENT_ID' => 'Client ID',
	'ACP_IMGUR_CLIENT_SECRET' => 'Client Secret',
	'ACP_IMGUR_ALBUM'	=> 'Album',
	'ACP_IMGUR_ALBUM_EXPLAIN' => 'Album ID where all images will be uploaded to. Leave it empty if you want all the images to be uploaded in the default location.',
	'ACP_IMGUR_AUTH_EXPLAIN' => 'Click the following link to authorize the application.',
	'ACP_IMGUR_PIN' => 'PIN',
	'ACP_IMGUR_PIN_EXPLAIN' => 'Authorization PIN needed to access to your Imgur account.',
	'ACP_IMGUR_SETTINGS_SAVED' => 'Imgur settings have been succesfully saved.',

	// Exceptions
	'EXCEPTION_IMGUR_NO_API_DATA' => 'Client ID and Client Secret are mandatory.',
]);
