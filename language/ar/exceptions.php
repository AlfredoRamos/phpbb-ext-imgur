<?php

/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GNU GPL-2.0
 *
 * Translated By : Bassel Taha Alhitary - www.alhitary.net
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
	'EXCEPTION_IMGUR_NO_API_DATA' => 'كتابة رقم التعريف ID وكلمة السر إجباري.',
	'EXCEPTION_IMGUR_AJAX_ONLY' => 'يُمكن استخدام هذا المسار فقط عند الحاجة إلى الأجاكس AJAX.',
	'EXCEPTION_IMGUR_BAD_REQUEST' => '%s'
]);
