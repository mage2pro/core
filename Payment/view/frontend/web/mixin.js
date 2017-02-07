// 2016-08-04
define([
	'./createMessagesComponent'
	,'df'
	,'df-lodash'
	,'Df_Checkout/js/action/place-order'
	,'Df_Checkout/js/action/redirect-on-success'
	,'Df_Checkout/js/data'
	,'jquery'
	,'mage/translate'
	,'Magento_Checkout/js/model/payment/additional-validators'
], function(
	createMessagesComponent
	, df
	,_
	, placeOrderAction
	, redirectOnSuccessAction
	, dfc
	, $, $t, validators
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
		savedCards: mixin.c(function() {
			var _this = this; return $.map(this.config('savedCards'), function(card) {
				return df.o.merge(card, {domId: [_this.getCode(), card.id].join('-')});
			});
		}),
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
	amountPF: c(function() {return this.formatP(this.amountP())}),
	/**
	 * 2017-02-07
	 * Размер платежа в валюте платёжной транзакции В КОПЕЙКАХ.
	 * Для простоты пока считаю, что достаточно умножить значение на 100
	 * (т.е. не учитываю специальные случаи бескопеечных валют типа японской цены).
	 * Пока этот метод используется только модулем Paymill.
	 * @returns {Number}
 	 */
	amountPI: c(function() {return 100 * this.amountP();}),
	/**
	 * 2016-09-06
	 * Форматирует произвольную денежную величину
	 * по правилам ВИТРИННОГО отображения платёжной валюты (это НЕ формат платёжной системы).
	 * 2017-02-07
	 * https://github.com/mage2pro/core/blob/1.12.8/Payment/ConfigProvider.php?ts=4#L65
	 * @returns {String}
 	 */
	formatP: function(amount) {return dfc.formatMoney(amount, this.paymentCurrency().format);},
	/**
	 * 2016-08-22
	 * @returns {Boolean}
	*/
	askForBillingAddress: function() {return this.config('askForBillingAddress');},
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
			// @used-by mage2pro/core/Payment/view/frontend/web/template/item.html
			formTemplate: null,
			test: {
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
			/**
			 * 2016-08-17
			 * Если метод вызван до отрисовки шаблона формы,
			 * то форма будет отсутствовать.
			 */
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
	 * @used-by mage2pro/core/Payment/view/frontend/web/template/item.html
	 * 2016-08-19
	 * В версиях Magento ниже 2.1.0 эта функция вызывается вне контекста this:
	 * https://github.com/magento/magento2/blob/2.0.9/app/code/Magento/Ui/view/base/web/js/lib/ko/bind/after-render.js#L19
	 * Однако this передаётся вторым аргументом.
	 * В Magento 2.1.0 функция вызывается уже в контексте this:
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Ui/view/base/web/js/lib/knockout/bindings/after-render.js#L20
	 * @param {HTMLElement} element
	 * @param {Object} _this
	 */
	dfFormAfterRender: function(element, _this) {},
	/**
	 * 2016-08-16
	 * @used-by dfFormCssClassesS()
	 * @returns {String[]}
	 */
	dfFormCssClasses: function() {return ['form', 'df-payment', this.getCode()];},
	/**
	 * 2016-08-16
	 * @used-by mage2pro/core/Payment/view/frontend/web/template/item.html
	 * @returns {String}
	 */
	dfFormCssClassesS: function() {return df.a.ccClean(' ', this.dfFormCssClasses());},
	/**
	 * 2016-08-08
	 * @param {String} name
	 * @param {String} value
	 * @returns {jQuery} HTMLElement
	 */
	dfFormElementByAttr: function(name, value) {return this.dfForm(df.dom.attr(name, value));},
	/**
	 * 2016-08-08
	 * @param {String} name
	 * @returns {jQuery} HTMLInputElement[]
	 */
	dfInputByName: function(name) {return this.dfFormElementByAttr('name', name);},
	/**
	 * 2016-08-08
	 * @param {String} name
	 * @returns {?String}
	 */
	dfRadioValue: function(name) {return df.dom.radioValue(this.dfInputByName(name));},
	/**
	 * 2016-08-23
	 * @param {String} id
	 * @returns {String}
	 */
	domId: function(id) {return [this.getCode(), id].join('-');},
	/**
	 * 2016-08-06
	 * @override
	 * @see https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L185-L194
	 * @see https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Payment/view/frontend/web/js/view/payment/cc-form.js#L106-L124
	 * @used-by placeOrderInternal()
	 * @used-by getPlaceOrderDeferredObject(): https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L161-L165
	 * @used-by selectPaymentMethod(): https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L167-L175
	 */
	getData: function () {
		return {
			/**
			 * 2016-05-03
			 * Если не засунуть данные (например, «token») внутрь «additional_data»,
			 * то получим сбой типа:
			 * «Property "Token" does not have corresponding setter
			 * in class "Magento\Quote\Api\Data\PaymentInterface»
			 */
			additional_data: this.dfData()
			,method: this.item.method
		};
	},
	/**
	 * 2016-07-01
	 * @returns {String}
	*/
	getTitle: function() {
		return df.a.ccClean(' ', [this._super(), !this.isTest() ? null :
			'[<b>title</b>]'.replace('title', df.a.ccClean(' ', [
				this.df.test.showBackendTitle ? this.config('titleBackend') : null
				, this.df.test.suffix
			]))
		]);
	},
	imports: {onActiveChange: 'active'},
	/**
	 * 2016-08-23
	 * Эту функцию вызвать надо так: mixin.initialize.apply(this);
	 */
	initialize: function() {
		if (!this.askForBillingAddress()) {
			/**
			 * 2016-08-23
			 * По умолчанию isPlaceOrderActionAllowed устроена так:
			 		isPlaceOrderActionAllowed: ko.observable(quote.billingAddress() != null)
			 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L44
			 */
			this.isPlaceOrderActionAllowed(true);
		}
	},
	/**
	 * 2016-08-04
	 * @returns {Boolean}
	*/
	isTest: function() {return this.config('isTest');},
	/**
	 * 2016-08-06
	 * @used-by placeOrderInternal()
	 */
	onSuccess: function() {redirectOnSuccessAction.execute()},
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
	/** 2016-08-06 */
	placeOrderInternal: function() {
		var _this = this;
		$.when(placeOrderAction(this.getData(), this.messageContainer, this.config('route')))
			.fail(function() {_this.isPlaceOrderActionAllowed(true);})
			// 2016-08-26
			// Надо писать именно так, чтобы сохранить контекст _this
			.done(function(data) {_this.onSuccess(data);})
		;
	},
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
