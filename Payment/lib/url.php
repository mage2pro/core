<?php
use Df\Payment\Url;
/**
 * 2017-03-23
 * @used-by \Df\Payment\Init\Action::action()
 * @used-by \Dfe\AllPay\Block\Info\BankCard::allpayAuthCode()
 * @used-by \Dfe\SecurePay\Refund::process()
 * @param string|object $m
 * @param string $url
 * @param string[] $stages
 * @param bool $test [optional]
 * @param mixed[] ...$args [optional]
 * @return string
 */
function dfp_url($m, $url, array $stages = [], $test = null, ...$args) {return
	Url::s($m, $stages)->url($url, $test, ...$args)
;}