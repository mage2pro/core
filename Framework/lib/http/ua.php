<?php

/**
 * 2018-11-23 https://stackoverflow.com/a/53446950
 * 2020-01-19 @deprecated It is unused.
 * @see df_is_google_ua()
 */
function df_is_google_page_speed():bool {return df_request_ua('Chrome-Lighthouse');}

/**
 * 2020-01-19 https://support.google.com/webmasters/answer/1061943
 * @see df_is_google_page_speed()
 * @used-by \Frugue\Store\Plugin\Framework\App\FrontControllerInterface::aroundDispatch()
 */
function df_is_google_ua():bool {return df_request_ua(
	'APIs-Google', 'Mediapartners-Google', 'AdsBot-Google', 'Googlebot', 'FeedFetcher-Google'
	,'DuplexWeb-Google', 'Google Favicon'
);}

/**
 * 2016-12-25
 * 2017-02-18 Модуль Checkout.com раньше использовал dfa($_SERVER, 'HTTP_USER_AGENT')
 * @used-by df_context()
 * @used-by df_is_google_page_speed()
 * @used-by df_is_google_ua()
 * @used-by \Dfe\CheckoutCom\Charge::metaData()
 * @used-by \Dfe\Sift\Payload\Browser::p()
 * @used-by \Dfe\Spryng\P\Charge::p()
 * @used-by \Stock2Shop\OrderExport\Payload::visitor()
 * @used-by vendor/emipro/socialshare/view/frontend/templates/socialshare.phtml (dxmoto.com)
 * https://github.com/dxmoto/site/issues/103
 * @param string ...$s [optional]
 * @return string|bool
 */
function df_request_ua(...$s) {
	$r = df_request_header('user-agent'); /** @var string $r */
	return !$s ? $r : df_contains($r, ...$s);
}