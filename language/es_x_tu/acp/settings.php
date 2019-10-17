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
	'ACP_IMGUR_REGISTER' => 'Registrar una nueva aplicación',
	'ACP_IMGUR_REGISTER_EXPLAIN' => 'Si no cuentas con las credenciales de la API que se piden a continuación, inicia sesión en tu cuenta de Imgur y registra una nueva aplicación con el siguiente enlace.<br><strong>Si ya cuentas con las credenciales de la API, no necesitas crear una nueva aplicación.</strong>',
	'ACP_IMGUR_AUTHORIZE' => 'Autorizar acceso',
	'ACP_IMGUR_AUTHORIZE_EXPLAIN' => 'Necesitas autorizar la aplicación para poder subir las imágenes a tu cuenta.',

	'ACP_IMGUR_API_SETTINGS' => 'Ajustes de la API',
	'ACP_IMGUR_API_SETTINGS_EXPLAIN' => '<p>Aquí puedes añadir los datos necesarios para la API de Imgur. Consulta las <a href="https://www.phpbb.com/customise/db/extension/imgur/faq"><strong>Preguntas Frecuentes</strong></a> para obtener más información. Si requieres de ayuda, por favor visita la sección de <a href="https://www.phpbb.com/customise/db/extension/imgur/support"><strong>Soporte</strong></a>.</p>',

	'ACP_IMGUR_CLIENT_ID' => '<em>Client ID</em>',
	'ACP_IMGUR_CLIENT_ID_EXPLAIN' => 'Cadena de texto compuesta por números hexadecimales con una longitud de 15 caracteres.',
	'ACP_IMGUR_CLIENT_SECRET' => '<em>Client Secret</em>',
	'ACP_IMGUR_CLIENT_SECRET_EXPLAIN' => 'Cadena de texto compuesta por números hexadecimales con una longitud de 40 caracteres.',
	'ACP_IMGUR_ALBUM' => 'Álbum',
	'ACP_IMGUR_ALBUM_EXPLAIN' => 'Cadena de texto alfanumérico con una longitud igual o mayor a 5 caracteres. Será usado para almacenar las imágenes subidas. Déjalo vacío si deseas que las imágenes sean subidas en la ubicación por defecto.',
	'ACP_IMGUR_ALBUM_DOWNLOAD' => 'Descargar copia de seguridad del álbum',

	'ACP_IMGUR_OUTPUT_TYPE' => 'Tipo de salida',
	'ACP_IMGUR_OUTPUT_TYPE_EXPLAIN' => 'Debes habilitar al menos una opción. La opción por defecto debe estar habilitada, sino se utilizará la primer opción disponible.',

	'ACP_IMGUR_OUTPUT_TYPE_BUILTIN' => 'Incluidos',
	'ACP_IMGUR_OUTPUT_TYPE_EXTRA' => 'Proporcionados por otras extensiones',

	'ACP_IMGUR_THUMBNAIL_KEEP_PROPORTIONS' => 'Tamaños de miniatura que mantienen las proporciones de la imagen',
	'ACP_IMGUR_THUMBNAIL_NOT_KEEP_PROPORTIONS' => 'Tamaños de miniatura que <u>no</u> mantienen las proporciones de la imagen',

	'ACP_IMGUR_THUMBNAIL_SIZE' => 'Tamaño de miniatura',
	'ACP_IMGUR_THUMBNAIL_SIZE_EXPLAIN' => 'Este ajuste no tendrá ningun efecto si el tipo de salida no se establece en <samp>Miniatura</samp>.',

	'ACP_IMGUR_OUTPUT_TEXT_EXPLAIN' => 'URL de imagen sin formato',
	'ACP_IMGUR_OUTPUT_URL_EXPLAIN' => '<code>[url]<var>{imagen}</var>[/url]</code>',
	'ACP_IMGUR_OUTPUT_IMAGE_EXPLAIN' => '<code>[img]<var>{imagen}</var>[/img]</code>',
	'ACP_IMGUR_OUTPUT_THUMBNAIL_EXPLAIN' => '<code>[url=<var>{imagen}</var>][img]<var>{miniatura}</var>[/img][/url]</code>',

	'ACP_IMGUR_THUMBNAIL_SMALL' => 'Pequeña',
	'ACP_IMGUR_THUMBNAIL_SMALL_EXPLAIN' => '160x160px',

	'ACP_IMGUR_THUMBNAIL_MEDIUM' => 'Mediana',
	'ACP_IMGUR_THUMBNAIL_MEDIUM_EXPLAIN' => '320x320px',

	'ACP_IMGUR_THUMBNAIL_LARGE' => 'Grande',
	'ACP_IMGUR_THUMBNAIL_LARGE_EXPLAIN' => '640x640px',

	'ACP_IMGUR_THUMBNAIL_HUGE' => 'Enorme',
	'ACP_IMGUR_THUMBNAIL_HUGE_EXPLAIN' => '1024x1024px',

	'ACP_IMGUR_THUMBNAIL_SMALL_SQUARE' => 'Cuadrado pequeño',
	'ACP_IMGUR_THUMBNAIL_SMALL_SQUARE_EXPLAIN' => '90x90px',

	'ACP_IMGUR_THUMBNAIL_BIG_SQUARE' => 'Cuadrado grande',
	'ACP_IMGUR_THUMBNAIL_BIG_SQUARE_EXPLAIN' => '160x160px',

	'ACP_IMGUR_TOGGLE_DISPLAY_FIELD' => 'Mostrar/Ocultar %s',

	'ACP_IMGUR_VALIDATE_INVALID_FIELDS' => 'Valores inválidos para los campos: %s',
	'ACP_IMGUR_VALIDATE_VALUES_NOT_ALLOWED' => 'Los valores proporcionados para <samp>%1$s</samp> no estan permitidos: <code>%2$s</code>',

	'OUTPUT_SETTINGS' => 'Ajustes de salida',
	'OUTPUT_SETTINGS_EXPLAIN' => '<p>Aquí puedes habilitar, deshabilitar y establecer por defecto algunas opciones que cambiarán la salida de las imágenes subidas. También mostrará opciones personalizadas añadidas por extensiones de terceros, sin embargo, <strong>necesitarás habilitar cada opción personalizada para poder usarla</strong>.</p>',
]);
