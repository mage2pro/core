/**
 * 2017-03-02
 * @see Df_GingerPaymentsBase https://github.com/mage2pro/ginger-payments-base/tree/1.1.3/view/frontend/web/main.js
 * @see Dfe_AllPay/main https://github.com/mage2pro/allpay/tree/1.6.7/view/frontend/web/main.js
 * @see Dfe_IPay88/main https://github.com/mage2pro/ipay88/tree/1.1.3/view/frontend/web/main.js
 * @see Dfe_Robokassa/main https://github.com/mage2pro/robokassa/tree/1.0.3/view/frontend/web/main.js
 * @see Dfe_YandexKassa/main: https://github.com/mage2pro/yandex-kassa/blob/0.1.1/view/frontend/web/main.js
 */
define([
	'df', 'df-lodash', 'Df_Core/my/redirectWithPost', 'Df_Payment/custom', 'ko'
], function(df, _, redirectWithPost, parent, ko) {'use strict';
/** 2017-09-06 @uses Class::extend() https://github.com/magento/magento2/blob/2.2.0-rc2.3/app/code/Magento/Ui/view/base/web/js/lib/core/class.js#L106-L140 */	
return parent.extend({
	defaults: {df: {
		// 2017-04-15
		// @used-by Df_Payment/main
		css: 'withOptions'
		// 2017-03-02
		// @used-by Df_Payment/main
		// https://github.com/mage2pro/core/blob/2.4.21/Payment/view/frontend/web/template/main.html#L36-L38		
		,formTemplate: 'Df_Payment/withOptions'
		/** 2017-09-09 @used-by Df_Payment/mixin::dfFormCssClasses() */
		,placeOrderButtonAlignment: 'center'
	}},
	/**
	 * 2016-08-08
	 * 2017-03-01
	 * 2017-07-26
	 * These data are submitted to the M2 server part
	 * as the `additional_data` property value on the «Place Order» button click:
	 * @used-by Df_Payment/mixin::getData():
	 *		getData: function() {return {additional_data: this.dfData(), method: this.item.method};},	
	 * https://github.com/mage2pro/core/blob/2.8.4/Payment/view/frontend/web/mixin.js#L224
	 * @override
	 * @see Df_Payment/mixin::dfData()
	 * https://github.com/mage2pro/core/blob/2.8.4/Payment/view/frontend/web/mixin.js#L130-L137
	 * @see Df_GingerPaymentsBase/main::dfData()
	 * https://github.com/mage2pro/ginger-payments-base/blob/1.1.3/view/frontend/web/main.js#L36-L38
	 * @returns {Object}
	 */
	dfData: function() {return df.o.merge(this._super(), df.clean({
		option: this.postProcessOption(this.option())
	}));},
	/**
	 * 2017-03-04
	 * @override
	 * @see Df_Payment/custom::initialize()
	 * @returns {exports}
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
	 * 2017-03-01
	 * 2017-09-17
	 * The `true` value means that the payment options need to be shown on the Magento side.
	 * The `false` value means that the payment options need to be shown on the allPay side.
	 * @returns {Boolean}
	 */
	needShowOptions: function() {return this.config('needShowOptions');},
	/**
	 * 2016-08-15
	 * 2017-09-19 @todo Improve the scenario when this.config('options') is empty.
	 * @final
	 * @used-by woOptions()
	 * @used-by Dfe_AllPay/main::oneOffOptions()
	 * https://github.com/mage2pro/allpay/blob/1.1.40/view/frontend/web/main.js?ts=4#L103
	 * @returns {Object|Array}
	 */
	options: function() {return this.config('options') || [];},
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
	 * 2017-09-19
	 * A text to be shown on the Magento checkout page instead of the payment options dialog.
	 * @used-by https://github.com/mage2pro/allpay/blob/1.1.32/view/frontend/web/template/one-off/simple.html?ts=4#L7-L12
	 * @returns {String}
	 */
	optionsDescription: function() {
		/** @type {Object|Array} */ var o = this.options();
		return (this.config('optionsDescription') || '').replace('{options}',
			_.values(_.isObject(o) ? o : _.map(o, function(v) {return v['label'];})).join(', ')
		);
	},
	/**
	 * 2017-03-05
	 * @used-by dfData()
	 * @param {String} option
	 * @returns {?String}
	 */
	postProcessOption: function(option) {return option;},
	/**
	 * 2016-08-15
	 * @used-by Df_Payment/withOptions.html:
	 *	<!-- ko template: {
	 *		data: {level: $data.m ? $data.m.level + 1 : 1, m: $data, items: woOptions()}
	 *		,name: woT('list')
	 *	} --><!-- /ko -->
	 * https://github.com/mage2pro/core/blob/2.12.5/Payment/view/frontend/web/template/withOptions.html#L10-L13
	 * @returns {Object[]}
	 */
	woOptions: function() {
		/** @type {Object|Array} */ var o = this.options();
		// 2017-09-19 https://lodash.com/docs/4.17.4#map
		return _.isArray(o) ? o : _.map(o, function(v, k) {return {label: v, value: k};});
	},
	/**
	 * 2017-04-15
	 * Формирует идентификатор для <input> на основе идентификатора опции.
	 * Используется только для сопоставления <input> и его <label>.
	 * @param {String} id
	 * @returns {String}
	 */
	woRadioId: function(id) {return [this.domPrefix(), 'option', id].join('-');},
	/**
	 * 2017-04-15
	 * @param {String} suffix
	 * @returns {String}
	 */
	woT: function(suffix) {return 'Df_Payment/withOptions/' + suffix;}
});});
