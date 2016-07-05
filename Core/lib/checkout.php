<?php
use Magento\Checkout\Model\Session;
/**
 * 2016-05-06
 * @return Session
 */
function df_checkout_session() {return df_o(Session::class);}

/**
 * 2016-07-05
 * @return string
 */
function df_url_checkout_success() {return df_url('checkout/onepage/success');}

