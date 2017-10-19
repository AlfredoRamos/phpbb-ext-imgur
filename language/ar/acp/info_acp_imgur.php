<?php

/**
 * Imgur Extension for phpBB.
 * @author Bassel Taha Alhitary <http://www.alhitary.net>
 * @copyright 2017 Alfredo Ramos
 * @license GNU GPL-2.0
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
	'ACP_IMGUR'	=> 'خدمة ايميجر لرفع الصور',
	'ACP_IMGUR_CLIENT_ID' => 'رقم التعريف ID ',
	'ACP_IMGUR_CLIENT_SECRET' => 'كلمة السر ',
	'ACP_IMGUR_ALBUM'	=> 'رقم الألبوم',
	'ACP_IMGUR_ALBUM_EXPLAIN' => 'اكتب رقم الألبوم الذي أنشأته في حسابك Imgur لرفع جميع الصور إليه. أتركه فارغاً لو تريد رفع جميع الصور إلى الألبوم الإفتراضي.',
	'ACP_IMGUR_AUTH_EXPLAIN' => 'أنقر على الرابط التالي للمُصادقة على هذه الخدمة.',
	'ACP_IMGUR_PIN' => 'رمز التعريف الشخصي ',
	'ACP_IMGUR_PIN_EXPLAIN' => 'اكتب رقم الـ PIN للمُصادقة والوصول إلى حسابك في Imgur.',
	'ACP_IMGUR_SETTINGS_SAVED' => 'تم حفظ الإعدادات بنجاح.'
]);
