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
	'EXCEPTION_IMGUR_NO_API_DATA' => 'Les clés « Client ID » et « Client Secret » sont obligatoires.',
	'EXCEPTION_IMGUR_AJAX_ONLY' => 'Cette méthode de connexion ne peut être utilisée que via des appels AJAX.',

	'UPLOAD_ERR_INI_SIZE' => 'Le fichier envoyé a dépassé la valeur limite de la directive « <samp>upload_max_filesize</samp> » définie dans le fichier « <samp>php.ini</samp> »',
	'UPLOAD_ERR_FORM_SIZE' => 'Le fichier envoyé a dépassé la valeur limite de la directive « <samp>MAX_FILE_SIZE</samp> » définie dans le formulaire HTML',
	'UPLOAD_ERR_PARTIAL' => 'Le fichier envoyé a été partiellement envoyé',
	'UPLOAD_ERR_NO_FILE' => 'Aucun fichier n’a été envoyé',
	'UPLOAD_ERR_NO_TMP_DIR' => 'Il manque un répertoire temporaire',
	'UPLOAD_ERR_CANT_WRITE' => 'Échec d’écriture du fichier sur le disque',
	'UPLOAD_ERR_EXTENSION' => 'Une extension PHP a interrompu l’envoi du fichier',

	'IMGUR_AUTHORIZATION' => 'Autorisation',
	'IMGUR_AUTHORIZATION_EXPLAIN' => 'Autorisation de l’application Imgur en cours, merci de patienter.',
	'IMGUR_AUTHORIZED' => 'L’application Imgur est déjà autorisée, merci de fermer cette fenêtre.'
]);
