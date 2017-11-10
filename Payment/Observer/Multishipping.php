<?php
namespace Df\Payment\Observer;
use Df\Payment\Method;
use Df\Payment\Token;
use Magento\Framework\Event\Observer as O;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Payment as OP;
/**
 * 2017-08-28
 * Note 1.
 * The @see \Df\Payment\Method descendants are singletons:
 * @see \Df\Payment\Method::sg()
 * https://github.com/mage2pro/core/blob/2.10.47/Payment/Method.php#L1827-L1840
 * @see \Df\Payment\Plugin\Model\Method\FactoryT::aroundCreate()
 * https://github.com/mage2pro/core/blob/2.10.47/Payment/Plugin/Model/Method/FactoryT.php#L7-L58
 *
 * Usually it works ok because we process a single payment on a HTTP request.
 * But in the multishipping scenario we process multiple payments on a HTTP request:
 * @see \Magento\Multishipping\Model\Checkout\Type\Multishipping::createOrders():
 * 		foreach ($orders as $order) {
 *			$order->place();
 * https://github.com/magento/magento2/blob/2.2.0-rc2.2/app/code/Magento/Multishipping/Model/Checkout/Type/Multishipping.php#L763-L764
 *
 * So we need to reset the payment methods cache to support the multishipping scenario correctly.
 *
 * Note 2.
 * We also implement payment tokens exchanging.
 * It allow us to use a single payment token for multiple payments.
 *
 * Note 3.
 * @used-by \Magento\Sales\Model\Order\Payment::place():
 * 		$this->_eventManager->dispatch('sales_order_payment_place_start', ['payment' => $this]);
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Model/Order/Payment.php#L295-L305
 * https://github.com/magento/magento2/blob/2.2.0-rc2.2/app/code/Magento/Sales/Model/Order/Payment.php#L327-L337
 */
final class Multishipping implements ObserverInterface {
	/**
	 * 2017-08-28
	 * @override
	 * @see ObserverInterface::execute()
	 * @used-by \Magento\Framework\Event\Invoker\InvokerDefault::_callObserverMethod()
	 * @param O $o
	 */
	function execute(O $o) {
		if (df_is_checkout_multishipping()) {
			$p = $o['payment']; /** @var OP $p */
			/**
			 * 2017-08-28
			 * I intentionally do not use @see dfp_my() here, 
			 * because this function will instantiate a @see \Df\Payment\Method instance
			 * (or use a previously cached instance). 
			 */
			if (df_starts_with(($code = $p->getMethod()), 'dfe_')) { /** @var string $code */
				Method::sgReset();
				if ($cardId = Token::exchangedGet($code)) {  /** @var string|null $cardId */
					dfp_add_info($p, [Token::KEY => $cardId]);
				}
			}
		}
	}
}