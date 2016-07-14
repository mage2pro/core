<?php
use Df\Checkout\Model\Session as DfSession;
use Magento\Checkout\Model\Session;
use Magento\Framework\Phrase;
/**
 * 2016-07-14
 * @param string|Phrase $text
 * @return void
 */
function df_checkout_error($text) {df_checkout_message($text, false);}

/**
 * 2016-07-14
 * @param string|Phrase $text
 * @param bool $success
 * @return void
 */
function df_checkout_message($text, $success) {
	/** @var array(array(string => bool|Phrase)) $messages */
	$messages = df_checkout_session()->getMessagesDf();
	$messages[]= ['text' => df_phrase($text), $success];
	df_checkout_session()->setMessagesDf($messages);
}

/**
 * 2016-05-06
 * @return Session|DfSession
 */
function df_checkout_session() {return df_o(Session::class);}

/**
 * 2016-07-05
 * @return string
 */
function df_url_checkout_success() {return df_url('checkout/onepage/success');}

