/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
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
			message: $('[name="message"]'),
			signature: $('[name="signature"]')
		};
		var $progress = {};
		var $responseBody = {};
		var $errors = [];
		var $loadingIndicator;

		// Imgur API limit (MiB)
		var $maxFileSize = (10 * 1024 * 1024);

		// Progress objects
		$progress.wrapper = $('#imgur-progress-wrapper').first();
		$progress.bar = $progress.wrapper.children('#imgur-progress').first();
		$progress.label = $progress.wrapper.find('#imgur-progress-label > code').first();

		// Exit if there are no images to upload
		if ($files.length <= 0) {
			return;
		}

		// Prevent button spamming
		$imgurButton.prop('disabled', true);

		// Add images
		for (var $i = 0; $i < $files.length; $i++) {
			// Don't send images bigger than $maxFileSize
			if ($files[$i].size > $maxFileSize) {
				$errors.push(
					$imgur.lang.image_too_big
					.replace('{file}', $files[$i].name)
					.replace('{size}', (($files[$i].size / 1024) / 1024))
					.replace('{max_size}', (($maxFileSize / 1024) / 1024))
				);
				continue;
			}

			$formData.append('imgur_image[]', $files[$i]);
		}

		// Exit if no images were added
		if (!$formData.has('imgur_image[]')) {
			$errors.push($imgur.lang.no_images);
		}

		// Show progress bar
		$progress.wrapper.addClass('uploading');

		// Upload the image(s)
		$.ajax({
			url: $imgurButton.attr('data-ajax-action'),
			type: 'POST',
			data: $formData,
			contentType: false,
			cache: false,
			processData: false,
			xhr: function() {
				var $xhr = $.ajaxSettings.xhr();

				$xhr.upload.addEventListener('progress', function($event) {
					if ($event.lengthComputable) {
						var $percentage = ($event.loaded / $event.total) * 100;

						// Update progress bar percentage
						$progress.bar.val($percentage);

						// Show progress bar info
						$progress.label.text(
							$imgur.lang.upload_progress
							.replace('{percentage}', $percentage)
							.replace('{loaded}', (($event.loaded / 1024) / 1024))
							.replace('{total}', (($event.total / 1024) / 1024))
						);
					}
				}, false);

				return $xhr;
			}
		}).done(function($data) {
			try {
				// Process images
				$loadingIndicator = phpbb.loadingIndicator();

				// Empty response
				if ($data.length <= 0) {
					return;
				}

				// PHP $_FILES errors
				if ($data.errors.length > 0) {
					for (var $i = 0; $i < $data.errors.length; $i++) {
						$errors.push($data.errors[$i]);
					}
				}

				// Add image
				$.each($data, function($key, $value) {
					// Check only numeric keys
					// https://stackoverflow.com/a/9716488
					if (isNaN(parseFloat($key)) || !isFinite($key)) {
						return;
					}

					var $bbcode = '';
					var $image = {
						link: '',
						thumbnail: ''
					};

					$image.link = $value.link;

					// Generate thumbnail
					if ($image.link.length > 0) {
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
			} catch (ex) {
				$errors.push(ex.message);
			}
		}).fail(function($data, $textStatus, $error) {
			// Parse JSON response
			try {
				$responseBody = $.parseJSON($data.responseText);
				$errors.push($responseBody.message);
			} catch (ex) {
				$errors.push(ex.message);
			}

			// Failure error message
			$errors.push($error);
		}).then(function() {
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
					phpbb.alert($imgur.lang.error, $message);
				}
			}
		}).always(function() {
			// Re-enable button
			$imgurButton.prop('disabled', false);

			// Reset progress bar
			$progress.wrapper.removeClass('uploading');
			$progress.bar.removeAttr('value');

			// Hide loading indicator
			if ($loadingIndicator && $loadingIndicator.is(':visible')) {
				$loadingIndicator.fadeOut(phpbb.alertTime);
			}
		});
	});
})(jQuery);
