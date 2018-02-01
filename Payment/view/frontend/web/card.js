/**
 * 2016-07-16
 * @see Df_StripeClone/main https://github.com/mage2pro/core/blob/2.9.2/StripeClone/view/frontend/web/main.js
 * @see Dfe_SecurePay/main  https://github.com/mage2pro/securepay/blob/1.5.11/view/frontend/web/main.js
 */
define([
	'./mixin', 'df', 'Df_Payment/billingAddressChange', 'jquery', 'ko'
	,'Magento_Payment/js/model/credit-card-validation/credit-card-data'
	,'Magento_Payment/js/view/payment/cc-form'
	/**
	 * 2017-10-18
	 * Note 1.
	 * «JavaScript Unicode 8.0 Normalization - NFC, NFD, NFKC, NFKD»: https://github.com/walling/unorm
	 * Note 2.
	 * `The ineligible characters should be automatically replaced by the corresponding eligible ones
	 * while prefilling the cardholder's name
	 * (if «Prefill the cardholder's name from the billing address?» option is enabled)`:
	 * https://github.com/mage2pro/core/issues/37#issuecomment-337537967
	 */
	,'df-unorm'
	// 2017-07-12 It supports the following syntax in the templates:
	// 	'data-validate': JSON.stringify({'validate-card-number': '#' + fid('cc_type')})
	,'Magento_Payment/js/model/credit-card-validation/validator'
], function(mixin, df, baChange, $, ko, cardData, parent) {'use strict';
/** 2017-09-06 @uses Class::extend() https://github.com/magento/magento2/blob/2.2.0-rc2.3/app/code/Magento/Ui/view/base/web/js/lib/core/class.js#L106-L140 */	
return parent.extend(df.o.merge(mixin, {
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
				field: {expiration: 'Df_Payment/card/expiration'}
				,new: {
					atTheEnd: null
					/** 2017-10-16 @see Dfe_Stripe/main */
					,fields: 'Df_Payment/card/fields'
				}
				/**
				 * 2016-11-10 @used-by prefill()
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
	 * @used-by https://github.com/mage2pro/core/blob/2.8.11/Payment/view/frontend/web/template/card/new.html#L56-L58
	 *	<!--ko if: dfCard_customTemplate_afterCardholder() -->
	 *		<!-- ko template: {name: dfCard_customTemplate_afterCardholder()} --><!-- /ko -->
	 *	<!--/ko-->
	 * @see Dfe_Moip/card::dfCard_customTemplate_afterCardholder()
	 * https://github.com/mage2pro/moip/blob/0.9.0/view/frontend/web/card.js#L63
	 * @returns {String=}
	 */
	dfCard_customTemplate_afterCardholder: function() {return null;},
	/**
	 * 2017-07-14
	 * @used-by https://github.com/mage2pro/core/blob/2.8.11/Payment/view/frontend/web/template/card.html#L36-L38
	 *	<!--ko if: dfCard_customTemplate_bottom() -->
	 *		<!-- ko template: {name: dfCard_customTemplate_bottom()} --><!-- /ko -->
	 *	<!--/ko-->
	 * @see Dfe_Moip/card::dfCard_customTemplate_bottom()
	 * https://github.com/mage2pro/moip/blob/0.9.0/view/frontend/web/card.js#L64-L77
	 * @returns {String=}
	 */
	dfCard_customTemplate_bottom: function() {return null;},
	/**
	 * 2016-09-28
	 * @used-by Dfe_Square/expiration.html
	 * @used-by Dfe_Stripe/card/fields.html
	 * @returns {String}
	 */
	dfCardExpirationCompositeId: function() {return this.fid('expiration_composite');},	
	dfCardNumberId: function() {return this.fid('cc_number');},
	dfCardVerificationId: function() {return this.fid('cc_cid');},
	/**
	 * 2016-08-06
	 * 2017-03-01
	 * 2017-07-26
	 * These data are submitted to the M2 server part
	 * as the `additional_data` property value on the «Place Order» button click:
	 * @used-by Df_Payment/mixin::getData():
	 *		getData: function() {return {additional_data: this.dfData(), method: this.item.method};},
	 * https://github.com/mage2pro/core/blob/2.8.4/Payment/view/frontend/web/mixin.js#L224
	 * @override
	 * @see Df_Payment/mixin::dfData():
	 * https://github.com/mage2pro/core/blob/2.8.4/Payment/view/frontend/web/mixin.js#L130-L137
	 * @see Dfe_StripeClone/main::dfData()
	 * @returns {Object}
	 */
	dfData: function() {return {token: this.token};},
	/**
	 * 2016-08-16
	 * @override
	 * @see Df_Payment/mixin::dfFormCssClasses()
	 * https://github.com/mage2pro/core/blob/2.0.25/Payment/view/frontend/web/mixin.js?ts=4#L165
	 * @used-by Df_Payment/mixin::dfFormCssClassesS()
	 * https://github.com/mage2pro/core/blob/2.0.25/Payment/view/frontend/web/mixin.js?ts=4#L171
	 * @see Dfe_Stripe/main::dfFormCssClasses()
	 * @returns {String[]}
	 */
	dfFormCssClasses: function() {return mixin.dfFormCssClasses.call(this).concat(['df-card']);},
	/**
	 * 2016-08-04
	 * 2017-07-25
	 * @see Df_Payment/mixin::domPrefix()
	 * @override
	 * @see Magento_Payment/js/view/payment/cc-form::getCode():
	 * 		return 'cc';
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Payment/view/frontend/web/js/view/payment/cc-form.js#L98-L104
	 * @see Magento_Checkout/js/view/payment/default::getCode():
	 * 		return this.item.method;
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L203-L208
	 * @returns {String}
	 */
	getCode: function() {return this.item.method;},
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
	 * @see Dfe_Moip/card::initialize()
	 * @see Dfe_SecurePay/main::initialize()
	 * https://github.com/mage2pro/securepay/blob/1.5.3/view/frontend/web/main.js#L68-L86
	 * @see Dfe_Square/main::initialize()
	 * https://github.com/mage2pro/square/blob/1.1.2/view/frontend/web/main.js#L125-L158
	 * @see Dfe_TwoCheckout/main::initialize()
	 * https://github.com/mage2pro/2checkout/blob/1.3.5/view/frontend/web/main.js#L61-L73
	 * @returns {exports}
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
		this.isNewCardChosen = ko.computed(function() {return this.newCardId === this.currentCard();}, this);
		// 2016-11-12
		this.cardholder.subscribe(function(v) {cardData.cardholder = v;});
		// 2017-07-22
		// It implements the feature:
		// `Add a new option «Prefill the cardholder's name from the billing address?»
		// to the payment modules which require (or accept) the cardholder's name`
		// https://github.com/mage2pro/core/issues/14
		if (this.requireCardholder() && this.prefillCardholder()) {
			baChange(this, function(a) {
				/**
				 * 2018-01-02
				 * "The cardholder's name field is prefiled with «UNDEFINED UNDEFINED» in earosacoustic.com
				 * (Magento 2.0.17 & Hungersoft One Step Checkout (`HS_OneStepCheckout`))":
				 * https://github.com/mage2pro/stripe/issues/61
				 */
				if (a.firstname && a.lastname) {
					this.cardholder((a.firstname + ' ' + a.lastname).toUpperCase()
						/**
						 * 2017-10-18.
						 * Note 1. «Replacing diacritics in Javascript»: https://stackoverflow.com/a/46192691
						 * Note 2. «JavaScript Unicode 8.0 Normalization - NFC, NFD, NFKC, NFKD»:
						 * https://github.com/walling/unorm
						 * Note 3.
						 * `The ineligible characters should be automatically replaced by the corresponding eligible ones
						 * while prefilling the cardholder's name
						 * (if «Prefill the cardholder's name from the billing address?» option is enabled)`:
						 * https://github.com/mage2pro/core/issues/37#issuecomment-337537967
						 * Note 4.
						 * The same solution server-side (in PHP):
						 *	private function cardholder(A $a) {return transliterator_transliterate('Any-Latin; Latin-ASCII',
						 *		df_strtoupper(df_cc_s($a->getFirstname(), $a->getLastname()))
						 *	);}
						 * https://github.com/mage2pro/stripe/blob/2.3.2/Block/Multishipping.php#L76-L98
						 */
						.normalize('NFD').replace(/[^\w\s-]/g, '')
					);
				}
			});
		}
		// 2016-11-10 Prefill should work only in the Test mode.
		if (this.isTest()) {
			// 2016-11-10 «Prefill the Payment Form with Test Data?»
			/** @type {*} */ var prefill = this.config('prefill');
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
	 * @see Dfe_Moip/card::initObservable()
	 * https://github.com/mage2pro/moip/blob/0.9.0/view/frontend/web/card.js#L95-L104
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
		if (this.requireCardholder() && !this.prefillCardholder()) {
			this.cardholder('DMITRY FEDYUK');
		}
		this.creditCardNumber(d);
		this.prefillWithAFutureData();
		this.creditCardVerificationNumber(this.df.card.prefill.cvv);
	},
	/**
	 * 2017-07-22
	 * «Prefill the cardholder's name from the billing address?»
	 * https://github.com/mage2pro/core/issues/14
	 * @final
	 * @used-by initialize()
	 * @used-by prefill()
	 * @returns {Boolean}
	 */
	prefillCardholder: function() {return this.config('prefillCardholder');},
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
	 * @used-by initialize()
	 * @used-by prefill()
	 * @used-by Df_Payment/card/fields.html
	 * @used-by Dfe_Stripe/card/fields.html
	 * @returns {Boolean}
	 */
	requireCardholder: function() {return(this.df.card.requireCardholder || this.config('requireCardholder'));},
	/**
	 * 2016-08-06
	 * @override
	 * @see mage2pro/core/Payment/view/frontend/web/mixin.js
	 * @returns {Boolean}
	*/
	validate: function() {
		/** @type {Boolean} */ var r = !this.isNewCardChosen() || !!this.selectedCardType();
		if (!r) {
			this.showErrorMessage('It looks like you have entered an incorrect bank card number.');
		}
		return r && this._super() && mixin.validate.apply(this);
	}
}));});
