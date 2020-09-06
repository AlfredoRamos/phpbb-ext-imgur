/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

(function() {
	'use strict';

	// Authentication window
	document.body.addEventListener('click', function(e) {
		let link = e.target.closest('#imgur-authorize-url');

		if (!link) {
			return;
		}

		e.preventDefault();
		popup(link.getAttribute('href').trim(), 760, 570, '_imgur_auth');
	});

	// Show/hide client secret
	document.body.addEventListener('click', function(e) {
		let toggle = e.target.closest('#toggle-client-secret');

		if (!toggle) {
			return;
		}

		let field = document.body.querySelector('#imgur-client-secret');
		let icon = toggle.querySelector('.icon');
		let isHidden = (field.getAttribute('type').trim() === 'password');

		// Toggle field type
		field.setAttribute('type', (isHidden ? 'text' : 'password'));

		// Toggle icon
		icon.classList.toggle('fa-eye-slash', isHidden);
		icon.classList.toggle('fa-eye', !isHidden);
	});
})();
