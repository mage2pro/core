<?php
use Magento\Payment\Model\InfoInterface as II;
use Magento\Sales\Api\Data\OrderPaymentInterface as IOP;
use Magento\Sales\Api\OrderPaymentRepositoryInterface as IRepository;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Repository;
use Magento\Quote\Model\Quote\Payment as QP;

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

