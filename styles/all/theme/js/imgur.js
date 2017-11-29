/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0
 */

(function($) {
	'use strict';

	// Insert text at cursor position
	// Slightly modified version
	// https://gist.github.com/mathiasbynens/326491
	$.fn.insertAtCaret = function($text) {
		return this.each(function() {
			if (document.selection) {
				// Internet Explorer
				this.focus();
				var $selection = document.selection.createRange();
				$selection.text = $text;
				this.focus();
			} else if (this.selectionStart || this.selectionStart == '0') {
				// Modern browsers
				var $position = {
					start: this.selectionStart,
					end: this.selectionEnd,
					top: this.scrollTop
				};

				// Insert text
				this.value = this.value.substring(0, $position.start)
					+ $text
					+ this.value.substring($position.end, this.value.length);
				this.focus();

				// Update position
				this.selectionStart = $position.start + $text.length;
				this.selectionEnd = $position.start + $text.length;
				this.scrollTop = $position.top;
			} else {
				// Fallback
				this.value += $text;
				this.focus();
			}
		});
	};

	// Show image selection window
	$(document.body).on('click', '.imgur-button', function() {
		$('#imgur-image').trigger('click');
	});

	// Upload images
	$(document.body).on('change', '#imgur-image', function() {
		phpbb.clearLoadingTimeout();

		var $imgurButton = $(this);
		var $formData = new FormData();
		var $files = $imgurButton.prop('files');
		var $contentBody = {
			message: $('#postingbox #message'),
			signature: $('#postform #signature'),
			quickreply: $('#qr_postform [name="message"]')
		};
		var $loadingIndicator;

		// Exit if there are no images to upload
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
			url: $imgurButton.attr('data-ajax-action'),
			type: 'POST',
			data: $formData,
			contentType: false,
			cache: false,
			processData: false
		}).done(function($data) {
			$.each($data, function($key, $value) {
				var $bbcode = '';
				var $image = {
					link: $value.link.replace('http://', 'https://')
				};

				// Generate thumbnail
				if ($image.link.length >= 0) {
					var $ext = '.' + $image.link.split('.').pop();
					var $size = $imgurButton.attr('data-thumbnail-size') || 't';

					$image.thumbnail = $image.link.replace(
						$ext,
						$size + $ext
					);
				}

				// Generate BBCode
				switch ($imgurButton.attr('data-output-type')) {
					case 'url':
						$bbcode = '[url]' + $image.link + '[/url]';
						break;
					case 'image':
						$bbcode = '[img]' + $image.link + '[/img]';
						break;
					case 'thumbnail':
						$bbcode = '[url=' + $image.link + '][img]'
							+ $image.thumbnail + '[/img][/url]';
						break;
					default:
						// Text
						$bbcode = $image.link;
						break;
				}

				// Add BBCode to content
				for (var $k in $contentBody) {
					if ($contentBody.hasOwnProperty($k)) {
						if ($contentBody[$k].length > 0) {
							$contentBody[$k].insertAtCaret($bbcode);
						}
					}
				}
			});
		}).fail(function($data, $textStatus, $error) {
			var $responseBody = {};
			var $errors = [];

			// Parse JSON response
			try {
				$responseBody = $.parseJSON($data.responseText);
				$errors.push($responseBody.message);
			} catch (ex) {
				$errors.push(ex.message);
			}

			// Failure error message
			$errors.push($error);

			// Show a phpBB alert with the errors
			if ($errors.length > 0) {
				var $message = '';

				for (var $i = 0; $i < $errors.length; $i++) {
					$message += $errors[$i];

					if ($i < ($errors.length - 1)) {
						$message += '<br />';
					}
				}

				if ($message.length > 0) {
					phpbb.alert($textStatus, $message);
				}
			}
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
