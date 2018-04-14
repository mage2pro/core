/**
 * 2017-03-02
 * @see Df_GingerPaymentsBase https://github.com/mage2pro/ginger-payments-base/tree/1.1.3/view/frontend/web/main.js
 * @see Dfe_AllPay/main https://github.com/mage2pro/allpay/tree/1.6.7/view/frontend/web/main.js
 * @see Dfe_IPay88/main https://github.com/mage2pro/ipay88/tree/1.1.3/view/frontend/web/main.js
 * @see Dfe_Robokassa/main https://github.com/mage2pro/robokassa/tree/1.0.3/view/frontend/web/main.js
 * @see Dfe_YandexKassa/main: https://github.com/mage2pro/yandex-kassa/blob/1.0.1/view/frontend/web/main.js
 */
define([
	'./mixin', 'df', 'df-lodash', 'Df_Payment/custom', 'ko'
], function(mixin, df, _, parent, ko) {'use strict';
/** 2017-09-06 @uses Class::extend() https://github.com/magento/magento2/blob/2.2.0-rc2.3/app/code/Magento/Ui/view/base/web/js/lib/core/class.js#L106-L140 */	
return parent.extend({
	/**
	 * 2017-09-20
	 * The `.withOptions-psp` style is used here:
	 * 		.df-payment-method.withOptions-psp > .payment-method-title {padding-bottom: 10px;}
	 * https://github.com/mage2pro/core/blob/2.12.9/Payment/view/frontend/web/less/withOptions.less#L1-L3
	 * @final
	 * @override
	 * @see Df_Payment/mixin::containerCss()
	 * @used-by Df_Payment/main.html
	 * 		attr: {'class': ['payment-method df-payment-method', domPrefix(), containerCss()].join(' ')}
	 * https://github.com/mage2pro/core/blob/2.12.8/Payment/view/frontend/web/template/main.html#L3		
	 * @returns {string}
	 */
	containerCss: function() {return df.a.ccClean(', ', [mixin.containerCss.apply(this), 'withOptions'
		/**
		 * 2017-09-21
		 * «Where to ask for a payment option?» (`magento` or `psp`).
		 * @see \Df\Payment\ConfigProvider::configOptions():
		 * 		'needShowOptions' => Options::needShow($s)
		 * https://github.com/mage2pro/core/blob/2.12.14/Payment/ConfigProvider.php#L186-L187
		 */
		,'withOptions-' + (this.needShowOptions() ? 'magento' : 'psp')
		/**
		 * 2017-09-21
		 * «Payment options display mode» (`images` or `text`).
		 * @see \Df\Payment\ConfigProvider::configOptions():
		 * 		'optionsDisplayMode' => $s->v('optionsDisplayMode', null, DisplayMode::IMAGES)
		 * https://github.com/mage2pro/core/blob/2.12.14/Payment/ConfigProvider.php#L194-L201
		 * It is used by:
		 * *) iPay88: https://github.com/mage2pro/ipay88/blob/1.4.1/view/frontend/web/main.less#L19-L23
		 * *) Robokassa: https://github.com/mage2pro/robokassa/blob/1.3.0/view/frontend/web/main.less#L9-L13
		 */
		,'withOptions-displayMode-' + this.config('optionsDisplayMode')
	]);},
	defaults: {df: {
		// 2017-03-02
		// @used-by Df_Payment/main
		// https://github.com/mage2pro/core/blob/2.4.21/Payment/view/frontend/web/template/main.html#L36-L38		
		formTemplate: 'Df_Payment/withOptions'
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
	dfData: function() {return _.assign(this._super(), df.clean({option: this.optionFinal()}));},
	/**
	 * 2017-03-04
	 * @override
	 * @see Df_Payment/custom::initialize()
	 * @used-by <...>
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
		var canPlaceOrder = this.canPlaceOrder;
		/**
		 * 2017-09-20
		 * @override
		 * @see Df_Payment/mixin::canPlaceOrder()
		 * @used-by Df_Payment/main.html:
		 *		<button <...> data-bind="<...> enable: canPlaceOrder">
		 *			<span data-bind="df_i18n: 'Place Order'"></span>
		 *		</button>
		 * @see Dfe_AllPay/main::canPlaceOrder()
		 */
		this.canPlaceOrder = ko.computed(function() {return (canPlaceOrder.call(this) && (
			!this.needShowOptions() || this.optionFinal()
		));}, this);
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
	 * @final
	 * The `true` value means that the payment options need to be shown on the Magento side.
	 * The `false` value means that the payment options need to be shown on the allPay side.
	 * @used-by containerCss()
	 * @used-by Df_Payment/withOptions.html:
	 *		<!--ko if: !needShowOptions() -->
	 *		<!-- 2017-09-19
	 *		It shows a simple text like: «The following payment options are available: <...>»
	 *		instead of the payment options dialog. -->
	 *			<div data-bind='html: optionsDescription()'></div>
	 *		<!--/ko-->
	 *		<!--ko if: needShowOptions() -->
	 *			<!--ko if: config('optionsPrompt') -->
	 *				<h4 class='df-please-select-an-option' data-bind="html: config('optionsPrompt')"/>
	 *			<!--/ko-->
	 *			<!-- ko template: {
	 *				data: {level: $data.m ? $data.m.level + 1 : 1, m: $data, items: options()}
	 *				,name: woT('list')
	 *			} --><!-- /ko -->
	 *		<!--/ko-->
	 * https://github.com/mage2pro/core/blob/2.12.8/Payment/view/frontend/web/template/withOptions.html#L12-L26
	 * @returns {Boolean}
	 */
	needShowOptions: function() {return this.config('needShowOptions');},
	/**
	 * 2016-08-15
	 * 2017-09-19 @todo Improve the scenario when this.config('options') is empty.
	 * @final
	 * @used-by optionsDescription()
	 * @used-by Df_Payment/withOptions.html:
	 *	<!-- ko template: {
	 *		data: {level: $data.m ? $data.m.level + 1 : 1, m: $data, items: options()}
	 *		,name: woT('list')
	 *	} --><!-- /ko -->
	 * https://github.com/mage2pro/core/blob/2.12.5/Payment/view/frontend/web/template/withOptions.html#L10-L13
	 * @used-by Dfe_AllPay/main::oneOffOptions()
	 * https://github.com/mage2pro/allpay/blob/1.1.40/view/frontend/web/main.js?ts=4#L103
	 * @returns {{label: String, value: String, children: ?Array}[]}
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
	optionsDescription: function() {return (this.config('optionsDescription') || '').replace(
		'{options}', _.map(this.options(), 'label').join(', ')
	);},
	/**
	 * 2017-09-20 The «undefined» value is used by the Dfe_AllPay/one-off/simple.html template.
	 * @final
	 * @used-by dfData()
	 * @used-by initialize()
	 * @returns {null}
	 */
	optionFinal: function() {var r = this.option(); return 'undefined' === r ? null : r;},
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
