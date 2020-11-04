<?php

/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
 * @ignore
 */
if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [
	'EXCEPTION_IMGUR_NO_API_DATA' => 'Client ID and Client Secret are mandatory.',
	'EXCEPTION_IMGUR_AJAX_ONLY' => 'This route can only be used on AJAX calls.',
	'EXCEPTION_IMGUR_UNAUTHORIZED' => 'The Imgur application has not been authorized yet.',
	'EXCEPTION_IMGUR_EMPTY_ALBUM' => 'The album ID is empty.',
	'EXCEPTION_IMGUR_NO_ALBUMS' => 'Could not get album IDs: %s.',

	'UPLOAD_ERR_INI_SIZE' => 'The uploaded file exceeds the <samp>upload_max_filesize</samp> directive in <samp>php.ini</samp>',
	'UPLOAD_ERR_FORM_SIZE' => 'The uploaded file exceeds the <samp>MAX_FILE_SIZE</samp> directive that was specified in the HTML form',
	'UPLOAD_ERR_PARTIAL' => 'The uploaded file was only partially uploaded',
	'UPLOAD_ERR_NO_FILE' => 'No file was uploaded',
	'UPLOAD_ERR_NO_TMP_DIR' => 'Missing a temporary folder',
	'UPLOAD_ERR_CANT_WRITE' => 'Failed to write file to disk',
	'UPLOAD_ERR_EXTENSION' => 'A PHP extension stopped the file upload',

	'ALBUM_ERR_INVALID_ID' => 'Invalid album ID.',

	'IMGUR_AUTHORIZATION' => 'Authorization',
	'IMGUR_AUTHORIZATION_EXPLAIN' => 'Imgur authorization in progress, please wait.',
	'IMGUR_AUTHORIZED' => 'You already authorized the Imgur application, you can close this window.'
]);
