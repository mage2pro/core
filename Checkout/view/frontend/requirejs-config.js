// 2020-07-08 "Add an unique CSS class to each payment method container": https://github.com/mage2pro/core/issues/102
var config = {config: {mixins: {
	'Magento_Checkout/js/view/payment/default': {'Df_Checkout/view/payment/default': true}
}}};