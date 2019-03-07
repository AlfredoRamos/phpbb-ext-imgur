/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

(function($) {
	'use strict';

	if (typeof window.$imgur === 'undefined') {
		var $imgur = {};
	} else {
		$imgur = $.extend(true, {
			lang: {
				imageTooBig: 'The image <samp>{file}</samp> is <code>{size}</code> MiB and it should be less that <code>{max_size}</code> MiB.',
				noImages: 'There are no images to upload.',
				uploadProgress: '{percentage}% ({loaded} / {total} MiB)'
			}
		}, window.$imgur);
	}

	// Show image selection window
	$(document.body).on('click', '.imgur-button', function() {
		$('#imgur-image').trigger('click');
	});

	// Upload images
	$(document.body).on('change', '#imgur-image', function() {
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
					$imgur.lang.imageTooBig
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
			$errors.push($imgur.lang.noImages);
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

				// Progress listener
				$xhr.upload.addEventListener('progress', function($event) {
					if ($event.lengthComputable) {
						var $percentage = ($event.loaded / $event.total) * 100;

						// Update progress bar percentage
						$progress.bar.val($percentage);

						// Show progress bar info
						$progress.label.text(
							$imgur.lang.uploadProgress
							.replace('{percentage}', $percentage)
							.replace('{loaded}', formatImageSize(($event.loaded / 1024) / 1024))
							.replace('{total}', formatImageSize(($event.total / 1024) / 1024))
						);

						// Progress bar native animation will
						// be used as loading indicator
						if ($percentage >= 100) {
							setTimeout(function() {
								$progress.bar.removeAttr('value');
							}, 500);
						}
					}
				}, false);

				return $xhr;
			}
		}).done(function($data) {
			console.log($data);
			try {
				// Empty response
				if ($data.length <= 0) {
					return;
				}

				// Add image
				$.each($data, function($key, $value) {
					var $bbcode = '';
					var $image = {
						link: '',
						thumbnail: ''
					};
					var $output = {};

					// Get image link
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

					// Generate output types
					$output.text = $image.link;
					$output.url = '[url]' + $image.link + '[/url]';
					$output.image = '[img]' + $image.link + '[/img]';
					$output.thumbnail = '[url=' + $image.link + '][img]'
						+ $image.thumbnail + '[/img][/url]';
					$output.markdown_image = '![](' + $image.link + ')';
					$output.markdown_thumbnail = '[![](' + $image.thumbnail
						+ ')](' + $image.link + ')';

					// Generate BBCode
					if ($output.hasOwnProperty($imgurButton.attr('data-output-type'))) {
						if ($output[$imgurButton.attr('data-output-type')].length > 0) {
							$bbcode = $output[$imgurButton.attr('data-output-type')];
						}
					}

					// Add BBCode to content
					for (var $k in $contentBody) {
						if ($contentBody.hasOwnProperty($k)) {
							if ($contentBody[$k].length > 0 && $bbcode.length > 0) {
								$contentBody[$k].insertAtCaret($bbcode);
							}
						}
					}

					// Add generated output in posting editor panel
					for (var $k in $output) {
						if ($output.hasOwnProperty($k)) {
							var $field = $('[name="imgur_output_' + $k + '"]').first();

							if ($field.length > 0 && $output[$k].length > 0) {
								$field.val($output[$k]);
							}
						}
					}
				});
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
			// Re-enable button
			$imgurButton.prop('disabled', false);

			// Reset progress bar
			$progress.wrapper.removeClass('uploading');
			$progress.bar.removeAttr('value');
		});
	});

	// Imgur dropdown menu
	$(document.body).on('contextmenu', '.imgur-button', function($event) {
		$event.preventDefault();
		$(this).parents('.imgur-dropdown-container').first()
			.children('.imgur-dropdown').first().toggleClass('show');
	});

	// Imgur dropdown menu item actions
	$(document.body).on('click', '.imgur-dropdown-content li > span', function() {
		var $imgurButton = $('#imgur-image');

		// Update output type
		$imgurButton.attr('data-output-type', $(this).attr('data-output-type'));

		// Trigger upload
		$imgurButton.trigger('click');
	});

	// Close Imgur dropdown menu
	$(document.body).on('click', function($event) {
		if (!$event.target.matches('.imgur-button-content li > span')) {
			$.each($('.imgur-dropdown'), function() {
				if ($(this).hasClass('show')) {
					$(this).removeClass('show');
				}
			});
		}
	});
})(jQuery);
