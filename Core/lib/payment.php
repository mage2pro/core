<?php
use Df\Payment\Method;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Sales\Api\Data\OrderPaymentInterface as IOP;
use Magento\Sales\Api\OrderPaymentRepositoryInterface as IRepository;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Repository;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Quote\Model\Quote\Payment as QP;

/**
 * 2016-07-12
 * @param II|I|OP|QP|null $payment
 * @return void
 */
function df_payment_apply_custom_transaction_id($payment) {
	$payment->setTransactionId($payment[Method::CUSTOM_TRANS_ID]);
	$payment->unsetData(Method::CUSTOM_TRANS_ID);
}

/**
 * 2016-07-10
 * @see \Magento\Sales\Block\Adminhtml\Transactions\Detail\Grid::getTransactionAdditionalInfo()
 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Block/Adminhtml/Transactions/Detail/Grid.php#L112-L125
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Block/Adminhtml/Transactions/Detail/Grid.php#L112-L125
 * @param II|I|OP|QP|null $payment
 * @param array(string => mixed) $values
 * @return void
 */
function df_payment_set_transaction_info($payment, array $values) {
	ksort($values);
	$payment->setTransactionAdditionalInfo(Transaction::RAW_DETAILS, $values);
}

/**
 * 2016-05-20
 * @param II|I|OP|QP $payment
 * @param array $info
 */
function df_order_payment_add(II $payment, array $info) {
	foreach ($info as $key => $value) {
		/** @var string $key */
		/** @var string $value */
		$payment->setAdditionalInformation($key, $value);
	}
}

/**
 * 2016-05-07
 * https://mage2.pro/t/1558
 * @param int $id
 * @return IOP|OP
 */
function df_order_payment_get($id) {return df_order_payment_r()->get($id);}

/**
 * 2016-05-07
 * https://mage2.pro/tags/order-payment-repository
 * @return IRepository|Repository
 */
function df_order_payment_r() {return df_o(IRepository::class);}

