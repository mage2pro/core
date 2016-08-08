define(['jquery', 'df-lodash'], function($, _) {return {
	/**
	 * 2016-08-06
	 * Аналог моей функции PHP df_cc_clean()
	 * @param {String} glue
	 * @param {Array} a
	 * @returns {String}
	 */
	ccClean: function(glue, a) {return this.clean(a).join(glue);},
	/**
	 * 2016-06-03
	 * Аналог моей функции PHP df_clean()
	 * http://underscorejs.org/#without
	 *
	 * 2016-08-08
	 * В библиотеках Underscope и Lodash есть ещё функция compact,
	 * но она удаляет больше, чем наша: все значения, которые приводятся к false
	 * (в том числе false и 0, что нам не надо).
	 * http://underscorejs.org/#compact
	 * https://lodash.com/docs#compact
	 *
	 * В Lodash тоже есть такая функция: https://lodash.com/docs#without
	 *
	 * @param {Array} a
	 * @returns {Array}
	 */
	clean: function(a) {return _.without(a, '', null, undefined, []);}
};});