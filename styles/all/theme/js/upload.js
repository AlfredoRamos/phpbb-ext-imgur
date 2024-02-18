/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@proton.me>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

(() => {
	'use strict';

	// Global variables
	let errors = [];
	let addOutput = true;

	// Show image selection window
	document.body.addEventListener('click', (e) => {
		const image = document.body.querySelector('#imgur-image');
		const button = e.target.closest('.imgur-button-upload');

		if (!image || !button) {
			return;
		}

		const attribute = button.getAttribute('data-add-output');
		addOutput = attribute === null || attribute === 'true';
		const evt = new MouseEvent('click', {
			bubbles: true,
			cancelable: true,
		});
		image.dispatchEvent(evt);
	});

	// Upload images
	document.body.addEventListener('change', (e) => {
		const imgurImage = e.target;

		if (!imgurImage.matches('#imgur-image')) {
			return;
		}

		// Upload images
		window.imgur.upload(imgurImage.files, {
			image: imgurImage,
			output: addOutput,
		});
	});

	// Imgur dropdown menu
	document.body.addEventListener('contextmenu', (e) => {
		const wrapper = e.target.closest('.imgur-wrapper');

		if (!wrapper) {
			return;
		}

		const select = wrapper.querySelector('.imgur-output-select');

		if (!select) {
			return;
		}

		e.preventDefault();
		select.classList.remove('tw-hidden');
		select.focus();
	});

	// Update output type
	document.body.addEventListener('change', (e) => {
		const select = e.target;

		if (!select.matches('.imgur-output-select')) {
			return;
		}

		const image = document.body.querySelector('#imgur-image');
		const type = select.value.trim();

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
	document.body.addEventListener('click', (e) => {
		const select = e.target.closest('.imgur-output-select');

		if (!select || (select !== null && !select.contains(e.target))) {
			document.body
				.querySelectorAll('.imgur-output-select')
				.forEach((item) => {
					if (!item) {
						return;
					}

					item.classList.add('tw-hidden');
				});
			return;
		}
	});

	// Drag and drop zone
	const dropZone = document.body.querySelector('.imgur-drop-zone');

	// Drag and drop upload
	if (dropZone !== null) {
		dropZone.addEventListener(
			'dragenter',
			(e) => {
				window.imgur.preventDropZoneDefaults(e);
				window.imgur.highlightDropZone();
			},
			false
		);

		dropZone.addEventListener(
			'dragleave',
			(e) => {
				window.imgur.preventDropZoneDefaults(e);
				window.imgur.highlightDropZone(false);
			},
			false
		);

		dropZone.addEventListener(
			'dragover',
			(e) => {
				window.imgur.preventDropZoneDefaults(e);
				window.imgur.highlightDropZone();
			},
			false
		);

		dropZone.addEventListener(
			'drop',
			(e) => {
				const element = window.imgur.preventDropZoneDefaults(e);
				window.imgur.highlightDropZone(false);
				const button = element.querySelector('.imgur-button-upload');

				if (button !== null) {
					const attribute = button.getAttribute('data-add-output');
					addOutput = attribute === null || attribute === 'true';
				}

				// Upload images
				window.imgur.upload(e.dataTransfer.files, {
					output: addOutput,
				});
			},
			false
		);
	}

	// Copy output field text to message
	document.body.addEventListener('click', (e) => {
		const button = e.target.closest('.imgur-button-paste');

		if (!button) {
			return;
		}

		const field = button.parentNode.querySelector('.imgur-output-field');

		if (!field) {
			return;
		}

		const bbcode = field.value.trim();

		if (bbcode.length <= 0) {
			return;
		}

		// Add BBCode to content
		window.insert_text(bbcode);
	});

	// Show output fields only when needed
	document.body.addEventListener('change', (e) => {
		const field = e.target;

		if (!field.matches('.imgur-output-field')) {
			return;
		}

		const wrapper = field.closest('.imgur-field-wrapper');
		const cssClass = 'tw-hidden';

		wrapper.classList.toggle(
			cssClass,
			field.value.trim().length <= 0 &&
				!wrapper.classList.contains(cssClass)
		);
	});

	// Resize output fields in posting editor panel
	window.phpbb.resizeTextArea(jQuery('.imgur-output-field'));

	// Add generated output in posting editor panel
	try {
		if (window.imgur.storage.enabled) {
			const outputType = window.imgur.getOutputType();

			// Restore user preference
			if (document.body.querySelector('#imgur-image') !== null) {
				document.body
					.querySelectorAll('.imgur-output-select')
					.forEach((item) => {
						if (!item) {
							return;
						}

						if (item.value === outputType) {
							return;
						}

						const option = item.querySelector(
							'[value="' + outputType + '"]'
						);

						if (!option) {
							return;
						}

						option.selected = true;
						const evt = new Event('change', {
							bubbles: true,
							cancelable: true,
						});
						item.dispatchEvent(evt);
					});
			}

			// Delete output if page doesn't have the fields to do so
			if (
				document.body.querySelectorAll(
					'#imgur-panel .imgur-output-field'
				).length <= 0
			) {
				window.sessionStorage.removeItem(window.imgur.storage.session);
			}

			window.imgur.fillOutputFields();
		}
	} catch (ex) {
		errors.push(ex.message);
	}

	window.imgur.showErrors(errors);
})();
