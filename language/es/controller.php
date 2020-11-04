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
	'EXCEPTION_IMGUR_NO_API_DATA' => '<em>Client ID</em> y <em>Client Secret</em> son obligatorios.',
	'EXCEPTION_IMGUR_AJAX_ONLY' => 'Esta ruta sólo puede ser usada en llamadas AJAX.',
	'EXCEPTION_IMGUR_UNAUTHORIZED' => 'La aplicación de Imgur aún no ha sido autorizada.',
	'EXCEPTION_IMGUR_EMPTY_ALBUM' => 'El ID del álbum está vacío.',
	'EXCEPTION_IMGUR_NO_ALBUMS' => 'No se pudo obtener el ID de los álbumes: %s.',

	'UPLOAD_ERR_INI_SIZE' => 'La imagen cargada excede la directiva <samp>upload_max_filesize</samp> declarada en <samp>php.ini</samp>',
	'UPLOAD_ERR_FORM_SIZE' => 'La imagen cargada excede la directiva <samp>MAX_FILE_SIZE</samp> declarada en el formulario HTML',
	'UPLOAD_ERR_PARTIAL' => 'La imagen cargada solo fue subida parcialmente',
	'UPLOAD_ERR_NO_FILE' => 'Ningún archivo fue subido',
	'UPLOAD_ERR_NO_TMP_DIR' => 'Carpeta temporal faltante',
	'UPLOAD_ERR_CANT_WRITE' => 'Error al escribir el archivo en disco',
	'UPLOAD_ERR_EXTENSION' => 'Una extensión PHP detuvo la subida de la imagen',

	'ALBUM_ERR_INVALID_ID' => 'ID de álbum inválido.',

	'IMGUR_AUTHORIZATION' => 'Autorización',
	'IMGUR_AUTHORIZATION_EXPLAIN' => 'Autorización Imgur en progreso, por favor espere.',
	'IMGUR_AUTHORIZED' => 'Ya ha autorizado la aplicación Imgur, puede cerrar esta ventana.'
]);
