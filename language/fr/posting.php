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
	'IMGUR_BUTTON_EXPLAIN' => 'Envoyer les images sélectionnées vers le service Imgur et les insérer comme contenu dans le message',

	'IMGUR_OUTPUT_TEXT' => 'Texte',
	'IMGUR_OUTPUT_URL' => 'Adresse URL',
	'IMGUR_OUTPUT_IMAGE' => 'Image',
	'IMGUR_OUTPUT_THUMBNAIL' => 'Miniature',

	'IMGUR_TAB' => 'Imgur',
	'IMGUR_UPLOAD' => 'Envoyer des images via Imgur',
	'IMGUR_ADD_TO_POST' => 'Insérer dans le message',
	'IMGUR_PANEL_BUTTON_EXPLAIN' => 'Les images envoyées ne seront pas ajoutées dans le message afin de sélectionner seulement celles souhaitées.',

	'IMGUR_INVALID_MIME_TYPE' => 'The MIME type <code>{type}</code> of the image <samp>{file}</samp> is not allowed.',
	'IMGUR_IMAGE_TOO_BIG' => 'The image <samp>{file}</samp> is <code>{size}</code> and it should be less than <code>{max_size}</code>.',
	'IMGUR_NO_IMAGES' => 'Il n’y a aucune images à envoyer.',
	'IMGUR_UPLOAD_PROGRESS' => '{percentage}% ({loaded} / {total})',
	'IMGUR_EMPTY_RESPONSE' => 'The request unexpectedly returned an empty response.'
]);
