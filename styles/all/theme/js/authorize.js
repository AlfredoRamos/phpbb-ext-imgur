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
	if (parseInt(imgurAuthorize.getAttribute('data-ajax-authorized')) === 1) {
		return;
	}

	// Helper variables
	let formData = new FormData();
	let queryString = location.hash.substring(1);
	let regexp = /([^&=]+)=([^&]*)/g;
	let match = null;
	let responseBody = {};
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
		try {
			// Redirect
			if (e.target.readyState === 4 && e.target.status === 200) {
				if (window.opener !== null) {
					// Refresh ACP page
					window.opener.location.reload(true);

					// Close current window
					window.close();
				}

				return;
			}

			// Get response
			let response = e.target.responseText;

			// Empty response
			if (response.length <= 0) {
				errors.push(imgur.lang.emptyResponse);
				return;
			}
			// Parse JSON response
			responseBody = JSON.parse(response);

			// Empty response body
			if (responseBody.length <= 0) {
				errors.push(imgur.lang.emptyResponse);
				return;
			}

			// Get error message
			if (Array.isArray(responseBody)) {
				responseBody.forEach(function(item) {
					if (!item) {
						return;
					}

					errors.push(item.message);
				});
			} else {
				errors.push(responseBody.message);
			}
		} catch (ex) {
			errors.push(ex.message);
		}

		showImgurErrors(errors);
	});

	// Failure
	xhr.addEventListener('error', function(e) {
		try {
			// Get response
			let response = e.target.responseText;

			// Empty response
			if (response.length <= 0) {
				errors.push(imgur.lang.emptyResponse);
				return;
			}

			// Parse JSON response
			responseBody = JSON.parse(response);

			// Empty response body
			if (responseBody.length <= 0) {
				errors.push(imgur.lang.emptyResponse);
				return;
			}

			// Check for errors
			if (Array.isArray(responseBody)) {
				responseBody.forEach(function(item) {
					if (!item) {
						return;
					}

					errors.push(item.message);
				});
			} else {
				errors.push(responseBody.message);
			}
		} catch (ex) {
			errors.push(ex.message);
		}

		showImgurErrors(errors);
	});

	// Post-request
	xhr.addEventListener('loadend', function() {
		// Reset
		formData = new FormData();
		responseBody = {};
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
