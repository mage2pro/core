// 2016-08-17
define(['jquery'], function($) {return function() {
	$('body').toggleClass('df-single-payment-method', 1 === window.checkoutConfig.paymentMethods.length);
};});