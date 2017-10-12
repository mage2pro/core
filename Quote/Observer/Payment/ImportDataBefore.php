<?php
namespace Df\Quote\Observer\Payment;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
/**
 * 2017-10-12
 * Event: `sales_quote_payment_import_data_before`
 * @see \Magento\Quote\Model\Quote\Payment::importData():
 * 1) Magento 2.2.0:
 *		$data = $this->convertPaymentData($data);
 *		$data = new \Magento\Framework\DataObject($data);
 *		$this->_eventManager->dispatch(
 *			$this->_eventPrefix . '_import_data_before',
 *			[$this->_eventObject => $this, 'input' => $data]
 *		);
 * https://github.com/magento/magento2/blob/2.2.0/app/code/Magento/Quote/Model/Quote/Payment.php#L172-L177
 *
 * 2) Magento 2.0.0:
 *		$data = new \Magento\Framework\DataObject($data);
 *		$this->_eventManager->dispatch(
 *			$this->_eventPrefix . '_import_data_before',
 *			[$this->_eventObject => $this, 'input' => $data]
 *		);
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Quote/Model/Quote/Payment.php#L146-L150
 * @singleton https://magento.stackexchange.com/questions/34395/magento-event-observers-singleton-versus-model#comment102703_34524
 */
final class ImportDataBefore implements ObserverInterface {
	/**
	 * 2017-10-12
	 * @override
	 * @see ObserverInterface::execute()
	 * @used-by \Magento\Framework\Event\Invoker\InvokerDefault::_callObserverMethod()
	 * @param Observer $ob
	 */
	function execute(Observer $ob) {self::$_data = $ob['input'];}

	/**
	 * 2017-10-12
	 * @used-by \Df\Payment\Method::isAvailable()
	 * @return DataObject|null
	 */
	static function data() {return self::$_data;}

	/**
	 * 2017-10-12
	 * @used-by data()
	 * @used-by execute()
	 * @var DataObject|null
	 */
	private static $_data;
}

