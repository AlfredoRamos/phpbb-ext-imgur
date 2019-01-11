/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

(function($) {
	'use strict';

	// Authentication window
	$('#imgur-authorize-url').on('click', function($event) {
		$event.preventDefault();
		popup($(this).attr('href'), 760, 570, '_imgurauth');
	});

	// Show/hide client secret
	$('#toggle-client-secret').on('click', function() {
		var $elements = {
			clientSecret: $('#imgur_client_secret'),
			icon: $(this).children('.icon').first()
		};

		if ($elements.clientSecret.prop('type') === 'password') {
			$elements.clientSecret.prop('type', 'text');
			$elements.icon.removeClass('fa-eye').addClass('fa-eye-slash');
		} else {
			$elements.clientSecret.prop('type', 'password');
			$elements.icon.removeClass('fa-eye-slash').addClass('fa-eye');
		}
	});
})(jQuery);
