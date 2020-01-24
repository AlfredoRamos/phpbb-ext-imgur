/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

(function() {
	'use strict';

	// Imgur API limit (MiB)
	const maxFileSize = (10 * 1024 * 1024);

	// Imgur API allowed MIME types
	// https://help.imgur.com/hc/en-us/articles/115000083326
	const allowedMIMETypes = [
		'image/jpeg',
		'image/png',
		'image/gif',
		'image/tiff',
		'image/tiff-fx',
		'image/webp'
	];

	// Local and session storage
	const imgurStorage = {
		enabled: (typeof Storage !== 'undefined'),
		local: 'imgur_output_type',
		session: 'imgur_output_list'
	};

	// Global variables
	let outputList = [];
	let errors = [];
	let addOutput = true;

	// Show image selection window
	document.body.addEventListener('click', function(e) {
		let image = document.body.querySelector('#imgur-image');
		let button = e.target.closest('.imgur-button');

		if (!image || !button) {
			return;
		}

		let attribute = button.getAttribute('data-add-output');

		addOutput = (attribute === null || attribute === 'true');

		let evt;

		// IE11 fix
		if (typeof MouseEvent !== 'function') {
			evt = document.createEvent('MouseEvent');
			evt.initEvent('click', true, true);
		} else {
			evt = new MouseEvent('click', {
				bubbles: true,
				cancelable: true
			});
		}

		image.dispatchEvent(evt);
	});

	// Upload images
	document.body.addEventListener('change', function(e) {
		if (!e.target.matches('#imgur-image')) {
			return;
		}

		let imgurImage = e.target;
		let imgurButton = document.body.querySelectorAll('.imgur-button');
		let files = imgurImage.files;
		let formData = new FormData();

		// Prevent button spamming
		imgurButton.forEach(function(item) {
			if (!item) {
				return;
			}

			item.setAttribute('disabled', true);
		});
		imgurImage.setAttribute('disabled', true);

		// Validate file list
		if (files.length <= 0) {
			return;
		}

		// Validate file
		Array.prototype.forEach.call(files, function(file) {
			// MIME type
			if (allowedMIMETypes.indexOf(file.type) < 0) {
				errors.push(
					imgur.lang.invalidMimeType
					.replace('{file}', file.name)
					.replace('{type}', file.type)
				);

				return;
			}

			// Size
			if (file.size > maxFileSize) {
				errors.push(
					imgur.lang.imageTooBig
					.replace('{file}', file.name)
					.replace('{size}', ((file.size / 1024) / 1024))
					.replace('{max_size}', ((maxFileSize / 1024) / 1024))
				);

				return;
			}

			// Add image to upload queue
			formData.append('imgur_image[]', file);
		});

		// Exit if no images were added
		if (!formData.has('imgur_image[]')) {
			errors.push(imgur.lang.noImages);
			showImgurErrors(errors);

			// Re-enable buttons
			imgurButton.forEach(function(item) {
				if (!item) {
					return;
				}

				item.removeAttribute('disabled');
			});
			imgurImage.removeAttribute('disabled');

			// Clear error messages
			errors = [];

			return;
		}

		// Restore user preference
		if (imgurStorage.enabled) {
			if (window.localStorage.getItem(imgurStorage.local) !== 'null' &&
				window.localStorage.getItem(imgurStorage.local) !== null
			) {
				imgurImage.setAttribute(
					'data-output-type',
					window.localStorage.getItem(imgurStorage.local)
				);
			}
		}

		// Helpers
		let progress = {};
		let responseBody = {};

		// Progress bar
		progress.wrapper = document.body.querySelector('#imgur-progress-wrapper');

		// Show progress bar
		if (progress.wrapper) {
			progress.bar = progress.wrapper.querySelector('#imgur-progress');
			progress.label = progress.wrapper.querySelector('#imgur-progress-label > code');

			progress.wrapper.classList.add('uploading');
		}

		// Upload the image(s)
		const xhr = new XMLHttpRequest();

		// Progress
		xhr.upload.addEventListener('progress', function(e) {
			if (!e.lengthComputable || !progress.bar || !progress.label) {
				return;
			}

			let percentage = (e.loaded / e.total) * 100;

			// Update progress bar percentage
			progress.bar.value = percentage;

			// Show progress bar info
			progress.label.textContent = imgur.lang.uploadProgress
				.replace('{percentage}', percentage)
				.replace('{loaded}', formatImageSize((e.loaded / 1024) / 1024))
				.replace('{total}', formatImageSize((e.total / 1024) / 1024));

			// Progress bar native animation will be used as loading indicator
			if (percentage >= 100) {
				setTimeout(function() {
					progress.bar.removeAttribute('value');
				}, 500);
			}
		}, false);

		// Success
		xhr.addEventListener('load', function(e) {
			try {
				// Get response
				let response = e.target.responseText;

				// Empty response
				if (response.length <= 0) {
					errors.push(imgur.lang.emptyResponse);
					return;
				}

				// Parse JSON response
				responseBody = JSON.parse(response);

				// Empty response body
				if (responseBody.length <= 0) {
					errors.push(imgur.lang.emptyResponse);
					return;
				}

				// Check for errors
				if (e.target.status !== 200) {
					// Get error message
					if (Array.isArray(responseBody)) {
						responseBody.forEach(function(item) {
							if (!item) {
								return;
							}

							errors.push(item.message);
						});
					} else {
						errors.push(responseBody.message);
					}

					errors.push(e.target.statusText);
					return;
				}

				// Add image
				responseBody.forEach(function(item) {
					if (!item) {
						return;
					}

					let output = {};
					let bbcode = '';
					let image = {
						link: '',
						thumbnail: ''
					};

					// Get image link
					image.link = item.link;

					// Generate thumbnail
					if (image.link.length > 0) {
						let ext = '.' + image.link.split('.').pop();
						let size = imgurImage.getAttribute('data-thumbnail-size').trim() || 't';

						image.thumbnail = image.link.replace(
							ext,
							size + ext
						);
					}

					// Generate output types
					output.text = image.link;
					output.url = '[url]' + image.link + '[/url]';
					output.image = '[img]' + image.link + '[/img]';
					output.thumbnail = '[url=' + image.link + '][img]'
						+ image.thumbnail + '[/img][/url]';

					// Save data to session
					if (imgurStorage.enabled) {
						outputList = Object.entries(output);
						window.sessionStorage.setItem(imgurStorage.session, JSON.stringify(outputList));
					}

					let outputType = imgurImage.getAttribute('data-output-type');

					// Generate BBCode
					if (output.hasOwnProperty(outputType)) {
						if (output[outputType].length > 0) {
							bbcode = output[outputType];
						}
					} else {
						// Fallback to image
						imgurImage.setAttribute('data-output-type', 'image');

						document.body.querySelectorAll('.imgur-output-select').forEach(function(item) {
							if (!item) {
								return;
							}

							item.value = 'image';

							let evt;

							// IE11 fix
							if (typeof Event !== 'function') {
								evt = document.createEvent('Event');
								evt.initEvent('change', true, true);
							} else {
								evt = new Event('change', {
									bubbles: true,
									cancelable: true
								});
							}

							item.dispatchEvent(evt);
						});

						bbcode = output.image;
					}

					// Add BBCode to content
					if (addOutput) {
						insert_text(bbcode);
					}
				});
			} catch (ex) {
				errors.push(ex.message);
			}

			showImgurErrors(errors);
		});

		// Failure
		xhr.addEventListener('error', function(e) {
			try {
				// Get response
				let response = e.target.responseText;

				// Empty response
				if (response.length <= 0) {
					errors.push(imgur.lang.emptyResponse);
					return;
				}

				// Parse JSON response
				responseBody = JSON.parse(response);

				// Empty response body
				if (responseBody.length <= 0) {
					errors.push(imgur.lang.emptyResponse);
					return;
				}

				// Get error message
				if (Array.isArray(responseBody)) {
					responseBody.forEach(function(item) {
						if (!item) {
							return;
						}

						errors.push(item.message);
					});
				} else {
					errors.push(responseBody.message);
				}
			} catch (ex) {
				errors.push(ex.message);
			}

			showImgurErrors(errors);
		});

		// Post-upload
		xhr.addEventListener('loadend', function() {
			try {
				fillOutputFields(outputList);
			} catch (ex) {
				errors.push(ex.message);
			}

			showImgurErrors(errors);

			// Re-enable buttons
			imgurButton.forEach(function(item) {
				if (!item) {
					return;
				}

				item.removeAttribute('disabled');
			});
			imgurImage.removeAttribute('disabled');

			// Reset progress bar
			if (progress.wrapper) {
				progress.wrapper.classList.remove('uploading');

				if (progress.bar) {
					progress.bar.removeAttribute('value');
				}
			}

			// Clear error messages
			errors = [];
		});

		// Initialize request
		xhr.open(
			'POST',
			imgurImage.getAttribute('data-ajax-action').trim(),
			true
		);

		// Additional headers
		xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xhr.setRequestHeader('Cache-Control', 'no-cache');

		// Send request
		xhr.send(formData);
	});

	// Imgur dropdown menu
	document.body.addEventListener('contextmenu', function(e) {
		let wrapper = e.target.closest('.imgur-wrapper');

		if (!wrapper) {
			return;
		}

		let select = wrapper.querySelector('.imgur-output-select');

		if (!select) {
			return;
		}

		e.preventDefault();
		select.classList.toggle('select');
		select.focus();
	});

	// Update output type
	document.body.addEventListener('change', function(e) {
		if (!e.target.matches('.imgur-output-select')) {
			return;
		}

		let image = document.body.querySelector('#imgur-image');
		let type = e.target.value.trim();

		if (!image || type.length <= 0) {
			return;
		}

		image.setAttribute('data-output-type', type);

		// Save preferences
		if (imgurStorage.enabled) {
			window.localStorage.setItem(imgurStorage.local, type);
		}
	});

	// Hide Imgur output select
	document.body.addEventListener('click', function(e) {
		if (!e.target.matches('.select')) {
			let select = e.target.closest('.imgur-output-select');

			if (!select) {
				return;
			}

			select.classList.toggle('select', !select.classList.contains('select'));
		}
	});

	// Copy output field text to message
	document.body.addEventListener('click', function(e) {
		let button = e.target.closest('.imgur-output-paste');

		if (!button) {
			return;
		}

		let field = button.parentNode.querySelector('.imgur-output-field');

		if (!field) {
			return;
		}

		let bbcode = field.value.trim();

		if (bbcode.length <= 0) {
			return;
		}

		// Add BBCode to content
		insert_text(bbcode);
	});

	// Show output fields only when needed
	document.body.addEventListener('change', function(e) {
		if (!e.target.matches('.imgur-output-field')) {
			return;
		}

		let field = e.target;
		let wrapper = field.closest('dl');
		let cssClass = 'hidden';

		wrapper.classList.toggle(cssClass, (
			field.value.trim().length <= 0 && !wrapper.classList.contains(cssClass)
		));
	});

	// Resize output fields in posting editor panel
	let outputFields = document.body.querySelectorAll('.imgur-output-field');
	if (outputFields.length > 0) {
		phpbb.resizeTextArea(document.body.querySelectorAll('.imgur-output-field'));
	}

	// Add generated output in posting editor panel
	try {
		if (imgurStorage.enabled) {
			let image = document.body.querySelector('#imgur-image');

			let output = {
				type: {
					default: (image ? image.getAttribute('data-output-type').trim() : 'image'),
					current: window.localStorage.getItem(imgurStorage.local),
					allowed: imgur.config.types.split(',')
				}
			};

			// Fallback to default
			if (output.type.current === 'null' || output.type.current === null) {
				output.type.current = output.type.default;
			}

			// Must be allowed
			if (output.type.allowed.length > 0 && output.type.allowed.indexOf(output.type.current) < 0) {
				// Try image first
				let index = output.type.allowed.indexOf('image');

				// Fallback to first available
				index = (index < 0) ? 0 : index;

				// Update current value
				output.type.current = output.type.allowed[index];
			}

			if (window.sessionStorage.getItem(imgurStorage.session) !== 'null' &&
				window.sessionStorage.getItem(imgurStorage.session) !== null
			) {
				let selects = document.body.querySelectorAll('.imgur-output-select');

				// Restore user preference
				selects.forEach(function(item) {
					if (!item) {
						return;
					}

					let option = item.querySelector('[value="' + output.type.current + '"]');

					if (!option) {
						return;
					}

					option.selected = true;

					let evt;

					// IE11 fix
					if (typeof Event !== 'function') {
						evt = document.createEvent('Event');
						evt.initEvent('change', true, true);
					} else {
						evt = new Event('change', {
							bubbles: true,
							cancelable: true
						});
					}

					option.dispatchEvent(evt);
				});

				// Delete output if page doesn't have the fields to do so
				if (document.body.querySelectorAll('#imgur-panel .imgur-output-field').length <= 0) {
					window.sessionStorage.removeItem(imgurStorage.session);
				}

				// Get stored output
				outputList = outputList.concat(JSON.parse(
					window.sessionStorage.getItem(imgurStorage.session)
				));
			}

			fillOutputFields(outputList);
		}
	} catch (ex) {
		errors.push(ex.message);
	}

	showImgurErrors(errors);
})();
