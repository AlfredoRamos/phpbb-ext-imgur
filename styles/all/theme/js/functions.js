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
	if (!Array.isArray(errors) || errors.length <= 0) {
		return;
	}

	let message = '';

	errors.forEach(function(error, index) {
		if (!error) {
			return;
		}

		message += error;

		if (index < (errors.length - 1)) {
			message += '<br>';
		}
	});

	if (message.length <= 0) {
		return;
	}

	// Show a phpBB alert with the errors
	phpbb.alert(window.imgur.lang.error, message);
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
	if (!Array.isArray(output) || output.length <= 0) {
		return;
	}

	output.forEach(function(item) {
		if (!item) {
			return;
		}

		let key = item[0];
		let value = item[1];
		let field = document.body.querySelector('[name="imgur_output_' + key + '"]');

		if (!field) {
			return;
		}

		field.value = field.value.trim();
		value = value.trim();

		if (value.length <= 0) {
			return;
		}

		field.value += (field.value.length > 0 ? '\n' : '') + value;

		let evt;

		// IE11 fix
		if (typeof Event !== 'function') {
			evt = document.createEvent('Event');
			evt.initEvent('change', true, true);
		} else {
			evt = new Event('change', {
				bubbles: true,
				cancelable: true
			});
		}

		field.dispatchEvent(evt);
	});
}

/**
 * Get allowed output type.
 *
 * @param object helper
 *
 * @return string
 */
function getOutputType(helper) {
	let image = document.body.querySelector('#imgur-image');
	let defaultType = 'image';

	if (!helper.enabled) {
		return defaultType;
	}

	let current = window.localStorage.getItem(helper.local);
	let allowed = window.imgur.config.types.split(',');

	// Fallback to default
	if (current === 'null' || current === null) {
		current = defaultType;
	}

	// Must be allowed
	if (allowed.length > 0 && allowed.indexOf(current) < 0) {
		// Try image first
		let index = allowed.indexOf('image');

		// Fallback to first available
		index = (index < 0) ? 0 : index;

		// Update current value
		current = allowed[index];
		window.localStorage.setItem(helper.local, current);
	}

	return current;
}
