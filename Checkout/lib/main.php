<?php
use Df\Checkout\Model\Session as DfSession;
use Df\Core\Exception as DFE;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\Phrase;
use Magento\Sales\Model\Order as O;
/**
 * 2019-04-17
 * @used-by \Mangoit\MediaclipHub\Controller\Index\AddToCart::execute()
 * @return Cart
 */
function df_cart() {return df_o(Cart::class);}

/**
 * 2016-07-14
 * @used-by dfp_error()
 * @used-by \Df\Payment\CustomerReturn::execute()
 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::p()
 * @param array(string|Phrase) ...$args
 */
function df_checkout_error(...$args) {df_checkout_message(df_format($args), false);}

/**
 * 2016-07-14
 * Сообщение показывается всего на 5 секунд, а затем скрывается: https://mage2.pro/t/1871
 * @param string|Phrase $text
 * @param bool $success
 */
function df_checkout_message($text, $success) {
	$sess = df_checkout_session(); /** @var Session|DfSession $sess */
	$messages = $sess->getDfMessages(); /** @var array(array(string => bool|Phrase)) $messages */
	/**
	 * 2016-07-14
	 * @used-by https://github.com/mage2pro/core/blob/539a6c4/Checkout/view/frontend/web/js/messages.js?ts=4#L17
	 */
	$messages[]= ['text' => df_phrase($text), 'success' => $success];
	$sess->setDfMessages($messages);
}

/**
 * 2016-05-06
 * @used-by df_checkout_message()
 * @used-by df_ci_save()
 * @used-by df_order_last()
 * @used-by df_quote()
 * @used-by \Df\Customer\Observer\CopyFieldset\OrderAddressToCustomer::execute()
 * @used-by \Df\Payment\Block\Info::ii()
 * @used-by \Df\Payment\CustomerReturn::execute()
 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
 * @used-by \Dfe\AlphaCommerceHub\W\Reader::reqFilter()
 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::p()
 * @used-by \PPCs\Core\Plugin\Checkout\Controller\Onepage\Success::beforeDispatch()
 * @return Session|DfSession
 */
function df_checkout_session() {return df_o(Session::class);}

/**
 * 2018-10-06
 * @used-by df_redirect_to_payment()
 * @used-by df_redirect_to_success()
 * @used-by \Df\Payment\Block\Info::ii()
 * @used-by \Df\Payment\W\Action::execute()
 * @used-by \Dfe\AlphaCommerceHub\W\Reader::reqFilter()
 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::p()
 * @param bool $required [optional]
 * @return O|null
 * @throws DFE
 */
function df_order_last($required = true) {
	$s = df_checkout_session(); /** @var Session|DfSession $s */
	return $s->getLastRealOrderId() ? $s->getLastRealOrder() : (!$required ? null : df_error());
}

/**
 * 2016-07-05
 * 2017-02-28
 * В настоящее время эта функция никем не используется.
 * Раньше она использовалась модулем allPay,
 * но теперь там намного лучше обработка возвращаения покупателя в магазин:
 * https://github.com/mage2pro/allpay/blob/1.1.31/Charge.php?ts=4#L365-L378
 * @return string
 */
function df_url_checkout_success() {return df_url('checkout/onepage/success');}

