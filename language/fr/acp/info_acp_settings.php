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
	'ACP_IMGUR' => 'Service Imgur',
	'ACP_IMGUR_REGISTER' => 'Enregistrer une nouvelle ',
	'ACP_IMGUR_REGISTER_EXPLAIN' => 'Permet ci-dessous de s’enregistrer auprès de l’application Imgur. Se connecter au moyen de ses identifiants du service Imgur via le bouton suivant.<br /><strong>Puis saisir les clés (identifiants) de l’API Imgur ci-dessous.</strong>',
	'ACP_IMGUR_AUTHORIZE' => 'Autoriser l’accès',
	'ACP_IMGUR_AUTHORIZE_EXPLAIN' => 'Permet d’autoriser l’application Imgur au préalable de pouvoir envoyer des images sur son compte via ce service.',

	'ACP_IMGUR_API_SETTINGS' => 'Paramètres de l’API',
	'ACP_IMGUR_CLIENT_ID' => 'Clé « Client ID »',
	'ACP_IMGUR_CLIENT_ID_EXPLAIN' => 'Permet de saisir une suite de nombres en hexadécimal comportant 15 caractères.',
	'ACP_IMGUR_CLIENT_SECRET' => 'Clé « Client Secret »',
	'ACP_IMGUR_CLIENT_SECRET_EXPLAIN' => 'Permet de saisir une suite de nombres en hexadécimal comportant 40 caractères.',
	'ACP_IMGUR_ALBUM' => 'Album',
	'ACP_IMGUR_ALBUM_EXPLAIN' => 'Permet de sdaisir une suite alphanumérique d’une longueur égale ou supérieure à 5 caractères. Utile pour personnaliser l’emplacement de stockage des images envoyées. Laisser vide pour stocker les images envoyées dans l’emplacement par défaut.',
	'ACP_IMGUR_ALBUM_DOWNLOAD' => 'Télécharger une sauvegarde de l’album',

	'ACP_IMGUR_OUTPUT_TYPE' => 'Format de sortie',

	'ACP_IMGUR_THUMBNAIL_SIZE' => 'Taille de la miniature',
	'ACP_IMGUR_THUMBNAIL_SIZE_EXPLAIN' => 'Permet de définir les dimensions de la miniature. Pour être fonctionnelle cette option nécessite que l’option « Format de sortie » soit définit sur « <samp>Miniature</samp> ». Les dimensions des miniatures sont 160 x 160 px (pixels) pour le choix « <samp>Petite</samp> » et 320 x 320 px pour le choix « <samp>Moyenne</samp> », les proportions de l’image sont conservées.',
	'ACP_IMGUR_THUMBNAIL_SMALL' => 'Petite',
	'ACP_IMGUR_THUMBNAIL_MEDIUM' => 'Moyenne',

	'ACP_IMGUR_TOGGLE_DISPLAY_FIELD' => 'Afficher / masquer la %s',

	'ACP_IMGUR_VALIDATE_INVALID_FIELDS' => 'Valeurs erronées pour les champs : %s',

	'OUTPUT' => 'Format de sortie',
	'OUTPUT_SETTINGS' => 'Paramètres du format de sortie par défaut',

	'LOG_IMGUR_DATA' => '<strong>Données du service Imgur mises à jour</strong><br />» %s'
]);
