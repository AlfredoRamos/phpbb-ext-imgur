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
	'EXCEPTION_IMGUR_NO_API_DATA' => 'Identyfikator klienta ("Client ID") i klucz klienta ("Client Secret") są obowiązkowe.',
	'EXCEPTION_IMGUR_AJAX_ONLY' => 'Ta ścieżka może być użyta tylko w wersji AJAX.',
	'EXCEPTION_IMGUR_UNAUTHORIZED' => 'Aplikacja Imgur jeszcze nie została autoryzowana.',
	'EXCEPTION_IMGUR_EMPTY_ALBUM' => 'Numer albumu jest pusty.',
	'EXCEPTION_IMGUR_NO_ALBUMS' => 'Nie można uzyskać numeru albumu: %s.',

	'UPLOAD_ERR_INI_SIZE' => 'Ładowane pliki przekraczają <samp>upload_max_filesize</samp>, określoną w dyrektywie w pliku <samp>php.ini</samp>',
	'UPLOAD_ERR_FORM_SIZE' => 'Ładowany plik przekracza dopuszczalną wielkość, określoną w dyrektywie <samp>MAX_FILE_SIZE</samp>, określoną w formularzu HTML',
	'UPLOAD_ERR_PARTIAL' => 'Uwaga: Przesyłany plik został przesłany tylko częściowo!',
	'UPLOAD_ERR_NO_FILE' => 'Nie przesłano żadnego pliku',
	'UPLOAD_ERR_NO_TMP_DIR' => 'Brak folderu tymczasowego',
	'UPLOAD_ERR_CANT_WRITE' => 'Nie udało się zapisać pliku na dysku',
	'UPLOAD_ERR_EXTENSION' => 'Rozszerzenie PHP zatrzymało wysyłanie pliku',

	'ALBUM_ERR_INVALID_ID' => 'Nieprawidłowy nemer albumu.',

	'IMGUR_AUTHORIZATION' => 'Autoryzacja',
	'IMGUR_AUTHORIZATION_EXPLAIN' => 'Trwa autoryzacja Imgur, proszę czekać.',
	'IMGUR_AUTHORIZED' => 'Właśnie autoryzowałeś aplikację Imgur, możesz zamknąć to okno.'
]);
