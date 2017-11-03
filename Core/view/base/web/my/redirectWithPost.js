/**
 * 2016-07-05
 * 2017-11-03
 * Previously, I used a simple solution from here: http://stackoverflow.com/a/133997
 * https://github.com/mage2pro/core/blob/3.2.27/Core/view/base/web/my/redirectWithPost.js#L1-L28
 * I have dropped it, because it does not support nested properties,
 * and I need nested properties for my AlphaCommerceHub payment extension:
 * http://developer.alphacommercehub.com.au/docs/alphahpp-#product-line-information
 * https://mage2.pro/c/extensions/alphacommercehub
 * https://github.com/mage2pro/alphacommercehub
 * Now I use the solution from here: https://github.com/mgalante/jquery.redirect/tree/v1.1.1
 */
define(['jquery'], function($) {
	/**
	 * 2017-11-03
	 * https://github.com/mgalante/jquery.redirect/blob/v1.1.1/jquery.redirect.js#L100-L122
	 * @used-by iterate()
	 * @param {String} name
	 * @param {String} value
	 * @param {String[]} prefixes
	 * @returns {jQuery} HTMLInputElement
	 */
	var input = function (name, value, prefixes) {
		var prefix;
		if (prefixes.length > 0) {
			prefix = prefixes[0];
			var i;
			for (i = 1; i < prefixes.length; i += 1) {
				prefix += '[' + prefixes[i] + ']';
			}
			name = prefix + '[' + name + ']';
		}
		return $('<input>').attr({name: name, type: 'hidden', value: value});
	};
	/**
	 * 2017-11-03
	 * https://github.com/mgalante/jquery.redirect/blob/v1.1.1/jquery.redirect.js#L124-L135
	 * @used-by iterate()
	 * @used-by result
	 * @param {jQuery} $form HTMLFormElement
	 * @param {Object} p
	 * @param {String[]} prefixes
	 */
    var iterate = function($form, p, prefixes) {
        Object.keys(p).forEach(function(i) {
            if ('object' !== typeof p[i]) {
            	$form.append(input(i, p[i], prefixes));
            }
            else {
				var newPrefixes = prefixes.slice();
				newPrefixes.push(i);
				iterate($form, p[i], newPrefixes);
            }
        });
    };
	/**
	 * 2017-11-03
	 * https://github.com/mgalante/jquery.redirect/blob/v1.1.1/jquery.redirect.js#L137-L150
	 * @used-by clean()
	 * @used-by result
	 * @param {Object} p
	 * @returns {Object}
	 */
	var clean = function(p) {
		var names = Object.getOwnPropertyNames(p);
		for (var i = 0; i < names.length; i++) {
			var name = names[i];
			if (p[name] === null || p[name] === undefined) {
				delete p[name];
			}
			else if ('object' === typeof p[name]) {
				p[name] = clean(p[name]);
			}
			else if (p[name].length < 1) {
				delete p[name];
			}
		}
		return p;
	};
	/**
	 * 2016-07-05
	 * @param {String} url
	 * @param {?Object} p
	 */
	return function(url, p) {
		var $form = $('<form>').attr({action: url, method: 'post'});
		iterate($form, clean(p), []);
		$form.appendTo('body').submit();
	};
});