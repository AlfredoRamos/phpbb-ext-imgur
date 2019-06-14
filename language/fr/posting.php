<?php
/**
 *
 * Imgur. An extension for the phpBB Forum Software package.
 * French translation by Galixte (http://www.galixte.com)
 *
 * @copyright (c) 2017-2019 Alfredo Ramos <alfredo.ramos@yandex.com>
 * @license GNU General Public License, version 2 (GPL-2.0-only)
 *
 */

/**
 * DO NOT CHANGE
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ « » “ ” …
//

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

	'IMGUR_IMAGE_TOO_BIG' => 'Le fichier image « <samp>{file}</samp> » ayant un poids de <code>{size}</code> » Mio devrait peser moins de <code>{max_size}</code> Mio.',
	'IMGUR_NO_IMAGES' => 'Il n’y a aucune images à envoyer.',
	'IMGUR_UPLOAD_PROGRESS' => '{percentage}% ({loaded} / {total} Mio)',
]);
