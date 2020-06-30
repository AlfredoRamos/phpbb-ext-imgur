/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

(function() {
	'use strict';

	// Global variables
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
		let imgurImage = e.target;

		if (!imgurImage.matches('#imgur-image')) {
			return;
		}

		// Upload images
		uploadImagesToImgur(imgurImage.files, {
			image: imgurImage,
			output: addOutput
		});
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
		let select = e.target;

		if (!select.matches('.imgur-output-select')) {
			return;
		}

		let image = document.body.querySelector('#imgur-image');
		let type = select.value.trim();

		if (!image || type.length <= 0) {
			return;
		}

		image.setAttribute('data-output-type', type);

		// Save preferences
		if (window.imgur.storage.enabled) {
			window.localStorage.setItem(window.imgur.storage.local, type);
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

	// Drag and drop zone
	let dropZone = document.body.querySelector('#imgur-drop-zone');

	// Drag and drop upload
	if (dropZone !== null) {
		dropZone.addEventListener('dragenter', function(e) {
			preventImgurDropZoneDefaults(e);
			highlightImgurDropZone();
		}, false);

		dropZone.addEventListener('dragleave', function(e) {
			preventImgurDropZoneDefaults(e);
			highlightImgurDropZone(false);
		}, false);

		dropZone.addEventListener('dragover', function(e) {
			preventImgurDropZoneDefaults(e);
			highlightImgurDropZone();
		}, false);

		dropZone.addEventListener('drop', function(e) {
			let element = preventImgurDropZoneDefaults(e);
			highlightImgurDropZone(false);

			let button = element.querySelector('.imgur-button');

			if (button !== null) {
				let attribute = button.getAttribute('data-add-output');
				addOutput = (attribute === null || attribute === 'true');
			}

			// Upload images
			uploadImagesToImgur(e.dataTransfer.files, {
				output: addOutput
			});
		}, false);
	}

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
		let field = e.target;

		if (!field.matches('.imgur-output-field')) {
			return;
		}

		let wrapper = field.closest('dl');
		let cssClass = 'hidden';

		wrapper.classList.toggle(cssClass, (
			field.value.trim().length <= 0 && !wrapper.classList.contains(cssClass)
		));
	});

	// Resize output fields in posting editor panel
	window.phpbb.resizeTextArea(jQuery('.imgur-output-field'));

	// Add generated output in posting editor panel
	try {
		if (window.imgur.storage.enabled) {
			let outputType = getOutputType();

			// Restore user preference
			if (document.body.querySelector('#imgur-image') !== null) {
				document.body.querySelectorAll('.imgur-output-select').forEach(function(item) {
					if (!item) {
						return;
					}

					if (item.value === outputType) {
						return;
					}

					let option = item.querySelector('[value="' + outputType + '"]');

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

					item.dispatchEvent(evt);
				});
			}

			// Delete output if page doesn't have the fields to do so
			if (document.body.querySelectorAll('#imgur-panel .imgur-output-field').length <= 0) {
				window.sessionStorage.removeItem(window.imgur.storage.session);
			}

			fillOutputFields();
		}
	} catch (ex) {
		errors.push(ex.message);
	}

	showImgurErrors(errors);
})();
