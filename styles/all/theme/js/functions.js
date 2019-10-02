/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

'use strict';

/**
 * Show errors in a modal window.
 *
 * @param array errors
 *
 * @return void
 */
function showImgurErrors(errors) {
	// Ensure settings exist
	if (typeof window.$imgur === 'undefined') {
		var $imgur = {};
	} else {
		$imgur = $.extend(true, {
			lang: {
				error: 'Error'
			}
		}, window.$imgur);
	}

	// Show a phpBB alert with the errors
	if (errors.length > 0) {
		var message = '';

		for (var i = 0; i < errors.length; i++) {
			message += errors[i];

			if (i < (errors.length - 1)) {
				message += '<br>';
			}
		}

		if (message.length > 0) {
			phpbb.alert($imgur.lang.error, message);
		}
	}
}

/**
 * Format filesize to show 3 fractional digits.
 *
 * @param float fileSize
 *
 * @return string
 */
function formatImageSize(fileSize) {
	return fileSize.toLocaleString(
		undefined,
		{
			minimumFractionDigits: 3,
			maximumFractionDigits: 3
		}
	);
}

/**
 * Fill output fields.
 *
 * @param array output
 *
 * @return void
 */
function fillOutputFields(output) {
	if (output === null || output.length <= 0) {
		return;
	}

	for (var i = 0; i < output.length; i++) {
		for (var k in output[i]) {
			if (!output[i].hasOwnProperty(k)) {
				continue;
			}

			var $field = $('[name="imgur_output_' + k + '"]').first();
			$field.val($.trim($field.val()));
			output[i][k] = $.trim(output[i][k]);

			if ($field.length > 0 && output[i][k].length > 0) {
				if ($field.val().length > 0) {
					$field.val($field.val() + '\n' + output[i][k]);
				} else {
					$field.val(output[i][k]);
				}

				$field.trigger('change');
			}
		}
	}
}
