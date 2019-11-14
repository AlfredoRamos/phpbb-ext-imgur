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

	// Global variables
	var $outputList = [];
	var $errors = [];
	var $imgurStorage = {
		enabled: (typeof(Storage) !== 'undefined'),
		local: 'imgur_output_type',
		session: 'imgur_output_list'
	};
	var $addOutput = true;

	// Show image selection window
	$(document.body).on('click', '.imgur-button', function() {
		$addOutput = (typeof($(this).attr('data-add-output')) === 'undefined' ||
			$(this).attr('data-add-output') === 'true');
		$('#imgur-image').trigger('click');
	});

	// Upload images
	$(document.body).on('change', '#imgur-image', function() {
		var $imgurButton = $(this);
		var $formData = new FormData();
		var $files = $imgurButton.prop('files');
		var $progress = {};
		var $responseBody = {};

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

		// Restore user preference
		if ($imgurStorage.enabled) {
			if (window.localStorage.getItem($imgurStorage.local) !== 'null' &&
				window.localStorage.getItem($imgurStorage.local) !== null
			) {
				$imgurButton.attr('data-output-type', window.localStorage.getItem($imgurStorage.local));
			}
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
			try {
				// Empty response
				if ($data.length <= 0) {
					return;
				}

				// Remove session data
				if ($imgurStorage.enabled) {
					if (window.sessionStorage.getItem($imgurStorage.session) !== 'null' &&
						window.sessionStorage.getItem($imgurStorage.session) !== null
					) {
						window.sessionStorage.removeItem($imgurStorage.session);
					}
				}

				// Add image
				$.each($data, function($key, $value) {
					var $output = {};
					var $bbcode = '';
					var $image = {
						link: '',
						thumbnail: ''
					};

					// Get image link
					$image.link = $value.link;

					// Get image title
					$image.title = $value.title || $value.name || '';

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

					// Add custom output types
					$(document.body).trigger('alfredoramos.imgur.output_append', [$output, $image]);

					// Save (and append) data to session
					if ($imgurStorage.enabled) {
						if (window.sessionStorage.getItem($imgurStorage.session) !== 'null' &&
							window.sessionStorage.getItem($imgurStorage.session) !== null
						) {
							$outputList = JSON.parse(window.sessionStorage.getItem($imgurStorage.session));
						}

						$outputList.push($output);
						window.sessionStorage.setItem($imgurStorage.session, JSON.stringify($outputList));
					}

					// Generate BBCode
					if ($output.hasOwnProperty($imgurButton.attr('data-output-type'))) {
						if ($output[$imgurButton.attr('data-output-type')].length > 0) {
							$bbcode = $output[$imgurButton.attr('data-output-type')];
						}
					} else {
						// Fallback to image
						$imgurButton.attr('data-output-type', 'image');
						$('.imgur-output-select').val('image');
						$('.imgur-output-select').trigger('change');
						$bbcode = $output['image'];
					}

					// Add BBCode to content
					if ($addOutput) {
						insert_text($bbcode);
					}
				});
			} catch (ex) {
				$errors.push(ex.message);
			}

			showImgurErrors($errors);
		}).fail(function($data, $textStatus, $error) {
			try {
				// Parse JSON response
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
			try {
				fillOutputFields($outputList);
			} catch (ex) {
				$errors.push(ex.message);
			}

			showImgurErrors($errors);
		}).always(function() {
			// Re-enable button
			$imgurButton.prop('disabled', false);

			// Reset progress bar
			$progress.wrapper.removeClass('uploading');
			$progress.bar.removeAttr('value');

			// Clear errors messages
			$errors = [];
		});
	});

	// Imgur dropdown menu
	$(document.body).on('contextmenu', '.imgur-wrapper > .imgur-button', function($event) {
		$event.preventDefault();

		var $select = $(this).parents('.imgur-wrapper').first()
			.children('.imgur-output-select').first();

		$select.toggleClass('select');
		$select.focus();
	});

	// Update output type
	$(document.body).on('change', '.imgur-output-select', function() {
		$('#imgur-image').attr('data-output-type', $(this).val());

		// Save user preference
		if ($imgurStorage.enabled) {
			window.localStorage.setItem($imgurStorage.local, $(this).val());
		}
	});

	// Handle Imgur click events
	$(document.body).on('click', function($event) {
		var $select = '.imgur-output-select';
		var $class = 'select';

		// Polyfill for Element.matches()
		// https://developer.mozilla.org/en-US/docs/Web/API/Element/matches#Polyfill
		if (!Element.prototype.matches) {
			Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
		}

		// Hide select
		if (!$event.target.matches($select)) {
			$.each($($select), function() {
				if ($(this).hasClass($class)) {
					$(this).removeClass($class);
				}
			});
		}
	});

	// Copy output field text to message
	$(document.body).on('click', '.imgur-output-paste', function() {
		var $bbcode = $.trim($(this).siblings('.imgur-output-field').first().val());

		// BBCode value should not be empty
		if ($bbcode.length <= 0) {
			return;
		}

		// Add BBCode to content
		insert_text($bbcode);
	});

	// Show output fields only when needed
	$(document.body).on('change', '.imgur-output-field', function() {
		var $wrapper = $(this).parents('dl').first();
		var $class = 'hidden';

		if ($(this).val().length > 0 && $wrapper.hasClass($class)) {
			$wrapper.removeClass($class);
		}
	});

	// Resize output fields in posting editor panel
	phpbb.resizeTextArea($('.imgur-output-field'));

	// Add generated output in posting editor panel
	try {
		if ($imgurStorage.enabled) {
			var $output = {
				type: {
					default: $('#imgur-image').attr('data-output-type'),
					current: window.localStorage.getItem($imgurStorage.local),
					allowed: $imgur.config.types.split(',')
				}
			};

			// Fallback to default
			if ($output.type.current === 'null' || $output.type.current === null) {
				$output.type.current = $output.type.default;
			}

			// Must be allowed
			if ($output.type.allowed.length > 0 && $output.type.allowed.indexOf($output.type.current) < 0) {
				// Try image first
				var $index = $output.type.allowed.indexOf('image');

				// Fallback to first available
				$index = ($index < 0) ? 0 : $index;

				// Update current value
				$output.type.current = $output.type.allowed[$index];
			}

			// Restore user preference
			if ($('.imgur-output-select').length > 0 &&
				window.localStorage.getItem($imgurStorage.local) !== 'null' &&
				window.localStorage.getItem($imgurStorage.local) !== null
			) {
				$('.imgur-output-select').val($output.type.current);
				$('.imgur-output-select').trigger('change');
			}

			if (window.sessionStorage.getItem($imgurStorage.session) !== 'null' &&
				window.sessionStorage.getItem($imgurStorage.session) !== null
			) {
				// Delete output if page doesn't have the fields to do so
				if ($('#imgur-panel .imgur-output-field').length <= 0) {
					window.sessionStorage.removeItem($imgurStorage.session);
					return;
				}

				// Get stored output
				$outputList = $outputList.concat(JSON.parse(
					window.sessionStorage.getItem($imgurStorage.session)
				));
			}

			fillOutputFields($outputList);
		}
	} catch (ex) {
		$errors.push(ex.message);
	}

	showImgurErrors($errors);
})(jQuery);
