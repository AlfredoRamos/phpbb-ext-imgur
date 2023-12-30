/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@skiff.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

(() => {
	'use strict';

	// Container
	const imgurAuthorize = document.body.querySelector('#imgur-authorize');

	// Additional check if is already authorized
	// just in case, for some reason, user got this far
	if (
		parseInt(imgurAuthorize.getAttribute('data-ajax-authorized'), 10) === 1
	) {
		return;
	}

	// Helper variables
	const queryString = location.hash.substring(1);
	const queryParams = new URLSearchParams(queryString);
	const allowedParams = [
		'access_token',
		'expires_in',
		'token_type',
		'refresh_token',
		'account_id',
		'account_username',
		'scope',
	];
	let formData = new FormData();
	let errors = [];

	// Add form data
	allowedParams.forEach((value) => {
		formData.set(value, queryParams.get(value) || '');
	});

	// Check if form data is empty
	if (formData.entries().next().done) {
		return;
	}

	// Generate AJAX object
	const xhr = new XMLHttpRequest();

	// Success
	xhr.addEventListener('load', (e) => {
		window.imgur.handleResponse(e, errors, () => {
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
	xhr.addEventListener('error', (e) => {
		window.imgur.handleResponse(e, errors);
		window.imgur.showErrors(errors);
	});

	// Post-request
	xhr.addEventListener('loadend', () => {
		window.imgur.showErrors(errors);

		// Reset
		formData = new FormData();
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
