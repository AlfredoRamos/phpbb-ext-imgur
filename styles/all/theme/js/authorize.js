/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

(function() {
	'use strict';

	// Container
	let imgurAuthorize = document.body.querySelector('#imgur-authorize');

	// Additional check if is already authorized
	// just in case, for some reason, user got this far
	if (parseInt(imgurAuthorize.getAttribute('data-ajax-authorized'), 10) === 1) {
		return;
	}

	// Helper variables
	let formData = new FormData();
	let queryString = location.hash.substring(1);
	let regexp = /([^&=]+)=([^&]*)/g;
	let match = null;
	let errors = [];

	// Add form data
	do {
		match = regexp.exec(queryString);

		// No more matches
		if (!match) {
			break;
		}

		formData.set([decodeURIComponent(match[1])], decodeURIComponent(match[2]));
	} while (match);

	// Check if form data is empty
	if (formData.entries().next().done) {
		return;
	}

	// Generate AJAX object
	const xhr = new XMLHttpRequest();

	// Success
	xhr.addEventListener('load', function(e) {
		window.imgur.handleResponse(e, errors, function() {
			if (window.opener !== null) {
				// Refresh ACP page
				window.opener.location.reload(true);

				// Close current window
				window.close();
			}
		});

		window.imgur.showErrors(errors);
	});

	// Failure
	xhr.addEventListener('error', function(e) {
		window.imgur.handleResponse(e, errors);
		window.imgur.showErrors(errors);
	});

	// Post-request
	xhr.addEventListener('loadend', function() {
		// Reset
		formData = new FormData();
		response = {};
		errors = [];
	});

	// Initialize request
	xhr.open(
		'POST',
		imgurAuthorize.getAttribute('data-ajax-action').trim(),
		true
	);

	// Additional headers
	xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhr.setRequestHeader('Cache-Control', 'no-cache');

	// Send request
	xhr.send(formData);
})();
