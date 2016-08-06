// 2016-07-18
// 2016-08-06
// Используем $.extend вместо _.extend, потому что нам нужна опция deep:
// http://stackoverflow.com/a/24542665
// http://api.jquery.com/jquery.extend/
define([
	'Magento_Checkout/js/view/payment/default', './mixin', 'jquery'
], function(parent, mixin, $) {'use strict'; return parent.extend($.extend(true, {}, mixin, {
}));});


