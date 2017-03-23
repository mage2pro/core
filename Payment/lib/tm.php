<?php
use Df\Payment\TM;
use Df\Payment\W\Event;
/**
 * 2017-03-23
 * @used-by df_tmf()
 * @used-by \Df\Payment\Block\Info::isWait()
 * @used-by \Df\StripeClone\Block\Info::prepare()
 * @used-by \Dfe\AllPay\Block\Info\Offline::custom()
 * @used-by \Dfe\SecurePay\Refund::process()
 * @used-by \Dfe\SecurePay\Signer\Response::values()
 * @param string|object $m
 * @return TM
 */
function df_tm($m) {return TM::s($m);}

/**
 * 2017-03-23
 * @used-by \Df\PaypalClone\BlockInfo::responseF()
 * @used-by \Dfe\AllPay\Method::getInfoBlockType()
 * @used-by \Dfe\AllPay\Method::paymentOptionTitle()
 * @param string|object $m
 * @param string[] ...$k
 * @return Event|string|null
 */
function df_tmf($m, ...$k) {return df_tm($m)->responseF(...$k);}