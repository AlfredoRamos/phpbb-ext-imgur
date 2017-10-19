<?php

/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0
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
	'EXCEPTION_IMGUR_BAD_REQUEST' => '%s'
]);
