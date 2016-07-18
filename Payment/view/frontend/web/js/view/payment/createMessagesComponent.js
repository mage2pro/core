// 2016-07-18
define(['uiLayout'], function(layout) {
	/**
	 * 2016-07-16
	 * Перекрываем ради установки своего шаблона,
	 * который поддерживает теги HTML в диагностических сообщениях.
	 * @override
	 * @see https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Checkout/view/frontend/web/js/view/payment/default.js#L105-L125
	 * @returns {Component} Chainable
	 */
	return function() {
		layout([{
			parent: this.name,
			name: this.name + '.messages',
			displayArea: 'messages',
			component: 'Magento_Ui/js/view/messages',
			config: {
				messageContainer: this.messageContainer
				/**
				 * 2016-07-16
				 * Добавление этого параметра — это единственное,
				 * что мы поменяли в родительском методе.
				 */
				,template: 'Df_Checkout/messages'
			}
		}]);
		return this;
	};
});