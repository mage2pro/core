define(['jquery', 'df-lodash'], function($, _) {return {
	/**
	 * 2016-08-08
	 * Клонирует объект.
	 * Сейчас никем не используется.
	 * @param {Object} o
	 * @returns {Object}
	 */
	clone: function(o) {return $.extend(true, {}, o);},
	/**
	 * 2017-04-04
	 * Возвращает false, если объект пуст {} либо равен null/undefined.
	 * @used-by Df_Payment/mixin::placeOrderInternal()
	 * @param {?Object} o
	 * @returns {Boolean}
	 */
	e: function(o) {return !o || !_.size(o);},
	/**
	 * 2016-02-27
	 * Аналог функции PHP array_intersect_key()
	 * http://php.net/manual/function.array-intersect-key.php
	 * http://underscorejs.org/#keys
	 * http://underscorejs.org/#intersection
	 * http://underscorejs.org/#pick
	 *
	 * 2016-08-08
	 * Сейчас никем не используется.
	 *
	 * https://lodash.com/docs#keys
	 * https://lodash.com/docs#intersection
	 * https://lodash.com/docs#pick
	 *
	 * @param {Object} a1
	 * @param {Object} a2
	 * @returns {Object}
	 */
	intersectKeys: function(a1, a2) {return _.pick(a1, _.intersection(_.keys(a1), _.keys(a2)));},
	/**
	 * 2016-08-08
	 * Поля объекта a будут в результате перекрыты одноимёнными полями объекта b.
	 * Объект a при этом не меняется.
	 * @param {Object} a
	 * @param {Object} b
	 * @returns {Object}
	 */
	merge: function(a, b) {return $.extend(true, {}, a, b);}
};});