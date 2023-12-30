/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@skiff.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

(() => {
	'use strict';

	// Authentication window
	document.body.addEventListener('click', (e) => {
		const link = e.target.closest('#imgur-authorize-url');

		if (!link) {
			return;
		}

		e.preventDefault();
		window.open(link.getAttribute('href').trim(), '_imgur_auth');
	});

	// Show/hide client secret
	document.body.addEventListener('click', (e) => {
		const toggle = e.target.closest('#toggle-client-secret');

		if (!toggle) {
			return;
		}

		const field = document.body.querySelector('#imgur-client-secret');
		const icon = toggle.querySelector('.icon');

		if (!field || !icon) {
			return;
		}

		const isHidden = field.getAttribute('type').trim() === 'password';

		// Toggle field type
		field.setAttribute('type', isHidden ? 'text' : 'password');

		// Toggle icon
		icon.classList.toggle('fa-eye-slash', isHidden);
		icon.classList.toggle('fa-eye', !isHidden);
	});

	// Validate album
	document.body.addEventListener('click', (e) => {
		const button = e.target.closest('#validate-album');

		if (!button) {
			return;
		}

		e.preventDefault();

		const field = document.body.querySelector('#imgur-album');

		if (!field) {
			return;
		}

		window.imgur.validateAlbum({ button: button, field: field });
	});
})();
