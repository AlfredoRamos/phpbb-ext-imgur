/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

(function($) {
	'use strict';

	// Container
	var $imgurAuthorize = $('#imgur-authorize').first();

	// Additional check if is already authorized
	// just in case, for some reason, user got this far
	if (parseInt($imgurAuthorize.attr('data-ajax-authorized')) === 1) {
		return;
	}

	// Helper variables
	var $formData = new FormData();
	var $queryString = location.hash.substring(1);
	var $regexp = /([^&=]+)=([^&]*)/g;
	var $match = null;
	var $responseBody = {};
	var $errors = [];

	// Add form data
	do {
		$match = $regexp.exec($queryString);

		// No more matches
		if (!$match) {
			break;
		}

		$formData.set([decodeURIComponent($match[1])], decodeURIComponent($match[2]));
	} while ($match);

	// Check if form data is empty
	if ($formData.entries().next().done) {
		return;
	}

	// Execute AJAX call
	$.ajax({
		url: $imgurAuthorize.attr('data-ajax-action'),
		type: 'POST',
		data: $formData,
		contentType: false,
		cache: false,
		processData: false
	}).done(function($data, $textStatus, $jqXHR) {
		try {
			// Redirect or show message
			if ($jqXHR.readyState === 4 && $jqXHR.status === 200) {
				if (window.opener != null) {
					// Refresh ACP page
					window.opener.location.reload(true);

					// Close current window
					window.close();
				}
			} else {
				$responseBody = $.parseJSON($jqXHR.responseText);
				$errors.push($responseBody.message);
			}
		} catch (ex) {
			$errors.push(ex.message);
		}

		showImgurErrors($errors);
	}).fail(function($data, $textStatus, $error) {
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
	}).always(function() {
		$formData = new FormData();
		$responseBody = {};
		$errors = [];
	});
})(jQuery);
