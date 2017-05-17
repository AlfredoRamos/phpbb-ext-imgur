/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GNU GPL-2.0
 */

(function($) {
	'use strict';

	// Show image selection window
	$('#imgur-button').on('click', function() {
		$('#imgur-image').trigger('click');
	});

	// Upload images
	$('#imgur-image').on('change', function() {
		phpbb.clearLoadingTimeout();

		var $formData = new FormData();
		var $files = $(this).prop('files');
		var $contentBody = {
			message: $('#postingbox #message'),
			signature: $('#postform #signature')
		};
		var $imgurButton = $(this);
		var $loadingIndicator;

		// Exit if there's no images to upload
		if ($files.length <= 0) {
			return;
		}

		// Get images
		for (var i = 0; i < $files.length; i++) {
			$formData.append('imgur_image[]', $files[i]);
		}

		// Prevent button spamming
		$imgurButton.prop('disabled', true);
		$loadingIndicator = phpbb.loadingIndicator();

		// Upload the image(s)
		$.ajax({
			url: $(this).attr('data-ajax-action'),
			type: 'POST',
			data: $formData,
			contentType: false,
			cache: false,
			processData: false
		}).done(function($data) {
			$.each($data, function($key, $value) {
				var $bbcode = '';

				// BBCode data
				$bbcode = '[img]' + $value.link.replace('http://', 'https://') + '[/img]';

				// Add BBCode to content
				for (var $key in $contentBody) {
					if ($contentBody.hasOwnProperty($key)) {
						if ($contentBody[$key].length > 0) {
							$contentBody[$key].val($contentBody[$key].val() + $bbcode);
						}
					}
				}
			});
		}).fail(function($data, $textStatus, $error) {
			// Show a phpBB alert with the error
			phpbb.alert($textStatus, $error);
		}).always(function() {
			// Re-enable button
			$imgurButton.prop('disabled', false);

			// Hide loading indicator
			if ($loadingIndicator && $loadingIndicator.is(':visible')) {
				$loadingIndicator.fadeOut(phpbb.alertTime);
			}
		});

	});

})(jQuery);
