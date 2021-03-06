/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

'use strict';

// Polyfill for NodeList.forEach()
// https://developer.mozilla.org/en-US/docs/Web/API/NodeList/forEach#Polyfill
if (!NodeList.prototype.forEach) {
	NodeList.prototype.forEach = Array.prototype.forEach;
}

// Polifyll for Array.isArray()
// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/isArray#Polyfill
if (!Array.isArray) {
	Array.isArray = function(arg) {
		return Object.prototype.toString.call(arg) === '[object Array]';
	};
}

// Polyfill for Object.entries()
// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/entries#Polyfill
if (!Object.entries) {
	Object.entries = function( obj ){
		let ownProps = Object.keys(obj);
		let i = ownProps.length;
		let resArray = new Array(i);

		while (i--) {
			resArray[i] = [ownProps[i], obj[ownProps[i]]];
		}

		return resArray;
	};
}

// Polyfill for Element.matches()
// https://developer.mozilla.org/en-US/docs/Web/API/Element/matches#Polyfill
if (!Element.prototype.matches) {
	Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
}

// Polyfill for Element.closest()
// https://developer.mozilla.org/en-US/docs/Web/API/Element/closest#Polyfill
if (!Element.prototype.closest) {
	Element.prototype.closest = function(s) {
		let el = this;

		do {
			if (el.matches(s)) {
				return el;
			}

			el = el.parentElement || el.parentNode;
		} while (el !== null && el.nodeType === 1);

		return null;
	};
}

// Polyfill for Object.assign()
// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/assign#Polyfill
if (typeof Object.assign !== 'function') {
	Object.defineProperty(Object, 'assign', {
		value: function assign(target, varArgs) {
			if (target === null || typeof target === 'undefined') {
				throw new TypeError('Cannot convert undefined or null to object');
			}

			let to = Object(target);

			for (let index = 1; index < arguments.length; index++) {
				let nextSource = arguments[index];

				if (nextSource !== null && typeof nextSource !== 'undefined') {
					for (let nextKey in nextSource) {
						if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
							to[nextKey] = nextSource[nextKey];
						}
					}
				}
			}
			return to;
		},
		writable: true,
		configurable: true
	});
}
