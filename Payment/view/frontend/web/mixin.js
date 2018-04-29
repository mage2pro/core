/**
 * 2016-08-04
 * @see Df_Payment/card https://github.com/mage2pro/core/blob/2.9.2/Payment/view/frontend/web/card.js
 * @see Df_Payment/custom https://github.com/mage2pro/core/blob/2.9.2/Payment/view/frontend/web/custom.js
 */
define([
	'./createMessagesComponent', 'df', 'df-lodash', 'Df_Checkout/placeOrder'
	,'Df_Checkout/data', 'Df_Core/my/redirectWithPost', 'Df_Payment/billingAddressChange'
	,'jquery', 'Df_Intl/t', 'ko', 'mage/url'
	,'Magento_Checkout/js/model/payment/additional-validators'
	,'Magento_Checkout/js/model/quote'
], function(
	createMessagesComponent, df, _, placeOrder
	,dfc, rPost, baChange
	,$, $t, ko, lUrl
	,validators
	,quote
) {
'use strict';
/**
 * 2016-08-22
 * Стандартная функция df.c некорректно работает при наследовании вызвавающего её класса:
 * классы-наследники будут разделять кэш между собой.
 * Правильным решением является описание своей кэширующей функции
 * в родительском классе с использованием _.memoize
 * и с расчётом ключа кэша в параметре resolver.
 *
 * 2016-08-23
 * Пример использования:
 *		cards: mixin.c(function() {
 *			var _this = this; return $.map(this.config('cards'), function(card) {
 *				return df.o.merge(card, {domId: [_this.getCode(), card.id].join('-')});
 *			});
 *		}),
 *
 * 2016-09-06
 * Вынес эту функцию из класса, чтобы её могли вызывать свойства класса,
 * ведь свойства класса не могут ссылаться на другие свойства этого же класса
 * при нашей реализации класса: http://stackoverflow.com/questions/3173610
 *
 * @param {Function} func
 * @returns {Function}
 */
function c(func) {return _.memoize(func, function() {return this.domPrefix();});}
return {
	/**
	 * 2017-02-07
	 * Размер платежа в валюте платёжной транзакции В ФОРМАТЕ ПЛАТЁЖНОЙ СИСТЕМЫ
	 * (обычно — копейках).
	 * Пока этот метод используется только модулем Paymill:
	 * https://github.com/mage2pro/paymill/blob/0.1.3/view/frontend/web/main.js?ts=4#L48
	 * @returns {Number}
 	 */
	amountF: c(function() {return this.config('amountF');}),
	/**
	 * 2016-08-26
	 * Возвращает строку из 2 последних цифр суммы платежа в платежной валюте
	 * (которая необязательно совпадает с учётной или витринной).
	 * @returns {String}
	 */
	amountLast2: c(function() {return df.money.last2(this.amountP());}),
	/**
	 * 2016-09-06
	 * Размер платежа в валюте платёжной транзакции.
	 * 2017-02-07
	 * https://github.com/mage2pro/core/blob/1.12.8/Payment/ConfigProvider.php?ts=4#L77
	 * @returns {Number}
 	 */
	amountP: c(function() {return this.paymentCurrency().rate * this.dfc.grandTotalBase();}),
	/**
	 * 2016-09-06
	 * Форматирует размер платежа
	 * по правилам ВИТРИННОГО отображения платёжной валюты (это НЕ формат платёжной системы).
	 * @returns {String}
 	 */
	amountPD: c(function() {return this.formatAmountForDisplay(this.amountP())}),
	c: c,
	/**
	 * 2016-08-04
	 * 2016-09-05
	 * Отныне ключ может быть иерархическим: https://lodash.com/docs#get
	 * По смыслу это является аналогом функции PHP dfa_deep().
	 * @param {String=} key
	 * @returns {Object}|{*}
	 */
	config: function(key) {
		/** @type {Object} */
		var r =  window.checkoutConfig.payment[this.domPrefix()];
		return !key ? r : _.get(r, key);
	},
	/**
	 * 2017-09-20
	 * @used-by Df_Payment/main.html
	 * 		attr: {'class': ['payment-method df-payment-method', domPrefix(), containerCss()].join(' ')}
	 * https://github.com/mage2pro/core/blob/2.12.8/Payment/view/frontend/web/template/main.html#L3		
	 * @see Df_Payment/withOptions::containerCss()
	 * @returns {string}
	 */
	containerCss: function() {return '';},
	createMessagesComponent: createMessagesComponent,
	/**
	 * 2016-08-04
	 * @returns {String}
	*/
	debugMessage: function() {return '';},
	defaults: {
		active: false
		,df: {
			// 2016-08-06
			// @used-by Df_Payment/main
			// https://github.com/mage2pro/core/blob/2.4.21/Payment/view/frontend/web/template/main.html#L36-L38
			formTemplate: null
			/**
			 * 2017-09-09
			 * @used-by dfFormCssClasses()
			 * @see Df_Payment/withOptions
			 * @see Dfe_Moip/card
			 */
			,placeOrderButtonAlignment: 'left'
			,requireBillingAddress: false // 2017-07-29
			,test: {suffix: 'TEST MODE'}
		}
		,template: 'Df_Payment/main'
	},
	dfc: dfc,
	/**
	 * 2017-07-25
	 * It is indetical to Magento_Checkout/js/view/payment/default::isChecked():
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.5/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L179-L181
	 * I have duplicated it here, because the `isChecked` name confuses me.
	 * @used-by dfIsChosen()
	 * @used-by Df_Payment/main.html
	 * @returns {?String}
 	 */
	dfChosenMethod: ko.computed(function() {var m = quote.paymentMethod(); return m ? m.method : null;}),
	/**
	 * 2017-07-25
	 * @final
	 * @used-by Df_Payment/main.html:
	 *	<input
	 *		type="radio"
	 *		name="payment[method]"
	 *		class="radio"
	 *		data-bind="
	 *			attr: {id: domPrefix()}
	 *			,checked: dfRadioButtonCheckedHandler()
	 *			,click: selectPaymentMethod
	 *			,value: domPrefix()
	 *			,visible: isRadioButtonVisible()
	 *		"
	 *	/>
	 * https://github.com/mage2pro/core/blob/2.11.5/Payment/view/frontend/web/template/main.html#L20
	 */
	dfRadioButtonCheckedHandler: function() {return this.dfIsChosen() ? this.domPrefix() : null;},
	/**
	 * 2016-08-06
	 * 2017-07-26
	 * These data are submitted to the M2 server part
	 * as the `additional_data` property value on the «Place Order» button click:
	 * @used-by getData():
	 *		getData: function() {return {additional_data: this.dfData(), method: this.item.method};},
	 * https://github.com/mage2pro/core/blob/2.8.4/Payment/view/frontend/web/mixin.js#L224
	 * @see Df_Payment/card::dfData()
	 * https://github.com/mage2pro/core/blob/2.8.4/Payment/view/frontend/web/card.js#L60-L70
	 * @see Df_Payment/withOptions::dfData()
	 * https://github.com/mage2pro/core/blob/2.8.4/Payment/view/frontend/web/withOptions.js#L19-L33
	 * @see Dfe_Qiwi/main::dfData()
	 * @returns {Object}
	 */
	dfData: function() {return {};},
	/**
	 * 2016-08-04
	 * @used-by validate()
	 * https://github.com/mage2pro/core/blob/3.2.4/Payment/view/frontend/web/mixin.js#L477-L484
	 * @used-by Dfe_AllPay/main::dfFormAfterRender()
	 * https://github.com/mage2pro/allpay/blob/1.10.0/view/frontend/web/main.js#L11-L24
	 * @used-by Dfe_Qiwi/main::initialize()
	 * https://github.com/mage2pro/qiwi/blob/1.0.4/view/frontend/web/main.js#L39-L42
	 * @param {String=} selector
	 * @returns {?jQuery} HTMLFormElement
	 */
	dfForm: function(selector) {
		if (df.u(this._dfForm)) {
			var result = $('form.' + this.domPrefix());
			// 2016-08-17 Если метод вызван до отрисовки шаблона формы, то форма будет отсутствовать.
			if (result.length) {
				this._dfForm = result;
			}
		}
		return df.u(this._dfForm) ? null : (df.u(selector) ? this._dfForm : $(selector, this._dfForm));
	},
	/**
	 * 2016-08-17
	 * @used-by Df_Payment/main
	 * https://github.com/mage2pro/core/blob/2.0.36/Payment/view/frontend/web/template/main.html?ts=4#L33
	 * 2016-08-19
	 * Magento <= 2.1.0 calls an `afterRender` handler outside of the `this` context.
	 * It passes `this` to an `afterRender` handler as the second argument:
	 * https://github.com/magento/magento2/blob/2.0.9/app/code/Magento/Ui/view/base/web/js/lib/ko/bind/after-render.js#L19
	 * Magento >= 2.1.0 calls an `afterRender` handler within the `this` context:
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Ui/view/base/web/js/lib/knockout/bindings/after-render.js#L20
	 * 2017-02-16
	 * @see allPay: https://github.com/mage2pro/allpay/blob/1.5.3/view/frontend/web/main.js#L9-L22
	 * @param {HTMLElement} element
	 * @param {Object} _this
	 */
	dfFormAfterRender: function(element, _this) {},
	/**
	 * 2016-08-16
	 * @used-by dfFormCssClassesS()
	 * @see Df_GingerPaymentsBase/main::dfFormCssClasses()
	 * https://github.com/mage2pro/ginger-payments-base/blob/0.1.0/view/frontend/web/main.js?ts=4#L47
	 * @see Df_Payment/card::dfFormCssClasses()
	 * https://github.com/mage2pro/core/blob/2.0.25/Payment/view/frontend/web/card.js?ts=4#L76
	 * @see Dfe_AllPay/main::dfFormCssClasses()
	 * https://github.com/mage2pro/allpay/blob/1.1.33/view/frontend/web/main.js?ts=4#L60-L63
	 * @returns {String[]}
	 */
	dfFormCssClasses: function() {return ['form', 'df-payment', this.domPrefix(),
		'df-placeOrderButtonAlignment-' + this.df.placeOrderButtonAlignment
	];},
	/**
	 * 2016-08-16
	 * @final
	 * @used-by https://github.com/mage2pro/core/blob/2.0.25/Payment/view/frontend/web/template/main.html?ts=4#L33
	 * @returns {String}
	 */
	dfFormCssClassesS: function() {return df.a.ccClean(' ', this.dfFormCssClasses());},
	/**
	 * 2017-07-25
	 * @used-by Df_Payment/main.html
	 * @returns {Boolean}
 	 */
	dfIsChosen: function() {var m = quote.paymentMethod(); return m && m.method === this.item.method;},
	/**
	 * 2017-07-28
	 * @see Dfe_Moip/mixin::domPrefix()
	 * @used-by dfForm()
	 * @used-by dfFormCssClasses()
	 * @used-by Df_Payment/withOptions::woRadioId()
	 * @returns {String}
	 */
	domPrefix: function() {return this.item.method;},
	/**
	 * 2016-09-28
	 * @param {String} id
	 * @returns {String}
	 */
	fid: function(id) {return this.domPrefix() + '_' + id;},
	/**
	 * 2016-09-06
	 * Форматирует произвольную денежную величину
	 * по правилам ВИТРИННОГО отображения платёжной валюты (это НЕ формат платёжной системы).
	 * 2017-02-07
	 * https://github.com/mage2pro/core/blob/1.12.8/Payment/ConfigProvider.php?ts=4#L65
	 * @returns {String}
 	 */
	formatAmountForDisplay: function(amount) {return(
		dfc.formatMoney(amount, this.paymentCurrency().format)
	);},
	/**
	 * 2016-08-06
	 * 2017-03-01
	 * 2017-07-26 These data are submitted to the M2 server part on the «Place Order» button click.
	 * @override
	 *
	 * @see Magento_Checkout/js/view/payment/default::getData():
	 *		getData: function () {
	 *			return {
	 *				'method': this.item.method,
	 *				'po_number': null,
	 *				'additional_data': null
	 *			};
	 *		}
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L185-L194
	 *
	 * @see Magento_Payment/js/view/payment/cc-form::getData():
	 *		getData: function () {
	 *			return {
	 *				'method': this.item.method,
	 *				'additional_data': {
	 *					'cc_cid': this.creditCardVerificationNumber(),
	 *					'cc_ss_start_month': this.creditCardSsStartMonth(),
	 *					'cc_ss_start_year': this.creditCardSsStartYear(),
	 *					'cc_ss_issue': this.creditCardSsIssue(),
	 *					'cc_type': this.creditCardType(),
	 *					'cc_exp_year': this.creditCardExpYear(),
	 *					'cc_exp_month': this.creditCardExpMonth(),
	 *					'cc_number': this.creditCardNumber()
	 *				}
	 *			};
	 *		},
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Payment/view/frontend/web/js/view/payment/cc-form.js#L106-L124
	 *
	 * @used-by Df_Checkout/placeOrder:
	 * 		_.assign({ba: ba, qp: main.getData()}, l ? {} : {email: q.guestEmail})
	 * https://github.com/mage2pro/core/blob/3.5.4/Checkout/view/frontend/web/placeOrder.js#L66
	 * @used-by getPlaceOrderDeferredObject(): https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L161-L165
	 * @used-by selectPaymentMethod(): https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L167-L175
	 */
	getData: function() {return {
		// 2016-05-03
		// Если не засунуть данные (например, «token») внутрь «additional_data», то получим сбой типа:
		// «Property "Token" does not have corresponding setter
		// in class "Magento\Quote\Api\Data\PaymentInterface».
		additional_data: this.dfData(), method: this.item.method
	};},
	/**
	 * 2016-07-01
	 * @override
	 * @see Magento_Checkout/js/view/payment/default::getTitle():
	 *		getTitle: function () {
	 *			return this.item.title;
	 *		},
 	 *	https://github.com/magento/magento2/blob/2.2.0-RC1.5/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L198-L203
	 * @used-by Df_Payment/main.html:
	 * 		<label class="label" data-bind="attr: {for: getCode()}">
	 * 		    <span data-bind="html: getTitle()"></span>
 	 *	    </label>
	 * https://github.com/mage2pro/core/blob/2.9.8/Payment/view/frontend/web/template/main.html#L26
	 * @returns {String}
	*/
	getTitle: function() {
		/** @type {String} */ var r = this.config('title') || this._super();
		if (this.isTest()) {
			/** @type {String} */ var titleB = this.config('titleBackend');
			/** @type {String} */ var testSuffix = this.df.test.suffix;
			if (-1 === r.indexOf(titleB)) {
				testSuffix = titleB + ' ' + testSuffix;			
			}
			r += df.t(' [<b>%s</b>]', testSuffix)
		}
		return r;
	},
	imports: {onActiveChange: 'active'},
	/**
	 * 2016-08-23
	 * Эту функцию вызвать надо так: mixin.initialize.apply(this);
	 * @used-by <...>
	 * @see Df_Payment/card::initialize()
	 * @see Df_Payment/withOptions::initialize()
	 */
	initialize: function() {
		this.state_waitingForBillingAddress = ko.observable(false);
		this.state_waitingForServerResponse = ko.observable(false);
		/**
		 * 2017-09-20
		 * @used-by Df_Payment/main.html:
		 *		<button <...> data-bind="<...> enable: canPlaceOrder">
		 *			<span data-bind="df_i18n: 'Place Order'"></span>
		 *		</button>
		 * @see Df_Payment/withOptions::canPlaceOrder()
		 */
		this.canPlaceOrder = ko.computed(function() {return (
			!this.state_waitingForBillingAddress() && !this.state_waitingForServerResponse()
		);}, this);
		if (this.requireBillingAddress()) {
			baChange(this, function(a) {this.state_waitingForBillingAddress(!a)});
		}
	},
	/**
	 * 2016-08-04
	 * @returns {Boolean}
	*/
	isTest: function() {return this.config('isTest');},
	/**
	 * 2016-09-06
	 * 2017-02-07
	 * https://github.com/mage2pro/core/blob/1.12.8/Payment/ConfigProvider.php?ts=4#L52-L78
	 * @typedef {Object} PaymentCurrency
	 * @property {String} code		Код платёжной валюты.
	 * @property {Object} format	Правила форматирования платёжной валюты.
	 * @property {String} name		Название платёжной валюты.
	 * @property {Number} rate		Курс обмена учётной валюты на платёжную.
	 * @returns {PaymentCurrency}
	*/
	paymentCurrency: function() {return this.config('paymentCurrency');},
	/**
	 * 2016-08-06
	 * @final
	 * @used-by Dfe_TwoCheckout/main::placeOrder()
	 * https://github.com/mage2pro/2checkout/blob/1.6.0/view/frontend/web/main.js#L114
	 * @used-by Dfe_CheckoutCom/main::placeOrder()
	 * https://github.com/mage2pro/checkout.com/blob/1.5.1/view/frontend/web/main.js#L156
	 * @used-by Df_Payment/custom::placeOrder()
	 * https://github.com/mage2pro/core/blob/3.7.18/Payment/view/frontend/web/custom.js#L78
	 * @used-by Df_StripeClone/main::placeOrder()
	 * https://github.com/mage2pro/core/blob/3.7.18/StripeClone/view/frontend/web/main.js#L171
	 * https://github.com/mage2pro/core/blob/3.7.18/StripeClone/view/frontend/web/main.js#L182
	 * @used-by Dfe_SecurePay/main::placeOrder()
	 * https://github.com/mage2pro/securepay/blob/1.7.0/view/frontend/web/main.js#L135
	 * @used-by Dfe_Square/main::dfOnRender()
	 * https://github.com/mage2pro/square/blob/2.1.0/view/frontend/web/main.js#L130
	 * @used-by Dfe_Square/main::placeOrder()
	 * https://github.com/mage2pro/square/blob/2.1.0/view/frontend/web/main.js#L509
	 */
	placeOrderInternal: function() {
		var _this = this;
		// 2016-08-26 Надо писать именно так, чтобы сохранить контекст _this
		$.when(placeOrder(this))
			.fail(function() {_this.state_waitingForServerResponse(false);})
			.done(function(json) {
				// 2017-04-05
				// Отныне json у нас всегда строка: @see dfw_encode().
				// Для не требующих перенаправления модулей эта строка пуста, и !json возвращает true.
				/**
				 * @type {Object} d
				 * @type {Boolean=} d.forceGet
				 * @type {Object=} d.p
				 * @type {String=} d.url
				 */
				var d = !json ? {} : $.parseJSON(json);
				// 2016-06-28
				// Замечание 1.
				// Библиотека https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/action/redirect-on-success.js
				// отсутствует в версиях ранее 2.1.0: https://github.com/CKOTech/checkout-magento2-plugin/issues/3
				// Поэтому эмулируем её.
				// Замечание 2.
				// window.checkoutConfig.defaultSuccessPageUrl отсутствует в версиях ранее 2.1.0:
				// https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/Model/DefaultConfigProvider.php#L268
				// By analogy with https://github.com/magento/magento2/blob/2.0.7/app/code/Magento/Checkout/view/frontend/web/js/action/place-order.js#L51
				d.url && !df.o.e(d.p) && !d.forceGet ? rPost(d.url, _.assign({}, d.p, _this.postParams())) :
					window.location.replace(d.url ? (df.o.e(d.p) ? d.url : d.url + '?' + $.param(d.p)) :
						lUrl.build(window.checkoutConfig.defaultSuccessPageUrl || 'checkout/onepage/success/')
					)
				;
			})
		;
	},
	/**
	 * 2017-03-21
	 * @used-by placeOrderInternal()
	 * @returns {Object}
	 */
	postParams: function() {return {};},
	/**
	 * 2016-08-22
	 * @returns {Boolean}
	*/
	requireBillingAddress: function() {return(
		this.df.requireBillingAddress || this.config('requireBillingAddress')
	);},
	/**
	 * 2016-08-05
	 * @used-by Df_Payment/card::validate()
	 * @used-by Df_StripeClone/main::placeOrder()
	 * @used-by Dfe_CheckoutCom/main::placeOrder()
	 * @used-by Dfe_Square/main::dfOnRender()
	 * @used-by Dfe_Stripe/main::dfOnRender()
	 * @used-by Dfe_TwoCheckout/main::placeOrder()
	 * @param {String} message
	 * @param {Object=} params
	 * @param {Boolean=} needTranslate
	*/
	showErrorMessage: function(message, params, needTranslate) {
		message = !df.arg(needTranslate, true) ? message : $t(message);
		$.each(df.arg(params, {}), function(name, value) {
			message = message.replace(name, value);
		});
		this.messageContainer.addErrorMessage({'message': message});
	},
	/**
	 * 2017-04-15
	 * Символ доллара нужен, чтобы наши строки разбирались парсером ядра:
	 * https://github.com/magento/magento2/blob/2.1.6/app/code/Magento/Translation/etc/di.xml#L63
	 * <item name="mage_translation_static" xsi:type="string"
	 * >~\$t\((?s)[^'"]*?(["'])(.+?)\1(?s).*?\)~</item>
	 * @returns {Function}
	 */
	$t: $t,
	/**
	 * 2016-08-05
	 * Note 1.
	 * this._super() actually does nothing and just returns true:
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L210-L215
	 *
	 * Note 2.
	 * validators.validate() validates:
	 * 1) the customer's email (if the customer is not logged in): https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/model/customer-email-validator.js#L23-L33
	 * 2) the checkout agreement acceptance: https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/CheckoutAgreements/view/frontend/web/js/model/agreement-validator.js#L20-L46
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/model/payment/additional-validators.js#L29-L48
	 *
	 * @override
	 * @see https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L210-L215
	 * @see Df_Payment/card::validate()
	 * @see Df_Payment/custom::validate()
	 * @used-by Df_Payment/custom::validate()
	 * @returns {Boolean}
	*/
	validate: function() {
		/** @type {Boolean} */ var r = this._super() && validators.validate();
		if (r) {
			/** @type {Object} */ var $f = this.dfForm();
			r = $f.validation() && $f.validation('isValid');
		}
		return r;
	}
};});
