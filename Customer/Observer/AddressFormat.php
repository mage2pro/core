<?php
namespace Df\Customer\Observer;
use Df\Payment\Method;
use Magento\Customer\Model\Address\AbstractAddress as AA;
use Magento\Customer\Model\Address\Config as AddressConfig;
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
		 * В ядре объекты класса @see \Df\Customer\Observer\AddressFormat
		 * являются одиночками для каждого типа.
		 * Когда мы в данном методе меняем данные такого объекта,
		 * то изменения становятся глобальными, и система будет применять их
		 * и для следующих адресов, в том числе адресов доставки, не только платёжных.
		 * По этой причине нам надо очищать объекты.
		 */
		/** @var DataObject $typeDirty */
		$typeDirty = $o['type'];
		/**
		 * 2016-07-27
		 * По аналогии с @see \Magento\Sales\Model\Order\Address\Renderer::format()
		 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Model/Order/Address/Renderer.php#L51
		 * @var DataObject $typeClean
		 */
		$typeClean = $this->addressConfig()->getFormatByCode($typeDirty['code']);
		$o['type'] = $typeClean;
		/**
		 * 2016-07-27
		 * Если адрес не является экземпляром @see \Magento\Sales\Model\Order\Address,
		 * то мы попали сюда из метода @see \Magento\Customer\Model\Address\AbstractAddress::format()
		 * В комментарии к этому методу сказано, что он устаревший.
		 * Более того, мы в этом случае не сможем получить заказ и способ оплаты,
		 * и таким образом не сможем провернуть логику нашего метода.
		 */
		if ($address instanceof OA && df_address_is_billing($address)) {
			/** @var Order $order */
			$order = $address->getOrder();
			/** @var OP|null $payment */
			$payment = $order->getPayment();
			if ($payment) {
				/** @var Method|MethodInterface $method */
				$method = $payment->getMethodInstance();
				if ($method instanceof Method && !$method->ss()->askForBillingAddress()) {
					/**
					 * 2016-07-27
					 * Если в будущем мы захотим написать что-оибо более объёмное,
					 * то можно поставить ещё 'escape_html' => false
					 */
					$typeClean->addData([
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

	/**
	 * 2016-07-27
	 * Обсерверы по умолчанию являются одиночками: https://github.com/magento/magento2/blob/1.0.0-beta/lib/internal/Magento/Framework/Event/Invoker/InvokerDefault.php#L56-L60
	 * Мы используем свою одиночку, а не общую с ядром,
	 * потому что наша одиночка хранит значения по-умолчанию,
	 * а одиночка ядра загрязняется нашим хаком из метода
	 * @used-by \Df\Customer\Observer\AddressFormat::execute()
	 * Таким образом, мы используем нашу одиночку для того, чтобы очистить одиночку ядра.
	 * @return AddressConfig
	 */
	private function addressConfig() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_create(AddressConfig::class);
		}
		return $this->{__METHOD__};
	}
}


