/**
 * 2019-08-31
 * We need to toggle a custom class (`dfHasValue`) when an input has or has not a value.
 * A simple jQuery solution:
 *		$('input').each(function(i, e) {inputChange(e)}).on('change paste keyup', inputChange);
 * But it does not work with Knockout bindings:
 * 1) «Change event doesn't trigger when Knockout updates value»: https://stackoverflow.com/a/25733267
 * 2) «Trigger change event on value binding»: https://github.com/knockout/knockout/issues/1083
 * That is why I use another solution.
 */
require(['jquery', 'ko'], function($, ko) {
	var c = 'dfHasValue';
	(function() {
		// 2019-08-31 This code is needed for the proper initial state handling.
		var f = function(name) {
			var h = ko.bindingHandlers[name];
			var init = h.init;
			h.init = function(e) {init.apply(h, arguments); inputChange(e);};
		};
		f('textInput'); f('value');
	})();
	ko.bindingHandlers.dfHasValue = {init: function(e, nu, aF) {
		var a = aF(); var $e = $(e);
		// 2019-08-30 We work with the `value` and `textInput` bindings.
		// 2019-08-31 I changed `'' !==` to `!!` to properly handle `null`.
		(a.value || a.textInput).subscribe(function(v) {$e.toggleClass(c, !!v)});
	}};
	// 2019-08-31
	// 1) I changed `'' !==` to `!!` to properly handle `null`.
	// 2) «How to determine if Javascript object is an event?» https://stackoverflow.com/a/1458929
	var inputChange = function(e) {
		var $e = $(e.target || e);
		$e.toggleClass(c, !!$e.val());
	};
	require(['domReady!'], function() {
		$('input').each(function(i, e) {inputChange(e)}).on('change paste keyup', inputChange);
	});
});