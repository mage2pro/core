<?php
use Df\Payment\Url;
/**
 * 2017-03-23
 * @used-by Df\Payment\Init\Action::action()
 * @used-by Dfe\AllPay\Block\Info\BankCard::allpayAuthCode()
 * @used-by Dfe\Moip\API\Client::urlBase()
 * @used-by Dfe\SecurePay\Refund::process()
 * @used-by Dfe\Vantiv\API\Client::urlBase()
 * @param string|object $m
 * @param string[] $stages
 * @param mixed ...$a [optional]
 */
function dfp_url_api($m, string $url, array $stages = [], bool $test = null, ...$a):string {return Url::f($m, $stages)->url(
	$url, $test, ...$a
);}