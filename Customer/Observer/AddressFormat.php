<?php
namespace Df\Customer\Observer;
use Df\Payment\Method;
use Magento\Customer\Model\Address\AbstractAddress as AA;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer as O;
use Magento\Framework\Event\ObserverInterface;
use Magento\Payment\Model\MethodInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address as OA;
use Magento\Sales\Model\Order\Payment as OP;
/**
 * 2016-07-27
 * Событие: customer_address_format
 * Цель обработчика — форматирование платёжного адреса в том случае,
 * когда платёжный адрес не был заполнен покупателем
 * по той причине, что для моего конкретного платёжного модуля платёжный адрес не требовался
 * и не запрашивался у покупателя.
 * В такой ситуации от платёжного адреса имеется только адрес электронной почты.
 * Его показывать тоже нет смысла, потому что он был взят из данных покупателя,
 * и уже, как правило, присутствует в интерфейсе в блоке данных покупателя.
 *
 * How is the «customer_address_format» event fired? https://mage2.pro/t/1903
 *
 * @see \Magento\Sales\Model\Order\Address\Renderer::format()
 * @see \Magento\Customer\Model\Address\AbstractAddress::format()
 */
class AddressFormat implements ObserverInterface {
	/**
	 * @override
	 * @see ObserverInterface::execute()
	 * @used-by \Magento\Framework\Event\Invoker\InvokerDefault::_callObserverMethod()
	 * @param O $o
	 * @return void
	 */
	public function execute(O $o) {
		/** @var AA|OA $address */
		$address = $o['address'];
		/**
		 * 2016-07-27
		 * Если адрес не является экземпляром @see \Magento\Sales\Model\Order\Address,
		 * то мы попали сюда из метода @see \Magento\Customer\Model\Address\AbstractAddress::format()
		 * В комментарии к этому методу сказано, что он устаревший.
		 * Более того, мы в этом случае не сможем получить заказ и способ оплаты,
		 * и таким образом не сможем провернуть логику нашего метода.
		 */
		if ($address instanceof OA) {
			/** @var Order $order */
			$order = $address->getOrder();
			/** @var OP|null $payment */
			$payment = $order->getPayment();
			if ($payment) {
				/** @var Method|MethodInterface $method */
				$method = $payment->getMethodInstance();
				if ($method instanceof Method && !$method->ss()->askForBillingAddress()) {
					/** @var DataObject $type */
					$type = $o['type'];
					/**
					 * 2016-07-27
					 * Если в будущем мы захотим написать что-оибо более объёмное,
					 * то можно поставить ещё 'escape_html' => false
					 */
					$type->addData([
						'escape_html' => false
						,'default_format' => __(
							!df_is_backend()
							? 'Not used.'
							: 'The customer was not asked for it.'
						)
					]);
				}
			}
		}
	}
}


