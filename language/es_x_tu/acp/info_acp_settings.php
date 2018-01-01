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
	'ACP_IMGUR' => 'Ajustes de Imgur',
	'ACP_IMGUR_API_SETTINGS' => 'Ajustes de la API',
	'ACP_IMGUR_CLIENT_ID' => '<em>Client ID</em>',
	'ACP_IMGUR_CLIENT_SECRET' => '<em>Client Secret</em>',
	'ACP_IMGUR_ALBUM' => 'Álbum',
	'ACP_IMGUR_ALBUM_EXPLAIN' => 'Identificador del álbum en donde las imágenes serán subidas. Déjalo vacío si deseas que las imágenes sean subidas en la ubicación por defecto.',
	'ACP_IMGUR_AUTH_EXPLAIN' => 'Haz clic en el siguiente enlace para autorizar la aplicación.',
	'ACP_IMGUR_PIN' => 'PIN',
	'ACP_IMGUR_PIN_EXPLAIN' => 'PIN de autorización necesario para acceder a tu cuenta de Imgur.',
	'ACP_IMGUR_SETTINGS_SAVED' => 'Los ajustes de Imgur han sido guardados satisfactoriamente.',

	'ACP_IMGUR_OUTPUT' => 'Ajustes de salida de contenido de Imgur',
	'ACP_IMGUR_OUTPUT_TYPE' => 'Tipo de salida',
	'ACP_IMGUR_OUTPUT_TEXT' => 'Texto',
	'ACP_IMGUR_OUTPUT_URL' => 'URL',
	'ACP_IMGUR_OUTPUT_IMAGE' => 'Imagen',
	'ACP_IMGUR_OUTPUT_THUMBNAIL' => 'Miniatura',
	'ACP_IMGUR_THUMBNAIL_SIZE' => 'Tamaño de miniatura',
	'ACP_IMGUR_THUMBNAIL_SIZE_EXPLAIN' => 'Este ajuste no tendrá ningun efecto si el tipo de salida no se establece en <samp>Miniatura</samp>. Los tamaños de las miniaturas son de 160x160 para <samp>Pequeña</samp> y 320x320 para <samp>Mediana</samp>, las proporciones de la imagen son mantenidas.',
	'ACP_IMGUR_THUMBNAIL_SMALL' => 'Pequeña',
	'ACP_IMGUR_THUMBNAIL_MEDIUM' => 'Mediana',

	'OUTPUT' => 'Salida',
	'OUTPUT_SETTINGS' => 'Ajustes de salida',

	'LOG_IMGUR_DATA' => '<strong>Datos de Imgur actualizados</strong><br />» %s'
]);
