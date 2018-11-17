/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

(function($) {
	'use strict';

	var $formData = new FormData();
	var $queryString = location.hash.substring(1);
	var $regexp = /([^&=]+)=([^&]*)/g;
	var $match = null;
	var $imgurAuthorize = $('#imgur-authorize').first();
	var $errors = [];

	// Check if is already authorized
	if (parseInt($imgurAuthorize.attr('data-ajax-authorized')) === 1) {
		return;
	}

	// Add form data
	while ($match = $regexp.exec($queryString)) {
		$formData.append([decodeURIComponent($match[1])], decodeURIComponent($match[2]));
	}

	// Execute AJAX call
	$.ajax({
		url: $imgurAuthorize.attr('data-ajax-action'),
		type: 'POST',
		data: $formData,
		contentType: false,
		cache: false,
		processData: false
	}).done(function($data) {
		console.log($data);
		try {
			// Empty response
			if ($data.length <= 0) {
			}
		} catch (ex) {
			$errors.push(ex.message);
		}

		showImgurErrors($errors);
	}).fail(function($data, $textStatus, $error) {
		console.log($data);
		// Parse JSON response
		try {
			$responseBody = $.parseJSON($data.responseText);

			if ($.isArray($responseBody)) {
				for (var $i = 0; $i < $responseBody.length; $i++) {
					$errors.push($responseBody[$i].message);
				}
			} else {
				$errors.push($responseBody.message);
			}
		} catch (ex) {
			$errors.push(ex.message);
		}

		// Failure error message
		$errors.push($error);

		showImgurErrors($errors);
	}).then(function() {
		showImgurErrors($errors);
	}).always(function() {
		$errors = [];
		$formData = new FormData();
	});
})(jQuery);
