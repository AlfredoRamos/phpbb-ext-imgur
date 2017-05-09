/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GNU GPL-2.0
 */

$(function() {
	// Show image selection window
	$('#imgur-button').on('click', function() {
		$('#imgur-image').trigger('click');
	});

	// Upload images
	$('#imgur-image').on('change', function() {
		var $formData = new FormData();
		var $files = $(this).prop('files');
		var $postBody = {
			message: $('#postingbox #message'),
			signature: $('#postform #signature')
		};
		var $imgurButton = $(this);

		// Exit if there's no images to upload
		if ($files.length <= 0) {
			return;
		}

		// Get images
		for (var i = 0; i < $files.length; i++) {
			$formData.append('imgur_image[]', $files[i]);
		}

		// Prevent spamming
		$imgurButton.prop('disabled', true);

		$.ajax({
			url: 'app.php/imgur/upload',
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
				for (var $key in $postBody) {
					if ($postBody.hasOwnProperty($key)) {
						if ($postBody[$key].length > 0) {
							$postBody[$key].val($postBody[$key].val() + $bbcode);
						}
					}
				}

				// Delete hash
				console.log('[' + $value.id + '] ' + $value.deletehash);
			});
		}).fail(function($data, $textStatus, $error) {
			console.log('status: ' + $textStatus);
			console.log('error: ' + $error);
		}).always(function() {
			$imgurButton.prop('disabled', false);
		});
	});
});
