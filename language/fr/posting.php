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
	'IMGUR_PANEL_DROP_ZONE_EXPLAIN' => 'Faites glisser et déposez les images ici pour commencer le téléchargement ou cliquez sur le bouton ci-dessus pour sélectionner les images manuellement.',
	'IMGUR_PANEL_BUTTON_EXPLAIN' => 'Les images envoyées ne seront pas ajoutées dans le message afin de sélectionner seulement celles souhaitées.',

	'IMGUR_INVALID_MIME_TYPE' => 'Le type MIME <code>{type}</code> de l‘image <samp>{file}</samp> n‘est pas autorisé.',
	'IMGUR_IMAGE_TOO_BIG' => 'Le fichier image « <samp>{file}</samp> » ayant un poids de « <code>{size}</code> » devrait peser moins de <code>{max_size}</code>.',
	'IMGUR_NO_IMAGES' => 'Il n’y a aucune images à envoyer.',
	'IMGUR_UPLOAD_PROGRESS' => '{percentage}% ({loaded} / {total})',
	'IMGUR_EMPTY_RESPONSE' => 'La demande a retourné de manière inattendue une réponse vide.'
]);
