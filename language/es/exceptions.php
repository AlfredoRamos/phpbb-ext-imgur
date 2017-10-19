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
	'EXCEPTION_IMGUR_NO_API_DATA' => '<em>Client ID</em> y <em>Client Secret</em> son obligatorios.',
	'EXCEPTION_IMGUR_AJAX_ONLY' => 'Esta ruta sólo puede ser usada en llamadas AJAX.',
	'EXCEPTION_IMGUR_BAD_REQUEST' => '%s'
]);
