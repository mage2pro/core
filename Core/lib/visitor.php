<?php
use Df\Core\Visitor as V;
use Magento\Sales\Model\Order as O;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress as RA;
/**
 * 2016-05-20
 * @used-by \Dfe\TwoCheckout\Address::visitor()
 * @used-by \Df\Payment\Settings\_3DS::countries()
 * @used-by \Df\Sso\CustomerReturn::register()
 * @used-by \Frugue\Shipping\Header::_toHtml()
 * @used-by \Frugue\Store\Plugin\Framework\App\FrontControllerInterface::aroundDispatch()
 * @param string|null|O $ip [optional]
 */
function df_visitor($ip = null):V {return V::sp(df_is_o($ip) ? $ip->getRemoteIp() : $ip);}

/**
 * @used-by df_context()
 * @used-by df_sentry_m()
 * @used-by df_visitor_ip()
 * @used-by \Df\Core\Visitor::sp()
 * @used-by \Df\Security\BlackList::ip()
 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
 * @used-by \Dfe\Sift\API\B\Event::p()
 * @used-by \Dfe\TBCBank\API\Facade::check()
 * @used-by \Dfe\TBCBank\Charge::common()
 * @used-by \Dfe\TBCBank\Test\CaseT\Init::transId()
 * @used-by \Dfe\TBCBank\Test\CaseT\Regular::transId()
 * @used-by \CanadaSatellite\Bambora\Facade::api() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/175)
 * @used-by \Stock2Shop\OrderExport\Payload::visitor()
 */
function df_visitor_ip():string {
	/** @var RA $a */ $a = df_o(RA::class);
	# 2021-06-11
	# 1) «Ensure that the Customer IP address is being passed in the API request for all transactions»:
	# https://github.com/canadasatellite-ca/site/issues/175
	# 2) https://stackoverflow.com/a/14985633
	return df_my_local() ? '158.181.235.66' : dfa($_SERVER, 'HTTP_CF_CONNECTING_IP', $a->getRemoteAddress());
}

/**
 * 2017-11-01 It returns a string like «en_US».
 * https://stackoverflow.com/a/22334417
 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
 */
function df_visitor_locale():string {return \Locale::acceptFromHttp(dfa($_SERVER, 'HTTP_ACCEPT_LANGUAGE'));}