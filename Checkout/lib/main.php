<?php
use Df\Checkout\Model\Session as DfSession;
use Magento\Checkout\Model\Session;
use Magento\Framework\Phrase;
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
 * @used-by \Df\Customer\Observer\CopyFieldset\OrderAddressToCustomer::execute()
 * @used-by \Df\Payment\Block\Info::ii()
 * @used-by \Df\Payment\CustomerReturn::execute()
 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
 * @used-by \Dfe\AlphaCommerceHub\W\Reader::reqFilter()
 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::p()
 * @used-by df_quote()
 * @return Session|DfSession
 */
function df_checkout_session() {return df_o(Session::class);}

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

