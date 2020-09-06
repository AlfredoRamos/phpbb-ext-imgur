/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

(function() {
	'use strict';

	// Imgur library
	const Imgur =  {};

	// Storage configuration
	Imgur.storage = {
		enabled: (typeof Storage !== 'undefined'),
		local: 'imgur_output_type',
		session: 'imgur_output_list'
	};

	/**
	 * Format file size with the appropriate unit.
	 *
	 * @param float fileSize
	 *
	 * @return string
	 */
	Imgur.formatImageSize = function(fileSize) {
		// Binary round number
		const factor = 1024;

		// Only show this number of digits after the decimal point
		const digits = 3;

		// Byte
		let size = fileSize;
		let unit = window.imgur.lang.byte;

		if (fileSize >= (factor * factor)) {
			// MiB
			size /= (factor * factor);
			unit = window.imgur.lang.mebiByte;
		} else if (fileSize >= factor) {
			// KiB
			size /= factor;
			unit = window.imgur.lang.kibiByte;
		}

		// Show only specified fractional digits
		size = size.toFixed(digits);

		// Size and unit (language key)
		return (size + ' ' + unit);
	};

	/**
	 * Show errors in a modal window.
	 *
	 * @param array errors
	 *
	 * @return void
	 */
	Imgur.showErrors = function (errors) {
		if (!Array.isArray(errors) || errors.length <= 0) {
			return;
		}

		let message = '';

		errors.forEach(function(error, index) {
			if (!error) {
				return;
			}

			message += error;

			if (index < (errors.length - 1)) {
				message += '<br>';
			}
		});

		if (message.length <= 0) {
			return;
		}

		// Show a phpBB alert with the errors
		window.phpbb.alert(window.imgur.lang.error, message);
	};

	/**
	 * Check if a string is a valid JSON.
	 * Modified version of kubosho's code.
	 * https://stackoverflow.com/a/33369954
	 *
	 * @param string str
	 *
	 * @return bool
	 */
	Imgur.isJSON = function(str) {
		if (typeof str !== 'string') {
			return false;
		}

		let json = null;

		try {
			json = JSON.parse(str);
		} catch (ex) {
			return false;
		}

		if (typeof json === 'object' && json !== null) {
			return true;
		}

		return false;
	};

	/**
	 * Get allowed output type.
	 *
	 * @return string
	 */
	Imgur.getOutputType = function() {
		let defaultType = 'image';

		if (!window.imgur.storage.enabled) {
			return defaultType;
		}

		let current = window.localStorage.getItem(window.imgur.storage.local);
		const allowed = window.imgur.config.types.split(',');

		// Fallback to default
		if (current === 'null' || current === null) {
			current = defaultType;
		}

		// Must be allowed
		if (allowed.length > 0 && allowed.indexOf(current) < 0) {
			// Try image first
			let index = allowed.indexOf('image');

			// Fallback to first available
			index = (index < 0) ? 0 : index;

			// Update current value
			current = allowed[index];
			window.localStorage.setItem(window.imgur.storage.local, current);
		}

		return current;
	};

	/**
	 * Fill output fields.
	 *
	 * @return void
	 */
	Imgur.fillOutputFields = function() {
		let output = [];

		if (window.imgur.storage.enabled) {
			let savedList = window.sessionStorage.getItem(window.imgur.storage.session);

			if (savedList !== 'null' && savedList !== null) {
				output = output.concat(JSON.parse(savedList));
			}
		}

		if (!Array.isArray(output) || output.length <= 0) {
			return;
		}

		const allowed = window.imgur.config.types.split(',');

		// Allowed output types must not be empty
		if (allowed.length <= 0) {
			return;
		}

		// Cleanup
		document.body.querySelectorAll('.imgur-output-field').forEach(function(item) {
			if (!item) {
				return;
			}

			item.value = '';
		});

		// Fill fields
		output.forEach(function(item) {
			if (!item) {
				return;
			}

			for (let key in item) {
				// Fill only allowed output types
				if (allowed.indexOf(key) < 0) {
					continue;
				}

				let value = item[key];
				let field = document.body.querySelector('[name="imgur_output_' + key + '"]');

				if (!field) {
					return;
				}

				field.value = field.value.trim();
				value = value.trim();

				if (value.length <= 0) {
					return;
				}

				field.value += (field.value.length > 0 ? '\n' : '') + value;

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

				field.dispatchEvent(evt);
			}
		});
	};

	/**
	 * Validate and upload images to Imgur.
	 *
	 * @param FileList
	 * @param object
	 *
	 * @return void
	 */
	Imgur.upload = function(files, args) {
		// Imgur API limit (10 MiB)
		// https://apidocs.imgur.com/?version=latest#c85c9dfc-7487-4de2-9ecd-66f727cf3139
		const maxFileSize = (10 * 1024 * 1024);

		// Imgur API allowed MIME types
		// https://help.imgur.com/hc/en-us/articles/115000083326
		const mimeTypesRegexp = /^image\/(?:jpe?g|png|gif|tiff(?:-fx)?|webp)$/i;

		// Images container
		const formData = new FormData();

		// Output type
		let outputType = Imgur.getOutputType();

		// Helpers
		let errors = [];

		if (typeof args === 'undefined') {
			args = {};
		}

		if (!args.hasOwnProperty('image') || typeof args.image === 'undefined') {
			args.image = document.body.querySelector('#imgur-image');
		}

		if (!args.hasOwnProperty('output') || typeof args.output === 'undefined') {
			args.output = true;
		}

		// Upload buttons
		let imgurButton = document.body.querySelectorAll('.imgur-button-upload');

		// Prevent button spamming
		imgurButton.forEach(function(item) {
			if (!item) {
				return;
			}

			item.setAttribute('disabled', true);
		});

		args.image.setAttribute('disabled', true);

		// Validate file list
		if (files.length <= 0) {
			return;
		}

		// Validate file
		Array.prototype.forEach.call(files, function(file) {
			// Size
			if (file.size > maxFileSize) {
				errors.push(
					window.imgur.lang.imageTooBig
					.replace('{file}', file.name)
					.replace('{size}', Imgur.formatImageSize(file.size))
					.replace('{max_size}', Imgur.formatImageSize(maxFileSize))
				);

				return;
			}

			// MIME type
			if (!mimeTypesRegexp.test(file.type)) {
				errors.push(
					window.imgur.lang.invalidMimeType
					.replace('{file}', file.name)
					.replace('{type}', file.type)
				);

				return;
			}

			// Add image to upload queue
			formData.append('imgur_image[]', file);
		});

		// Exit if no images were added
		if (!formData.has('imgur_image[]')) {
			errors.push(window.imgur.lang.noImages);
			Imgur.showErrors(errors);

			// Re-enable buttons
			imgurButton.forEach(function(item) {
				if (!item) {
					return;
				}

				item.removeAttribute('disabled');
			});

			args.image.removeAttribute('disabled');

			return;
		}

		// Restore user preference
		args.image.setAttribute('data-output-type', outputType);

		// Helpers
		let progress = {};
		let response = {};

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
			progress.label.textContent = window.imgur.lang.uploadProgress
				.replace('{percentage}', percentage)
				.replace('{loaded}', Imgur.formatImageSize(e.loaded))
				.replace('{total}', Imgur.formatImageSize(e.total));

			// Progress bar native animation will be used as loading indicator
			if (percentage >= 100) {
				setTimeout(function() {
					progress.bar.removeAttribute('value');
				}, 500);
			}
		}, false);

		// Success
		xhr.addEventListener('load', function(e) {
			let outputList = [];

			try {
				// Get response
				let rawResponse = e.target.responseText;

				// Check for server errors or invalid JSON data
				if (!Imgur.isJSON(rawResponse) && e.target.status !== 200) {
					errors.push('HTTP ' + e.target.status + ' - ' + e.target.statusText);
					return;
				}

				// Empty response
				if (rawResponse.length <= 0) {
					errors.push(window.imgur.lang.emptyResponse);
					return;
				}

				// Parse JSON response
				response = JSON.parse(rawResponse);

				// Empty response body
				if (Object.keys(response).length <= 0) {
					errors.push(window.imgur.lang.emptyResponse);
					return;
				}

				// Check for errors
				if (e.target.status !== 200) {
					// Get error message
					if (Array.isArray(response)) {
						response.forEach(function(item) {
							if (!item) {
								return;
							}

							errors.push(item.message);
						});
					} else {
						errors.push(response.message);
					}

					errors.push(e.target.statusText);
					return;
				}

				// Add image
				response.forEach(function(item) {
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
						let size = args.image.getAttribute('data-thumbnail-size').trim() || 't';

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

					// Append output
					outputList.push(output);

					// Generate BBCode
					if (output.hasOwnProperty(outputType)) {
						if (output[outputType].length > 0) {
							bbcode = output[outputType];
						}
					}

					// Add BBCode to content
					if (args.output) {
						insert_text(bbcode);
					}
				});

				// Save data to session
				if (window.imgur.storage.enabled && Array.isArray(outputList) && outputList.length > 0) {
					window.sessionStorage.setItem(window.imgur.storage.session, JSON.stringify(outputList));
				}
			} catch (ex) {
				errors.push(ex.message);
			}

			Imgur.showErrors(errors);
		});

		// Failure
		xhr.addEventListener('error', function(e) {
			try {
				// Get response
				let rawResponse = e.target.responseText;

				// Empty response
				if (rawResponse.length <= 0) {
					errors.push(window.imgur.lang.emptyResponse);
					return;
				}

				// Parse JSON response
				response = JSON.parse(rawResponse);

				// Empty response body
				if (Object.keys(response).length <= 0) {
					errors.push(window.imgur.lang.emptyResponse);
					return;
				}

				// Get error message
				if (Array.isArray(response)) {
					response.forEach(function(item) {
						if (!item) {
							return;
						}

						errors.push(item.message);
					});
				} else {
					errors.push(response.message);
				}
			} catch (ex) {
				errors.push(ex.message);
			}

			Imgur.showErrors(errors);
		});

		// Post-upload
		xhr.addEventListener('loadend', function() {
			try {
				Imgur.fillOutputFields();
			} catch (ex) {
				errors.push(ex.message);
			}

			Imgur.showErrors(errors);

			// Re-enable buttons
			imgurButton.forEach(function(item) {
				if (!item) {
					return;
				}

				item.removeAttribute('disabled');
			});

			args.image.removeAttribute('disabled');

			// Reset progress bar
			if (progress.wrapper) {
				progress.wrapper.classList.remove('uploading');

				if (progress.bar) {
					progress.bar.removeAttribute('value');
				}
			}
		});

		// Initialize request
		xhr.open(
			'POST',
			args.image.getAttribute('data-ajax-action').trim(),
			true
		);

		// Additional headers
		xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xhr.setRequestHeader('Cache-Control', 'no-cache');

		// Send request
		xhr.send(formData);
	};

	/**
	 * Handle files inside drop zone.
	 *
	 * @param Event
	 *
	 * @return HTMLElement|null
	 */
	Imgur.preventDropZoneDefaults = function(e) {
		let element = (e.target.nodeType === Node.TEXT_NODE) ? e.target.parentNode : e.target;
		element = element.closest('#imgur-drop-zone');

		if (!element) {
			return null;
		}

		e.preventDefault();
		e.stopPropagation();

		return element;
	};

	/**
	 * Highlight drop zone.
	 *
	 * @param bool
	 *
	 * @return void
	 */
	Imgur.highlightDropZone = function(addOrRemove) {
		let element = document.body.querySelector('#imgur-drop-zone');

		if (!element) {
			return;
		}

		addOrRemove = (typeof addOrRemove === 'undefined') ? true : addOrRemove;

		const cssClass = 'active';

		if (addOrRemove === true) {
			if (!element.classList.contains(cssClass)) {
				element.classList.add(cssClass);
			}
		} else {
			if (element.classList.contains(cssClass)) {
				element.classList.remove(cssClass);
			}
		}
	};

	// Extend and export to global scope
	window.imgur = Object.assign(Imgur, window.imgur);
})();
