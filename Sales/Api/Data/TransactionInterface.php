<?php
namespace Df\Sales\Api\Data;
/**
 * 2016-07-14
 * В версиях Magento ранее 2.1.0 константы TYPE_* размещены в классе
 * @see \Magento\Sales\Model\Order\Payment\Transaction
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Model/Order/Payment/Transaction.php#L29-L43
 *
 * В версиях Magento от 2.1.0 константы TYPE_* размещены в интерфейсе
 * @see \Magento\Sales\Api\Data\TransactionInterface
 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Api/Data/TransactionInterface.php#L16-L30
 *
 * 2016-09-02
 * Из-за идиотского бага компилятора 2.0.9 приходится переделывать в классы те интерфейсы,
 * которые не реализуются ни одним из классов:
 * https://github.com/magento/magento2/issues/6179
 * https://code.dmitry-fedyuk.com/m2e/allpay/issues/25
 */
class TransactionInterface {
	const TYPE_AUTH = 'authorization';
	const TYPE_CAPTURE = 'capture';
	const TYPE_ORDER = 'order';
	const TYPE_PAYMENT = 'payment';
	const TYPE_REFUND = 'refund';
	const TYPE_VOID = 'void';
}


