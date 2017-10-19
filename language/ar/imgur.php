<?php

/**
 * Imgur Extension for phpBB.
 * @author Bassel Taha Alhitary <http://www.alhitary.net>
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
	'IMGUR_BUTTON_EXPLAIN' => 'حدد الصور التي تريد رفعها إلى مركز الرفع : Imgur وسيتم كتابة روابطها تلقائياً'
]);
