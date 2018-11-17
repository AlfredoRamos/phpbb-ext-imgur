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
	'IMGUR_BUTTON_EXPLAIN' => 'Upload the selected images to Imgur and post its content',

	'IMGUR_IMAGE_TOO_BIG' => 'The image <samp>{file}</samp> is <code>{size}</code> MiB and it should be less that <code>{max_size}</code> MiB.',
	'IMGUR_NO_IMAGES' => 'There are no images to upload.',
	'IMGUR_UPLOAD_PROGRESS' => '{percentage}% ({loaded} / {total} MiB)',
]);
