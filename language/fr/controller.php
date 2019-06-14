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
	'EXCEPTION_IMGUR_NO_API_DATA' => 'Les clés « Client ID » et « Client Secret » sont obligatoires.',
	'EXCEPTION_IMGUR_AJAX_ONLY' => 'Cette méthode de connexion ne peut être utilisée que via des appels AJAX.',

	'UPLOAD_ERR_INI_SIZE' => 'Le fichier envoyé a dépassé la valeur limite de la directive « <samp>upload_max_filesize</samp> » définie dans le fichier « <samp>php.ini</samp> ».',
	'UPLOAD_ERR_FORM_SIZE' => 'Le fichier envoyé a dépassé la valeur limite de la directive « <samp>MAX_FILE_SIZE</samp> » définie dans le formulaire HTML.',
	'UPLOAD_ERR_PARTIAL' => 'Le fichier envoyé a été partiellement envoyé.',
	'UPLOAD_ERR_NO_FILE' => 'Aucun fichier n’a été envoyé.',
	'UPLOAD_ERR_NO_TMP_DIR' => 'Il manque un répertoire temporaire.',
	'UPLOAD_ERR_CANT_WRITE' => 'Échec d’écriture du fichier sur le disque.',
	'UPLOAD_ERR_EXTENSION' => 'Une extension PHP a interrompu l’envoi du fichier.',

	'IMGUR_AUTHORIZATION' => 'Autorisation',
	'IMGUR_AUTHORIZATION_EXPLAIN' => 'Autorisation de l’application Imgur en cours, merci de patienter.',
	'IMGUR_AUTHORIZED' => 'L’application Imgur est déjà autorisée, merci de fermer cette fenêtre.'
]);
