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
	'IMGUR_BUTTON_EXPLAIN' => 'Prześlij wybrane obrazy do Imgur i opublikuj je w poście',

	'IMGUR_OUTPUT_TEXT' => 'Tekst',
	'IMGUR_OUTPUT_URL' => 'Adres URL',
	'IMGUR_OUTPUT_IMAGE' => 'Obraz',
	'IMGUR_OUTPUT_THUMBNAIL' => 'Miniatura',

	'IMGUR_TAB' => 'Imgur',
	'IMGUR_UPLOAD' => 'Prześlij do Imgur',
	'IMGUR_ADD_TO_POST' => 'Dodaj do posta',
	'IMGUR_PANEL_DROP_ZONE_EXPLAIN' => 'Przeciągnij i upuść obrazy tutaj, aby rozpocząć przesyłanie lub kliknij przycisk powyżej, aby wybrać obrazy ręcznie.',
	'IMGUR_PANEL_BUTTON_EXPLAIN' => 'Przesłane obrazy nie zostaną dodane do wiadomości, aby móc wybrać tylko te, które chcesz.',

	'IMGUR_INVALID_MIME_TYPE' => 'Typ MIME <code>{type}</code> obrazu <samp>{file}</samp> jest niedozwolony.',
	'IMGUR_IMAGE_TOO_BIG' => 'Obraz <samp>{file}</samp> jest za duży <code>{size}</code> i powinien być mniejszy niż <code>{max_size}</code>.',
	'IMGUR_NO_IMAGES' => 'Brak zdjęć do przesłania.',
	'IMGUR_UPLOAD_PROGRESS' => '{percentage}% ({loaded} / {total})',
	'IMGUR_EMPTY_RESPONSE' => 'Żądanie nieoczekiwanie zwróciło pustą odpowiedź.'
]);
