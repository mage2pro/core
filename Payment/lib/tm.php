<?php
use Df\Payment\TM;
use Df\Payment\W\Event;
/**
 * 2017-03-23
 * @used-by df_tmf()
 * @used-by Df\Payment\Block\Info::tm()
 * @used-by Df\Payment\Choice::tm()
 * @used-by Df\Payment\Facade::tm()
 * @used-by Df\StripeClone\Block\Info::prepare()
 * @used-by Dfe\AlphaCommerceHub\API\Facade\BankCard::op()
 * @used-by Dfe\AlphaCommerceHub\Method::_refund()
 * @used-by Dfe\AlphaCommerceHub\W\Reader::reqFilter()
 * @used-by Dfe\SecurePay\Refund::process()
 * @used-by Dfe\SecurePay\Signer\Response::values()
 * @param string|object $m
 */
function df_tm($m):TM {return TM::s($m);}

/**
 * 2017-03-23
 * @used-by Dfe\AllPay\Method::getInfoBlockType()
 * @param string|object $m
 * @return Event|string|null
 */
function df_tmf($m, string ...$k) {return df_tm($m)->responseF(...$k);}