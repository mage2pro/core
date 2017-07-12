// 2016-07-16
define([
	'./mixin', 'df', 'jquery', 'ko'
	,'Magento_Payment/js/model/credit-card-validation/credit-card-data'
	,'Magento_Payment/js/view/payment/cc-form'
], function(mixin, df, $, ko, cardData, parent) {'use strict'; return parent.extend(df.o.merge(mixin, {
	/**
	 * 2017-02-16
	 * @returns {String}
	 */
	creditCardExpYear2: function() {return this.creditCardExpYear().toString().slice(-2);}
	,defaults: {
		// 2016-11-12
		cardholder: ''
		,df: {
			card: {
				// 2016-09-28
				// @used-by mage2pro/core/Payment/view/frontend/web/template/card.html
				newTemplate: 'Df_Payment/card/new'
				/**
				 * 2016-11-10
				 * @used-by prefill()
				 *
				 * 2016-11-13
				 * Использовать в качестве значения строку безопаснее, чем число.
				 * В частности, 2Checkout ожидает CVV именно в виде строки.
				 * Если передать число, то будет сбой: «TypeError: e.cvv.replace is not a function»
				 * потому что 2Checkout обрабатывает значение так:
				 * e.cvv = e.cvv.replace(/[^0-9]+/g,'');
				 */
				,prefill: {cvv: '111'}
				// 2016-11-12 Требовать ли от плательщика указания имени владельца банковской карты.
				// 2017-06-13
				// Следующие модули всегда требуют указания имени плательщика:
				// Moip: https://github.com/mage2pro/moip/blob/0.5.2/view/frontend/web/main.js#L5
				// Omise: https://github.com/mage2pro/omise/blob/1.4.7/view/frontend/web/main.js?ts=4#L11
				// Paymill: https://github.com/mage2pro/paymill/blob/0.1.1/view/frontend/web/main.js?ts=4#L8
				//
				// У следующих модулей требование указания имени плательщика определяется администратором:
				// Stripe: https://github.com/mage2pro/stripe/blob/1.9.19/etc/adminhtml/system.xml#L248-L263
				// Выбор администратора передаётся с сервера в браузер методом
				// @see \Df\Payment\ConfigProvider\BankCard::config()
				// https://github.com/mage2pro/core/blob/2.7.9/Payment/ConfigProvider/BankCard.php#L20
				// 		'requireCardholder' => $s->requireCardholder()
				,requireCardholder: false
			},
			// 2016-08-06
			// @used-by Df_Payment/main
			// https://github.com/mage2pro/core/blob/2.4.21/Payment/view/frontend/web/template/main.html#L36-L38
			formTemplate: 'Df_Payment/card'
		}
	},
	/**
	 * 2017-06-13
	 * @returns {String=}
	 */
	dfCard_customTemplate_afterCardholder: function() {return null;},
	dfCardNumberId: function() {return this.fid('cc_number');},
	dfCardVerificationId: function() {return this.fid('cc_cid');},
	/**
	 * 2016-08-06
	 * 2017-03-01
	 * Задаёт набор передаваемых на сервер при нажатии кнопки «Place Order» данных.
	 * @override
	 * @see mage2pro/core/Payment/view/frontend/web/mixin.js::dfData()
	 * @used-by mage2pro/core/Payment/view/frontend/web/mixin.js::getData()
	 * https://github.com/mage2pro/core/blob/2.0.21/Payment/view/frontend/web/mixin.js?ts=4#L208-L225
	 * @returns {Object}
	 */
	dfData: function() {return {token: this.token};},
	/**
	 * 2016-08-04
	 * @override
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Payment/view/frontend/web/js/view/payment/cc-form.js#L98-L104
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L203-L208
	 * @returns {String}
	 */
	getCode: function() {return this.item.method;},
	/**
	 * 2016-08-16
	 * @override
	 * @see Df_Payment/mixin::dfFormCssClasses()
	 * https://github.com/mage2pro/core/blob/2.0.25/Payment/view/frontend/web/mixin.js?ts=4#L165
	 * @used-by Df_Payment/mixin::dfFormCssClassesS()
	 * https://github.com/mage2pro/core/blob/2.0.25/Payment/view/frontend/web/mixin.js?ts=4#L171
	 * @returns {String[]}
	 */
	dfFormCssClasses: function() {return mixin.dfFormCssClasses.call(this).concat(['df-card']);},
	/**
	 * 2016-08-23
	 * @override
	 * @see Magento_Payment/js/view/payment/cc-form::initialize()
	 * https://github.com/magento/magento2/blob/2.1.5/app/code/Magento/Payment/view/frontend/web/js/view/payment/cc-form.js#L48-L96
	 * @see Dfe_CheckoutCom/main::initialize()
	 * https://github.com/mage2pro/checkout.com/blob/1.3.5/view/frontend/web/main.js#L39-L53
	 * @see Dfe_Omise/main::initialize()
	 * https://github.com/mage2pro/omise/blob/1.8.5/view/frontend/web/main.js#L17-L28
	 * @see Dfe_Paymill/main::initialize()
	 * https://github.com/mage2pro/paymill/blob/1.4.4/view/frontend/web/main.js#L16-L29
	 * @see Dfe_Klarna/main::initialize()
	 * @see Dfe_SecurePay/main::initialize()
	 * https://github.com/mage2pro/securepay/blob/1.5.3/view/frontend/web/main.js#L68-L86
	 * @see Dfe_Square/main::initialize()
	 * https://github.com/mage2pro/square/blob/1.1.2/view/frontend/web/main.js#L125-L158
	 * @see Dfe_Stripe/main::initialize()
	 * https://github.com/mage2pro/stripe/blob/1.9.8/view/frontend/web/main.js#L32-L43
	 * @see Dfe_TwoCheckout/main::initialize()
	 * https://github.com/mage2pro/2checkout/blob/1.3.5/view/frontend/web/main.js#L61-L73
	 * @returns {Object}
	 */
	initialize: function() {
		this._super();
		mixin.initialize.apply(this);
		this.cards = this.config('cards');
		// 2017-02-10
		// Свойство «cards» передаёт браузеру
		// только один из потомков Df\Payment\ConfigProvider\BankCard:
		// Df\StripeClone\ConfigProvider, в то время как другой потомок,
		// Dfe\SecurePay\ConfigProvider, это свойство не передаёт.
		this.hasCards = this.cards && this.cards.length;
		this.newCardId = 'new';
		this.currentCard = ko.observable(!this.hasCards ? this.newCardId : this.cards[0].id);
		this.isNewCardChosen = ko.computed(function() {
			return this.newCardId === this.currentCard();
		}, this);
		// 2016-11-12
		this.cardholder.subscribe(function(v) {cardData.cardholder = v;});
		// 2016-11-10
		// Prefill should work only in the Test mode.
		if (this.isTest()) {
			// 2016-11-10
			// «Prefill the Payment Form with Test Data?»			
			/** @type {*} */
			var prefill = this.config('prefill');
			if (prefill) {
				this.prefill(prefill);				
			}
		}		
		return this;
	},
	/**
	 * 2016-11-12
	 * 2017-07-12 The method should return `this` because it is used in a chain:
	 *	this._super()
	 *		.initObservable()
	 *		.initModules()
	 *		.initStatefull()
	 *		.initLinks()
	 *		.initUnique();
	 * @used-by Magento_Ui/js/lib/core/element/element::initialize()
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.3/app/code/Magento/Ui/view/base/web/js/lib/core/element/element.js#L104
	 * @override
	 * @see Magento_Payment/js/view/payment/cc-form::initObservable()
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.3/app/code/Magento/Payment/view/frontend/web/js/view/payment/cc-form.js#L29-L45
	 * @see Dfe_Moip/main::initObservable()
	 * https://github.com/mage2pro/moip/blob/0.5.6/view/frontend/web/main.js#L33
	 * @see Dfe_Square/main::initObservable()
	 * https://github.com/mage2pro/square/blob/1.1.13/view/frontend/web/main.js#L165
	 * @returns {Element} Chainable
	*/
	initObservable: function() {this._super(); this.observe(['cardholder']); return this;},	
	/**
	 * 2016-11-10
	 * @used-by initialize()
	 * @see Dfe_CheckoutCom/main::prefill()
	 * https://github.com/mage2pro/checkout.com/blob/1.3.21/view/frontend/web/main.js#L140-L154
	 * @param {*} d
	 */
	prefill: function(d) {
		// 2016-11-12
		if (this.requireCardholder()) {
			this.cardholder('DMITRY FEDYUK');
		}
		this.creditCardNumber(d);
		this.prefillWithAFutureData();
		this.creditCardVerificationNumber(this.df.card.prefill.cvv);
	},	
	/**
	 * 2016-08-26
	 * @used-by prefill()
	 * @see https://github.com/mage2pro/paymill/blob/0.1.6/view/frontend/web/main.js?ts=4#L92-L105
	 * http://stackoverflow.com/a/6002276
	 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/getFullYear
	 */
	prefillWithAFutureData: function() {
		this.creditCardExpMonth(7);
		this.creditCardExpYear(1 + new Date().getFullYear());
	},
	/**
	 * 2017-02-16
	 * @final
	 * @used-by prefill()
	 * @returns {Boolean}
	 */
	requireCardholder: function() {return(
		this.df.card.requireCardholder || this.config('requireCardholder')
	);},
	/**
	 * 2016-08-06
	 * @override
	 * @see mage2pro/core/Payment/view/frontend/web/mixin.js
	 * @returns {Boolean}
	*/
	validate: function() {
		/** @type {Boolean} */
		var result = !this.isNewCardChosen() || !!this.selectedCardType();
		if (!result) {
			this.showErrorMessage('It looks like you have entered an incorrect bank card number.');
		}
		return result && this._super();
	}
}));});
