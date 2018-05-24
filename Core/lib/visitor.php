<?php
use Df\Core\Visitor as V;
use Magento\Sales\Model\Order as O;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress as RA;
/**
 * 2016-05-20
 * @used-by \Df\Payment\Settings\_3DS::countries()
 * @used-by \Frugue\Shipping\Header::_toHtml()
 * @used-by \Frugue\Core\Plugin\Framework\App\FrontControllerInterface::aroundDispatch()
 * @param string|null|O $ip [optional]
 * @return V
 */
function df_visitor($ip = null) {return V::sp(df_is_o($ip) ? $ip->getRemoteIp() : $ip);}

/**
 * @return string
 * @used-by df_sentry_m()
 * @used-by \Df\Core\Visitor::sp()
 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
 */
function df_visitor_ip() {
	/** @var RA $a */ $a = df_o(RA::class); return df_my_local() ? '92.243.166.8' : $a->getRemoteAddress();
}

/**
 * 2017-11-01 It returns a string like «en_US».
 * https://stackoverflow.com/a/22334417
 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
 * @return string
 */
function df_visitor_locale() {return \Locale::acceptFromHttp(dfa($_SERVER, 'HTTP_ACCEPT_LANGUAGE'));}