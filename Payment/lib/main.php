<?php
use Df\Payment\Method as M;
use Magento\Directory\Model\Currency;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Sales\Api\Data\OrderPaymentInterface as IOP;
use Magento\Sales\Api\OrderPaymentRepositoryInterface as IRepository;
use Magento\Sales\Model\Order as O;
use Magento\Quote\Model\Quote as Q;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Repository;
use Magento\Sales\Model\Order\Payment\Transaction as T;
use Magento\Quote\Model\Quote\Payment as QP;
/**
 * 2016-05-20
 * @see df_customer_info_add()
 * @used-by \Df\Payment\Method::iiaAdd()
 * @param II|I|OP|QP $payment
 * @param array $info
 */
function dfp_add_info(II $payment, array $info) {
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
function dfp_by_trans(T $t) {return dfp_get($t->getPaymentId());}

/**
 * 2016-11-15
 * @param O|Q $oq
 * @return Currency
 */
function dfp_currency($oq) {return
	$oq instanceof O ? $oq->getOrderCurrency() : (
		$oq instanceof Q ? df_currency($oq->getQuoteCurrencyCode()) : df_error()
	)
;}

/**
 * 2016-07-14
 * Поддержка тегов HTML обеспечивается шаблоном Df_Checkout/messages
 * @param string|null $message [optional]
 * @return void
 */
function dfp_error($message = null) {df_checkout_error(dfp_error_message($message));}

/**
 * 2016-08-19
 * @used-by dfp_error()
 * @used-by \Df\Payment\PlaceOrderInternal::message()
 * @used-by \Dfe\Stripe\Exception::messageC()
 * @param string|null $message [optional]
 * @return string
 */
function dfp_error_message($message = null) {return nl2br(df_cc_n(
	__("Sorry, the payment attempt is failed.")
	, $message ? __("The payment service's message is «<b>%1</b>».", $message) : null
	,__("Please try again, or try another payment method.")
));}

/**
 * 2016-05-07
 * https://mage2.pro/t/1558
 * @param int $id
 * @return IOP|OP
 */
function dfp_get($id) {return dfp_r()->get($id);}

/**
 * 2016-08-08
 * @used-by \Df\Payment\Charge::iia()
 * @used-by \Df\Payment\Method::iia()
 * @param II|OP|QP $payment
 * @param string|string[]|null $keys  [optional]
 * @return mixed|array(string => mixed)
 */
function dfp_iia(II $payment, $keys = null) {
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
function dfp_is_my(II $payment) {return dfp_method_is_my($payment->getMethodInstance());}

/**
 * 2016-11-17
 * @used-by df_trans_is_test()
 * @param II|OP|QP $p
 * @return bool
 */
function dfp_is_test(II $p) {return dfp_iia($p, M::II__TEST);}

/**
 * 2016-05-07
 * https://mage2.pro/tags/order-payment-repository
 * @return IRepository|Repository
 */
function dfp_r() {return df_o(IRepository::class);}

/**
 * 2016-09-08
 * @param string|object $caller
 * @param string|mixed[] $data
 * @param string|null $suffix [optional]
 * @return void
 */
function dfp_report($caller, $data, $suffix = null) {
	$data = !is_array($data) ? $data : df_json_encode_pretty($data);
	df_sentry("[Payment] $suffix", [
		'Payment Method' => dfp_method_title($caller)
		,'Payment Data' => $data
	]);
	df_report(
		df_ccc('--', 'mage2.pro/' . dfp_method_code($caller) . '-{date}--{time}', $suffix) .  '.log'
		,$data
	);
}

/**
 * 2016-08-14
 * @see dfp_webhook_case()
 * @used-by \Df\Payment\R\Response::payment()
 * @used-by \Dfe\CheckoutCom\Handler\Charge::paymentByTxnId()
 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::p()
 * @param II|OP|QP $payment
 * @param string $id
 * @return void
 */
function dfp_trans_id(II $payment, $id) {$payment[M::CUSTOM_TRANS_ID] = $id;}

/**
 * 2016-08-14
 * @see dfp_trans_id()
 * @used-by \Dfe\CheckoutCom\Handler\Charge::payment()
 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::p()
 * @used-by \Dfe\Stripe\Handler\Charge::payment()
 * @used-by \Dfe\TwoCheckout\Handler\Charge::payment()
 * @param II|OP|QP $payment
 * @return void
 */
function dfp_webhook_case(II $payment) {$payment[M::WEBHOOK_CASE] = true;}

/**
 * 2016-07-10
 * @see \Magento\Sales\Block\Adminhtml\Transactions\Detail\Grid::getTransactionAdditionalInfo()
 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Block/Adminhtml/Transactions/Detail/Grid.php#L112-L125
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Block/Adminhtml/Transactions/Detail/Grid.php#L112-L125
 * @param II|OP|QP|null $payment
 * @param array(string => mixed) $values
 * @return void
 */
function dfp_set_transaction_info(II $payment, array $values) {
	$payment->setTransactionAdditionalInfo(T::RAW_DETAILS, df_ksort($values));
}