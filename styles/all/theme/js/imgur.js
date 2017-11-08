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
	$(document).on('click', '.imgur-button', function() {
		$('#imgur-image').trigger('click');
	});

	// Upload images
	$(document).on('change', '#imgur-image', function() {
		phpbb.clearLoadingTimeout();

		var $formData = new FormData();
		var $files = $(this).prop('files');
		var $contentBody = {
			message: $('#postingbox #message'),
			signature: $('#postform #signature'),
			quickreply: $('#qr_postform [name="message"]')
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
