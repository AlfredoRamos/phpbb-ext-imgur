/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

(function($) {
	'use strict';

	// Insert text at cursor position
	// Modified version of Mathias Bynens' code
	// https://gist.github.com/mathiasbynens/326491
	$.fn.insertAtCaret = function($text) {
		return this.each(function() {
			if (document.selection) {
				// Internet Explorer
				this.focus();
				var $selection = document.selection.createRange();
				$selection.text = $text;
				this.focus();
			} else if (this.selectionStart || this.selectionStart === '0') {
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
})(jQuery);

function showImgurErrors(errors) {
	// Ensure settings exist
	if (typeof window.$imgur === 'undefined') {
		var $imgur = {};
	} else {
		$imgur = window.$imgur;
	}

	// Extend settings
	$imgur = $.extend({
		lang: {
			error: 'Error',
			image_too_big: 'The image <samp>{file}</samp> is <code>{size}</code> MiB and it should be less that <code>{max_size}</code> MiB.',
			no_images: 'There are no images to upload.',
			upload_progress: '{percentage}% ({loaded} / {total} MiB)'
		}
	}, $imgur);

	// Show a phpBB alert with the errors
	if (errors.length > 0) {
		var message = '';

		for (var i = 0; i < errors.length; i++) {
			message += errors[i];

			if (i < (errors.length - 1)) {
				message += '<br />';
			}
		}

		if (message.length > 0) {
			phpbb.alert($imgur.lang.error, message);
		}
	}
}
