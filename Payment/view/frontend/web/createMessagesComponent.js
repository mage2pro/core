// 2016-07-16
// Our custom module allows us to use HTML tags in diagnostic messages.
// @see https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L105-L125
define(['uiLayout'], function(l) {return function() {l([{
	component: 'Magento_Ui/js/view/messages',
	config: {
		messageContainer: this.messageContainer
		// 2016-07-16 It is the single line we have changed in the parent module.
		,template: 'Df_Checkout/messages'
	},
	displayArea: 'messages',
	name: this.name + '.messages',
	parent: this.name
}]);return this;};});