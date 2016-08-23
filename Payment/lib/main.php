<?php
use Df\Payment\Method;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Payment\Model\MethodInterface as IMethod;
use Magento\Sales\Api\Data\OrderPaymentInterface as IOP;
use Magento\Sales\Api\OrderPaymentRepositoryInterface as IRepository;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Repository;
use Magento\Sales\Model\Order\Payment\Transaction as T;
use Magento\Sales\Model\Order\Payment\Transaction\Repository as TR;
use Magento\Quote\Model\Quote\Payment as QP;
/**
 * 2016-08-20
 * @see df_trans_by_payment()
 * @param T $t
 * @return IMethod|Method;
 */
function df_method_by_trans(T $t) {return df_payment_by_trans($t)->getMethodInstance();}

/**
 * 2016-08-19
 * @see df_trans_is_my()
 * @used-by df_payment_is_my()
 * @param IMethod $method
 * @return bool
 */
function df_method_is_my(IMethod $method) {return $method instanceof Method;}

/**
 * 2016-05-20
 * @see df_customer_info_add()
 * @used-by \Df\Payment\Method::iiaAdd()
 * @param II|I|OP|QP $payment
 * @param array $info
 */
function df_payment_add_info(II $payment, array $info) {
	foreach ($info as $key => $value) {
		/** @var string $key */
		/** @var string $value */
		$payment->setAdditionalInformation($key, $value);
	}
}

/**
 * 2016-08-19
 * @see df_trans_by_payment()
 * @param T $t
 * @return OP
 */
function df_payment_by_trans(T $t) {return df_order_payment_get($t->getPaymentId());}

/**
 * 2016-07-14
 * Поддержка тегов HTML обеспечивается шаблоном Df_Checkout/messages
 * @param string|null $message [optional]
 * @return void
 */
function df_payment_error($message = null) {df_checkout_error(df_payment_error_message($message));}

/**
 * 2016-08-19
 * @used-by df_payment_error()
 * @used-by \Df\Payment\PlaceOrderInternal::message()
 * @used-by \Dfe\Stripe\Exception::messageForCustomer()
 * @param string|null $message [optional]
 * @return string
 */
function df_payment_error_message($message = null) {return nl2br(df_cc_n(
	__("Sorry, the payment attempt is failed.")
	, $message ? __("The payment service's message is «<b>%1</b>».", $message) : null
	,__("Please try again, or try another payment method.")
));}

/**
 * 2016-08-08
 * @used-by \Df\Payment\Charge::iia()
 * @used-by \Df\Payment\Method::iia()
 * @param II|OP|QP $payment
 * @param string|string[]|null $keys  [optional]
 * @return mixed|array(string => mixed)
 */
function df_payment_iia(II $payment, $keys = null) {
	/** @var mixed|array(string => mixed) $result */
	if (is_null($keys)) {
		$result = $payment->getAdditionalInformation();
	}
	else {
		if (!is_array($keys)) {
			$keys = df_tail(func_get_args());
		}
		$result =
			1 === count($keys)
			? $payment->getAdditionalInformation(df_first($keys))
			: dfa_select_ordered($payment->getAdditionalInformation(), $keys)
		;
	}
	return $result;
}

/**
 * 2016-08-19
 * @see df_trans_is_my()
 * @param II|OP|QP $payment
 * @return bool
 */
function df_payment_is_my(II $payment) {return df_method_is_my($payment->getMethodInstance());}

/**
 * 2016-08-14
 * @see df_payment_webhook_case()
 * @used-by \Df\Payment\R\Response::payment()
 * @used-by \Dfe\CheckoutCom\Handler\Charge::paymentByTxnId()
 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::p()
 * @param II|OP|QP $payment
 * @param string $id
 * @return void
 */
function df_payment_trans_id(II $payment, $id) {$payment[Method::CUSTOM_TRANS_ID] = $id;}

/**
 * 2016-08-14
 * @see df_payment_trans_id()
 * @used-by \Dfe\CheckoutCom\Handler\Charge::payment()
 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::p()
 * @used-by \Dfe\Stripe\Handler\Charge::payment()
 * @used-by \Dfe\TwoCheckout\Handler\Charge::payment()
 * @param II|OP|QP $payment
 * @return void
 */
function df_payment_webhook_case(II $payment) {$payment[Method::WEBHOOK_CASE] = true;}

/**
 * 2016-07-10
 * @see \Magento\Sales\Block\Adminhtml\Transactions\Detail\Grid::getTransactionAdditionalInfo()
 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Block/Adminhtml/Transactions/Detail/Grid.php#L112-L125
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Block/Adminhtml/Transactions/Detail/Grid.php#L112-L125
 * @param II|OP|QP|null $payment
 * @param array(string => mixed) $values
 * @return void
 */
function df_payment_set_transaction_info(II $payment, array $values) {
	$payment->setTransactionAdditionalInfo(T::RAW_DETAILS, df_ksort($values));
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





