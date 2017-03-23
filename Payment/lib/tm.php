<?php
use Df\Payment\TM;
/**
 * 2017-03-23
 * @used-by \Df\PaypalClone\BlockInfo::responseF()
 * @used-by \Df\PaypalClone\Method::responseF()
 * @used-by \Dfe\AllPay\Block\Info\Offline::custom()
 * @used-by \Dfe\AllPay\Method::getInfoBlockType()
 * @used-by \Dfe\AllPay\Method::paymentOptionTitle()
 * @used-by \Dfe\SecurePay\Refund::process()
 * @param string|object $m
 * @return TM
 */
function df_tm($m) {return TM::s($m);}