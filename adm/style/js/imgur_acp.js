/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

(function($) {
	'use strict';
	$('#imgur-auth-url').on('click', function($event) {
		$event.preventDefault();
		popup($(this).attr('href'), 400, 240, '_imgurauth');
	});
})(jQuery);
