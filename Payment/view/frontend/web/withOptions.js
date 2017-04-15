// 2017-03-03
// @see Df_GingerPaymentsBase/main
// https://github.com/mage2pro/ginger-payments-base/blob/0.1.1/view/frontend/web/main.js?ts=4
// @see Dfe_AllPay/main
// https://github.com/mage2pro/allpay/blob/1.1.37/view/frontend/web/main.js?ts=4
define([
	'df', 'Df_Core/my/redirectWithPost', 'Df_Payment/custom', 'jquery', 'ko'
], function(df, redirectWithPost, parent, $, ko) {'use strict'; return parent.extend({
	defaults: {df: {
		// 2017-03-02
		// @used-by Df_Payment/main
		// https://github.com/mage2pro/core/blob/2.4.21/Payment/view/frontend/web/template/main.html#L36-L38		
		formTemplate: 'Df_Payment/withOptions'
		,test: {showBackendTitle: false}
	}},
	/**
	 * 2016-08-08
	 * 2017-03-01
	 * Задаёт набор передаваемых на сервер при нажатии кнопки «Place Order» данных.
	 * @override
	 * @see mage2pro/core/Payment/view/frontend/web/mixin.js::dfData()
	 * @used-by mage2pro/core/Payment/view/frontend/web/mixin.js::getData()
	 * https://github.com/mage2pro/core/blob/2.0.21/Payment/view/frontend/web/mixin.js?ts=4#L208-L225
	 * @see \Dfe\AllPay\Method::II_OPTION
	 * https://github.com/mage2pro/allpay/blob/1.1.32/Method.php?ts=4#L126
	 * @returns {Object}
	 */
	dfData: function() {return df.o.merge(this._super(), df.clean({
		option: this.postProcessOption(this.option())
	}));},
	/**
	 * 2017-03-04
	 * @override
	 * @see Df_Payment/custom::initialize()
	 * @returns {Object}
	*/
	initialize: function() {
		this._super();
		// 2017-03-05
		// @used-by dfData()
		// @used-by Df_Payment/withOptions
		// https://github.com/mage2pro/core/blob/2.0.36/Payment/view/frontend/web/template/withOptions.html?ts=4#L12
		// @used-by Df_GingerPaymentsBase/main::dfData()
		// https://github.com/mage2pro/ginger-payments-base/blob/0.2.3/view/frontend/web/main.js?ts=4#L65
		// @used-by Dfe_AllPay/main::iPlans()
		// https://github.com/mage2pro/allpay/blob/1.2.0/view/frontend/web/main.js?ts=4#L50
		// @used-by Dfe_AllPay/one-off/simple
		// https://github.com/mage2pro/allpay/blob/1.2.0/view/frontend/web/template/one-off/simple.html?ts=4#L10
		// @used-by Dfe_AllPay/plans
		// https://github.com/mage2pro/allpay/blob/1.2.0/view/frontend/web/template/plans.html?ts=4#L21
		this.option = ko.observable();
		// 2017-03-05
		// Пример кода для отладки:
		//this.option.subscribe(function(v) {
		//	debugger;
		//}, this);
		return this;
	},
	/**
	 * 2016-08-15
	 * @final
	 * @used-by woOptions()
	 * @used-by Dfe_AllPay/main::oneOffOptions()
	 * https://github.com/mage2pro/allpay/blob/1.1.40/view/frontend/web/main.js?ts=4#L103
	 * @returns {Object}
	 */
	options: function() {return this.config('options');},
	/**
	 * 2017-03-04
	 * Allows to add a control after an option.
	 * @see Df_Payment/null
	 * https://github.com/mage2pro/core/blob/2.0.35/Payment/view/frontend/web/template/null.html
	 * @used-by Df_Payment/withOptions
	 * https://github.com/mage2pro/core/blob/2.0.35/Payment/view/frontend/web/template/withOptions.html?ts=4#L20
	 * @see Df_GingerPaymentsBase/main::optionAfter()
	 * https://github.com/mage2pro/ginger-payments-base/blob/0.1.9/view/frontend/web/main.js?ts=4#L42-L52
	 * @param {String} v
	 * @returns {?String}
	 */
	optionAfter: function(v) {return null;},
	/**
	 * 2017-03-05
	 * @used-by dfData()
	 * @param {String} option
	 * @returns {?String}
	 */
	postProcessOption: function(option) {return option;},
	/**
	 * 2016-08-15
	 * @returns {Object[]}
	 */
	woOptions: function() {return $.map(this.options(), function(v, k) {return {label: v, value: k};});},
	/**
	 * 2017-04-15
	 * Формирует идентификатор для <input> на основе идентификатора опции.
	 * Используется только для сопоставления <input> и его <label>.
	 * @param {String} id
	 * @returns {String}
	 */
	woRadioId: function(id) {return [this.getCode(), 'option', id].join('-');},
});});
