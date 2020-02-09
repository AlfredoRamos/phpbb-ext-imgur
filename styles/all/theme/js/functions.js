/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

'use strict';

// Polyfill for NodeList.forEach()
// https://developer.mozilla.org/en-US/docs/Web/API/NodeList/forEach#Polyfill
if (!NodeList.prototype.forEach) {
	NodeList.prototype.forEach = Array.prototype.forEach;
}

// Polifyll for Array.isArray()
// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/isArray#Polyfill
if (!Array.isArray) {
	Array.isArray = function(arg) {
		return Object.prototype.toString.call(arg) === '[object Array]';
	};
}

// Polyfill for Object.entries()
// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/entries#Polyfill
if (!Object.entries) {
	Object.entries = function( obj ){
		let ownProps = Object.keys(obj);
		let i = ownProps.length;
		let resArray = new Array(i);

		while (i--) {
			resArray[i] = [ownProps[i], obj[ownProps[i]]];
		}

		return resArray;
	};
}

// Polyfill for Element.matches()
// https://developer.mozilla.org/en-US/docs/Web/API/Element/matches#Polyfill
if (!Element.prototype.matches) {
	Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
}

// Polyfill for Element.closest()
// https://developer.mozilla.org/en-US/docs/Web/API/Element/closest#Polyfill
if (!Element.prototype.closest) {
	Element.prototype.closest = function(s) {
		let el = this;

		do {
			if (el.matches(s)) {
				return el;
			}

			el = el.parentElement || el.parentNode;
		} while (el !== null && el.nodeType === 1);

		return null;
	};
}

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
