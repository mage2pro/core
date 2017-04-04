// 2016-08-04
define([
	'./createMessagesComponent'
	,'df'
	,'df-lodash'
	,'Df_Checkout/js/action/place-order'
	,'Df_Checkout/js/action/redirect-on-success'
	,'Df_Checkout/js/data'
	,'Df_Core/my/redirectWithPost'
	,'jquery'
	,'mage/translate'
	,'Magento_Checkout/js/model/payment/additional-validators'
], function(
	createMessagesComponent, df, _, placeOrderAction, redirectOnSuccessAction
	,dfc, redirectWithPost, $, $t, validators
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
function c(func) {return _.memoize(func, function() {return this.getCode();});}
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
	/**
	 * 2016-08-22
	 * @returns {Boolean}
	*/
	requireBillingAddress: function() {return this.config('requireBillingAddress');},
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
		var result =  window.checkoutConfig.payment[this.getCode()];
		return !key ? result : _.get(result, key);
	},
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
			formTemplate: null,
			test: {
				// 2017-04-03
				// Эта опция определяет окончание заголовка способа оплаты в тестовом режиме
				// на странице оформления заказа.
				// 1) Если значение опции равно «true», то окончание заголовка
				// будет содержать название модуля, например: «[Omise TEST MODE]».
				// Этот вариант в настоящее время используется модулями,
				// принимающими только банковские карты.
				// Они на витрине по умолчанию имеют обобщённый заголовок «Bank Card»,
				// поэтому в окончании заголовка разумно писать название модуля,
				// что и достигается значением «true».
				//
				// 2) Если значение опции равно «false», то окончание заголовка
				// не будет содержать название модуля, например: [TEST MODE].
				// Это значение в настоящее время используется модулями
				// (allPay, Ginger Payments, Kassa Compleet, Klarna),
				// принимающими оплату сразу несколькими способами.
				// У этих модулей название модуля уже содержится в заголовоке,
				// поэтому нет необходимости повторять его в окончании заголовка.
				showBackendTitle: true
				,suffix: 'TEST MODE'
			}
		},
		template: 'Df_Payment/main'
	},
	dfc: dfc,
	/**
	 * 2016-08-06
	 * @used-by getData()
	 * @see https://github.com/mage2pro/core/blob/2.0.20/Payment/view/frontend/web/card.js?ts=4#L50-L57
	 * @see https://github.com/mage2pro/allpay/blob/1.1.32/view/frontend/web/main.js?ts=4#L17-L26
	 * @returns {Object}
	 */
	dfData: function() {return {};},
	/**
	 * 2016-08-04
	 * @param {String=} selector [optional]
	 * @returns {jQuery} HTMLFormElement
	 */
	dfForm: function(selector) {
		if (df.u(this._dfForm)) {
			var result = $('form.' + this.getCode());
			// 2016-08-17
			// Если метод вызван до отрисовки шаблона формы, то форма будет отсутствовать.
			if (result.length) {
				this._dfForm = result;
			}
		}
		return df.u(this._dfForm) ? null : (
			df.u(selector) ? this._dfForm : $(selector, this._dfForm)
		);
	},
	/**
	 * 2016-08-17
	 * @used-by Df_Payment/main
	 * https://github.com/mage2pro/core/blob/2.0.36/Payment/view/frontend/web/template/main.html?ts=4#L33
	 * 2016-08-19
	 * В версиях Magento ниже 2.1.0 эта функция вызывается вне контекста this:
	 * https://github.com/magento/magento2/blob/2.0.9/app/code/Magento/Ui/view/base/web/js/lib/ko/bind/after-render.js#L19
	 * Однако this передаётся вторым аргументом.
	 * В Magento 2.1.0 функция вызывается уже в контексте this:
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
	dfFormCssClasses: function() {return ['form', 'df-payment', this.getCode()];},
	/**
	 * 2016-08-16
	 * @final
	 * @used-by https://github.com/mage2pro/core/blob/2.0.25/Payment/view/frontend/web/template/main.html?ts=4#L33
	 * @returns {String}
	 */
	dfFormCssClassesS: function() {return df.a.ccClean(' ', this.dfFormCssClasses());},
	/**
	 * 2016-09-28
	 * @param {String} id
	 * @returns {String}
	 */
	fid: function(id) {return this.getCode() + '_' + id;},
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
	 * Задаёт набор передаваемых на сервер при нажатии кнопки «Place Order» данных.
	 * @override
	 * @see https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L185-L194
	 * @see https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Payment/view/frontend/web/js/view/payment/cc-form.js#L106-L124
	 * @used-by placeOrderInternal()
	 * @used-by getPlaceOrderDeferredObject(): https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L161-L165
	 * @used-by selectPaymentMethod(): https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L167-L175
	 */
	getData: function () {return {
		// 2016-05-03
		// Если не засунуть данные (например, «token») внутрь «additional_data», то получим сбой типа:
		// «Property "Token" does not have corresponding setter
		// in class "Magento\Quote\Api\Data\PaymentInterface».
		additional_data: this.dfData(), method: this.item.method
	};},
	/**
	 * 2016-07-01
	 * @returns {String}
	*/
	getTitle: function() {return df.a.ccClean(' ', [this._super(), !this.isTest() ? null :
		'[<b>title</b>]'.replace('title', df.a.ccClean(' ', [
			this.df.test.showBackendTitle ? this.config('titleBackend') : null
			, this.df.test.suffix
		]))
	]);},
	imports: {onActiveChange: 'active'},
	/**
	 * 2016-08-23
	 * Эту функцию вызвать надо так: mixin.initialize.apply(this);
	 * @used-by Df_Payment/card::initialize()
	 */
	initialize: function() {
		if (!this.requireBillingAddress()) {
			 // 2016-08-23
			 // По умолчанию isPlaceOrderActionAllowed устроена так:
			 //		isPlaceOrderActionAllowed: ko.observable(quote.billingAddress() != null)
			 // https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L44
			this.isPlaceOrderActionAllowed(true);
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
	 * @used-by Dfe_TwoCheckout/main::placeOrder()
	 * https://github.com/mage2pro/2checkout/blob/1.2.1/view/frontend/web/main.js#L97
	 * @used-by Dfe_CheckoutCom/main::placeOrder()
	 * https://github.com/mage2pro/checkout.com/blob/1.2.1/view/frontend/web/main.js#L173
	 * @used-by Df_Payment/withOptions::placeOrder()
	 * https://github.com/mage2pro/core/blob/2.3.3/Payment/view/frontend/web/withOptions.js#L104
	 * @used-by Df_StripeClone/main::placeOrder()
	 * https://github.com/mage2pro/core/blob/2.3.3/StripeClone/view/frontend/web/main.js#L73
	 * https://github.com/mage2pro/core/blob/2.3.3/StripeClone/view/frontend/web/main.js#L82
	 * @used-by Dfe_Klarna/main::placeOrder()
	 * https://github.com/mage2pro/klarna/blob/0.1.13/view/frontend/web/main.js#L39
	 * @used-by Dfe_SecurePay/main::placeOrder()
	 * https://github.com/mage2pro/securepay/blob/1.4.2/view/frontend/web/main.js#L115
	 * @used-by Dfe_Square/main::cardNonceResponseReceived()
	 * https://github.com/mage2pro/square/blob/1.1.0/view/frontend/web/main.js#L86
	 */
	placeOrderInternal: function() {
		var _this = this;
		$.when(placeOrderAction(this.getData(), this.messageContainer))
			.fail(function() {_this.isPlaceOrderActionAllowed(true);})
			// 2016-08-26
			// Надо писать именно так, чтобы сохранить контекст _this
			.done(function(json) {
				/** @type {?Object} */
				var p = null;
				/** @type {?String} */
				var url = null;
				if (json && json.length) {
					var data = $.parseJSON(json);
					p = data.params && _.size(data.params) ? data.params : null;
					url = data.url && data.url.length ? data.url : null;
				}
				!url ? redirectOnSuccessAction.execute() : (
					p ? redirectWithPost(url, df.o.merge(p, _this.postParams())) :
						window.location.replace(url)
				);
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
	 * 2016-08-05
	 * @param {String} message
	 * @param {Object=} params [optional]
	 * @param {Boolean=} needTranslate [optional]
	*/
	showErrorMessage: function(message, params, needTranslate) {
		message = !df.arg(needTranslate, true) ? message : $t(message);
		$.each(df.arg(params, {}), function(name, value) {
			message = message.replace(name, value);
		});
		this.messageContainer.addErrorMessage({'message': message});
	},
	/**
	 * 2016-08-05
	 * @override
	 * @see https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L210-L215
	 * @returns {Boolean}
	*/
	validate: function() {
		/**
		 * 2016-08-05
		 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/model/payment/additional-validators.js#L29-L48
		 * It validates:
		 * 1) the customer's email (if the customer is not logged in): https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/model/customer-email-validator.js#L23-L33
		 * 2) the checkout agreement acceptance: https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/CheckoutAgreements/view/frontend/web/js/model/agreement-validator.js#L20-L46
		 *
		 * The inherited this.validate() method actually does nothing and just returns true:
		 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L210-L215
		 */
		return this._super() && validators.validate();
	}
};});
