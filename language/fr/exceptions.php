<?php

/**
 * Imgur Extension for phpBB.
 * @author panteror
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
	'EXCEPTION_IMGUR_NO_API_DATA' => 'Client ID et Client Secret sont obligatoires.',
	'EXCEPTION_IMGUR_AJAX_ONLY' => 'Cette route ne peut être utilisée que sur les appels AJAX.',
	'EXCEPTION_IMGUR_BAD_REQUEST' => '%s'
]);
