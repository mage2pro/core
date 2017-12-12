/**
 * 2016-08-17
 * 2017-07-25
 * The initial version of this script was added on 2016-08-17 by the following commit:
 * https://github.com/mage2pro/core/commit/16ad9cd5
 * It had the following implementation:
 * 		1 === window.checkoutConfig.paymentMethods.length
 * This implementation is incorrect, because the `window.checkoutConfig.paymentMethods` array
 * is initialized only for the totally virtual quotes:
 * @see \Magento\Checkout\Model\DefaultConfigProvider::getConfig():
 * 		$output['paymentMethods'] = $this->getPaymentMethods();
 * https://github.com/magento/magento2/blob/2.2.0-RC1.5/app/code/Magento/Checkout/Model/DefaultConfigProvider.php#L294
 * @see \Magento\Checkout\Model\DefaultConfigProvider::getPaymentMethods():
 *		private function getPaymentMethods() {
 *			$paymentMethods = [];
 *			$quote = $this->checkoutSession->getQuote();
 *			if ($quote->getIsVirtual()) {
 *				foreach ($this->paymentMethodManagement->getList($quote->getId()) as $paymentMethod) {
 *					$paymentMethods[] = [
 *						'code' => $paymentMethod->getCode(),
 *						'title' => $paymentMethod->getTitle()
 *					];
 *				}
 *			}
 *			return $paymentMethods;
 *		}
 * https://github.com/magento/magento2/blob/2.2.0-RC1.5/app/code/Magento/Checkout/Model/DefaultConfigProvider.php#L606-L623
 * Moreover, a single payment method can provide multiple payment options,
 * and can add a separate rendererfor each option.
 *
 * Really the browser-based M2 part gets the list of available payment methods
 * in the `Magento_Checkout/js/action/get-payment-information` module:
 *		return storage.get(
 *			serviceUrl, false
 *		).done(function (response) {
 *			quote.setTotals(response.totals);
 *			paymentService.setPaymentMethods(methodConverter(response['payment_methods']));
 *			deferred.resolve();
 *		}).fail(function (response) {
 *			errorProcessor.process(response, messageContainer);
 *			deferred.reject();
 *		});
 *	https://github.com/magento/magento2/blob/2.2.0-RC1.5/app/code/Magento/Checkout/view/frontend/web/js/action/get-payment-information.js#L37-L46
 */
define(['jquery', 'Magento_Checkout/js/model/payment/method-list'], function($, mm) {return function() {
	/**
	 * 2017-07-25
	 * Note 1.
	 * `mm` is an observable array:
	 * http://knockoutjs.com/documentation/observableArrays.html
	 * http://knockoutjs.com/documentation/observables.html#explicitly-subscribing-to-observables
	 * «The subscribe function accepts three parameters:
	 * 1) callback is the function that is called whenever the notification happens,
	 * 2) target (optional) defines the value of this in the callback function,
	 * 3) event (optional; default is "change") is the name of the event to receive notification for.»
	 * I made it by analogy with Magento_Checkout/js/view/payment/list::initialize():
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.5/app/code/Magento/Checkout/view/frontend/web/js/view/payment/list.js#L32-L57
	 * Note 2.
	 * `arrayChange` looks like the best event (subscription topic) here.
	 * An ordinary mm.subscribe(function() {<...>}) fires only when the array is changes at the whole
	 * (not when some items are added to the array).
	 * The `arrayChange` documentation:
	 * http://blog.stevensanderson.com/2013/10/08/knockout-3-0-release-candidate-available/#array-change-subscriptions
	 */
	mm.subscribe(function() {
		var a = mm(); /** @type {Object[]} */
		// 2017-07-25
		// Note 1.
		// The `df-single-payment-method` CSS class is used here:
		// https://github.com/mage2pro/core/blob/2.9.4/Payment/view/frontend/web/main.less#L15-L25
		// Note 2. Moip adds multiple renderers.
		$('body').toggleClass('df-single-payment-method',
			// 2017-12-12
			// @todo "Provide a generic (reusable) way to a payment module
			// to provide multiple top-level payment options like currently `mage2pro/moip` does":
			// https://github.com/mage2pro/core/issues/44
			1 === a.length && -1 === ['dfe_alpha_commerce_hub', 'dfe_moip'].indexOf(a[0].method)
		);
	}, null, 'arrayChange');
};});