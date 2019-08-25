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
	'ACP_IMGUR' => 'Imgur',
	'ACP_IMGUR_REGISTER' => 'Registrar una nueva aplicación',
	'ACP_IMGUR_REGISTER_EXPLAIN' => 'Si no cuentas con las credenciales de la API que se piden a continuación, inicia sesión en tu cuenta de Imgur y registra una nueva aplicación con el siguiente enlace.<br><strong>Si ya cuentas con las credenciales de la API, no necesitas crear una nueva aplicación.</strong>',
	'ACP_IMGUR_AUTHORIZE' => 'Autorizar acceso',
	'ACP_IMGUR_AUTHORIZE_EXPLAIN' => 'Necesitas autorizar la aplicación para poder subir las imágenes a tu cuenta.',

	'ACP_IMGUR_API_SETTINGS' => 'Ajustes de la API',
	'ACP_IMGUR_CLIENT_ID' => '<em>Client ID</em>',
	'ACP_IMGUR_CLIENT_ID_EXPLAIN' => 'Cadena de texto compuesta por números hexadecimales con una longitud de 15 caracteres.',
	'ACP_IMGUR_CLIENT_SECRET' => '<em>Client Secret</em>',
	'ACP_IMGUR_CLIENT_SECRET_EXPLAIN' => 'Cadena de texto compuesta por números hexadecimales con una longitud de 40 caracteres.',
	'ACP_IMGUR_ALBUM' => 'Álbum',
	'ACP_IMGUR_ALBUM_EXPLAIN' => 'Cadena de texto alfanumérico con una longitud igual o mayor a 5 caracteres. Será usado para almacenar las imágenes subidas. Déjalo vacío si deseas que las imágenes sean subidas en la ubicación por defecto.',
	'ACP_IMGUR_ALBUM_DOWNLOAD' => 'Descargar copia de seguridad del álbum',

	'ACP_IMGUR_OUTPUT_TYPE' => 'Tipo de salida',

	'ACP_IMGUR_THUMBNAIL_SIZE' => 'Tamaño de miniatura',
	'ACP_IMGUR_THUMBNAIL_SIZE_EXPLAIN' => 'Este ajuste no tendrá ningun efecto si el tipo de salida no se establece en <samp>Miniatura</samp>. Los tamaños de las miniaturas son de 160x160 para <samp>Pequeña</samp> y 320x320 para <samp>Mediana</samp>, las proporciones de la imagen son mantenidas.',
	'ACP_IMGUR_THUMBNAIL_SMALL' => 'Pequeña',
	'ACP_IMGUR_THUMBNAIL_MEDIUM' => 'Mediana',

	'ACP_IMGUR_TOGGLE_DISPLAY_FIELD' => 'Mostrar/Ocultar %s',

	'ACP_IMGUR_VALIDATE_INVALID_FIELDS' => 'Valores inválidos para los campos: %s',

	'OUTPUT' => 'Salida',
	'OUTPUT_SETTINGS' => 'Ajustes de salida',

	'LOG_IMGUR_DATA' => '<strong>Datos de Imgur actualizados</strong><br>» %s'
]);
