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
	'ACP_IMGUR_REGISTER' => 'Zarejestruj nową aplikację',
	'ACP_IMGUR_REGISTER_EXPLAIN' => 'Jeśli nie masz wymaganych danych logowania API, zaloguj się na swoje konto Imgur i zarejestruj nową aplikację za pomocą poniższego linku.<br><strong>Jeśli masz już dane uwierzytelniające API, nie musisz tworzyć nowej aplikacji.</strong>',
	'ACP_IMGUR_AUTHORIZE' => 'Autoryzuj dostęp',
	'ACP_IMGUR_AUTHORIZE_EXPLAIN' => 'Musisz autoryzować aplikację, aby przesłać zdjęcia na swoje konto.',

	'ACP_IMGUR_API_SETTINGS' => 'Ustawienia API',
	'ACP_IMGUR_API_SETTINGS_EXPLAIN' => '<p>Tutaj możesz ustawić wymagane dane API Imgur. Sprawdź <a href="https://www.phpbb.com/customise/db/extension/imgur/faq" rel="external nofollow noreferrer noopener" target="_blank"><strong>FAQ</strong></a> aby uzyskać więcej informacji. Jeśli potrzebujesz pomocy, odwiedź sekcję <a href="https://www.phpbb.com/customise/db/extension/imgur/support" rel="external nofollow noreferrer noopener" target="_blank"><strong>Support</strong></a>.</p>',

	'ACP_IMGUR_CLIENT_ID' => 'ID klienta',
	'ACP_IMGUR_CLIENT_ID_EXPLAIN' => 'Ciąg składający się z liczb szesnastkowych o długości 15 znaków.',
	'ACP_IMGUR_CLIENT_SECRET' => 'Klucz klienta',
	'ACP_IMGUR_CLIENT_SECRET_EXPLAIN' => 'Ciąg składający się z liczb szesnastkowych o długości 40 znaków.',
	'ACP_IMGUR_ALBUM' => 'Album',
	'ACP_IMGUR_ALBUM_EXPLAIN' => 'Alfanumeryczny ciąg znaków o długości równej lub większej niż 5 znaków. Będzie używany do przechowywania przesłanych obrazów. Pozostaw puste, jeśli chcesz, aby wszystkie obrazy zostały przesłane w domyślnej lokalizacji.',
	'ACP_IMGUR_ALBUM_DOWNLOAD' => 'Pobierz kopię zapasową albumu',
	'ACP_IMGUR_ALBUM_VALIDATE' => 'Zatwierdź',

	'OUTPUT_SETTINGS' => 'Ustawienia danych wyjściowych',
	'OUTPUT_SETTINGS_EXPLAIN' => '<p>Tutaj możesz włączyć, wyłączyć i ustawić jako domyślne niektóre opcje, które zmienią wyjście przesłanych obrazów.</p>',

	'ACP_IMGUR_OUTPUT_TYPE' => 'Typ wyjścia',
	'ACP_IMGUR_OUTPUT_TYPE_EXPLAIN' => 'Musisz włączyć co najmniej jedną opcję. Domyślna opcja musi być włączona, w przeciwnym razie użyje pierwszego dostępnego.',

	'ACP_IMGUR_THUMBNAIL_KEEP_PROPORTIONS' => 'Rozmiary miniatur, które zachowują proporcje obrazu',
	'ACP_IMGUR_THUMBNAIL_NOT_KEEP_PROPORTIONS' => 'Rozmiary miniatur, które <u>nie</u> zachowują proporcje obrazów',

	'ACP_IMGUR_THUMBNAIL_SIZE' => 'Rozmiar miniatury',
	'ACP_IMGUR_THUMBNAIL_SIZE_EXPLAIN' => '<samp>Wybór rozmiaru miniatury</samp> nie będzie miał żadnego efektu, jeśli typ wyjścia nie jest ustawiony na <samp>miniaturki</samp>.',

	'ACP_IMGUR_OUTPUT_TEXT_EXPLAIN' => 'Adres URL surowego obrazu',
	'ACP_IMGUR_OUTPUT_URL_EXPLAIN' => '<code>[url]<var>{obraz}</var>[/url]</code>',
	'ACP_IMGUR_OUTPUT_IMAGE_EXPLAIN' => '<code>[img]<var>{obraz}</var>[/img]</code>',
	'ACP_IMGUR_OUTPUT_THUMBNAIL_EXPLAIN' => '<code>[url=<var>{obraz}</var>][img]<var>{miniatura}</var>[/img][/url]</code>',

	'ACP_IMGUR_THUMBNAIL_SMALL' => 'Mały',
	'ACP_IMGUR_THUMBNAIL_SMALL_EXPLAIN' => '160x160px',

	'ACP_IMGUR_THUMBNAIL_MEDIUM' => 'Średni',
	'ACP_IMGUR_THUMBNAIL_MEDIUM_EXPLAIN' => '320x320px',

	'ACP_IMGUR_THUMBNAIL_LARGE' => 'Duży',
	'ACP_IMGUR_THUMBNAIL_LARGE_EXPLAIN' => '640x640px',

	'ACP_IMGUR_THUMBNAIL_HUGE' => 'Ogromny',
	'ACP_IMGUR_THUMBNAIL_HUGE_EXPLAIN' => '1024x1024px',

	'ACP_IMGUR_THUMBNAIL_SMALL_SQUARE' => 'Mały kwadrat',
	'ACP_IMGUR_THUMBNAIL_SMALL_SQUARE_EXPLAIN' => '90x90px',

	'ACP_IMGUR_THUMBNAIL_BIG_SQUARE' => 'Duży kwadrat',
	'ACP_IMGUR_THUMBNAIL_BIG_SQUARE_EXPLAIN' => '160x160px',

	'ACP_IMGUR_TOGGLE_DISPLAY_FIELD' => 'Pokaż/Ukryj %s',

	'ACP_IMGUR_VALIDATE_INVALID_FIELDS' => 'Nieprawidłowe wartości pól: <samp>%s</samp>',
	'ACP_IMGUR_VALIDATE_VALUES_NOT_ALLOWED' => 'Wartości podane dla <samp>%1$s</samp> nie są dozwolone: <code>%2$s</code>',
	'ACP_IMGUR_VALIDATE_IMGUR_ALBUM' => 'Podany album nie istnieje na tym koncie Imgur.'
]);
