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
	'ACP_IMGUR'	=> 'Imgur : Paramètres',
	'ACP_IMGUR_CLIENT_ID' => 'Client ID',
	'ACP_IMGUR_CLIENT_SECRET' => 'Client Secret',
	'ACP_IMGUR_ALBUM'	=> 'Album',
	'ACP_IMGUR_ALBUM_EXPLAIN' => 'ID de l\'Album où toutes les images seront téléchargées. Laissez-le vide si vous voulez que toutes les images soient téléchargées dans l\'emplacement par défaut.',
	'ACP_IMGUR_AUTH_EXPLAIN' => 'Cliquez sur le lien suivant pour autoriser l\'application.',
	'ACP_IMGUR_PIN' => 'PIN',
	'ACP_IMGUR_PIN_EXPLAIN' => 'Code PIN d\'autorisation nécessaire pour accéder à votre compte Imgur.',
	'ACP_IMGUR_SETTINGS_SAVED' => 'Les paramètres d\'Imgur ont été sauvegardés avec succès.',

	'ACP_IMGUR_OUTPUT' => 'Paramètres de sortie d\'Imgur',
	'ACP_IMGUR_OUTPUT_TYPE' => 'Type de sortie',
	'ACP_IMGUR_OUTPUT_TEXT' => 'Texte',
	'ACP_IMGUR_OUTPUT_URL' => 'URL',
	'ACP_IMGUR_OUTPUT_IMAGE' => 'Image',
	'ACP_IMGUR_OUTPUT_THUMBNAIL' => 'Miniature',
	'ACP_IMGUR_THUMBNAIL_SIZE' => 'Taille de la miniature',
	'ACP_IMGUR_THUMBNAIL_SIZE_EXPLAIN' => 'Ce paramètre n\'aura aucun effet si le type de sortie n\'est pas défini sur <samp>Miniature</samp>. La taille de la miniature est de 160x160 pour <samp>Petite</samp> et 320x320 pour <samp>Moyenne</samp>, les proportions de l\'image sont conservées.',
	'ACP_IMGUR_THUMBNAIL_SMALL' => 'Petite',
	'ACP_IMGUR_THUMBNAIL_MEDIUM' => 'Moyenne',

	'OUTPUT' => 'Sortie',

	'LOG_IMGUR_DATA' => '<strong>Les données d\'Imgur ont changé</strong>'
]);
