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
	'ACP_IMGUR'	=> 'Ajustes de Imgur',
	'ACP_IMGUR_CLIENT_ID' => '<em>Client ID</em>',
	'ACP_IMGUR_CLIENT_SECRET' => '<em>Client Secret</em>',
	'ACP_IMGUR_ALBUM'	=> 'Álbum',
	'ACP_IMGUR_ALBUM_EXPLAIN' => 'Identificador del álbum en donde las imágenes serán subidas. Déjelo vacío si desea que las imágenes sean subidas en la ubicación por defecto.',
	'ACP_IMGUR_AUTH_EXPLAIN' => 'Haga clic en el siguiente enlace para autorizar la aplicación.',
	'ACP_IMGUR_PIN' => 'PIN',
	'ACP_IMGUR_PIN_EXPLAIN' => 'PIN de autorización necesario para acceder a su cuenta de Imgur.',
	'ACP_IMGUR_SETTINGS_SAVED' => 'Los ajustes de Imgur han sido guardados satisfactoriamente.'
]);
