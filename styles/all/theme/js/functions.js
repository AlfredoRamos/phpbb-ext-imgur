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
	phpbb.alert(imgur.lang.error, message);
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
	if (!helper.enabled) {
		return null;
	}

	let image = document.body.querySelector('#imgur-image');

	let output = {
		type: {
			default: (image ? image.getAttribute('data-output-type').trim() : 'image'),
			current: window.localStorage.getItem(helper.local),
			allowed: imgur.config.types.split(',')
		}
	};

	// Fallback to default
	if (output.type.current === 'null' || output.type.current === null) {
		output.type.current = output.type.default;
	}

	// Must be allowed
	if (output.type.allowed.length > 0 && output.type.allowed.indexOf(output.type.current) < 0) {
		// Try image first
		let index = output.type.allowed.indexOf('image');

		// Fallback to first available
		index = (index < 0) ? 0 : index;

		// Update current value
		output.type.current = output.type.allowed[index];
		window.localStorage.setItem(helper.local, output.type.current);
	}

	return output.type.current;
}
