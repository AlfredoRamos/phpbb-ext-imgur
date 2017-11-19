/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0
 */

(function($) {
	'use strict';
	$('#imgur_output_type').on('change', function() {
		// Disable thumbnail size <select>
		$('#imgur_thumbnail_size').prop('disabled', ($(this).val() != 'thumbnail'));

		// Disable template <input>
		$('#imgur_output_template').prop('disabled', ($(this).val() != 'custom'));

		// Make template required
		$('#imgur_output_template').prop('required', ($(this).val() == 'custom'));
	});
})(jQuery);
