<?php
use Zend_Http_Client as C;
/**
 * 2017-07-16
 * @used-by df_github_request()
 * @used-by df_oro_get_list()
 * @used-by \Df\OAuth\App::requestToken()
 * @used-by \Dfe\BlackbaudNetCommunity\Url::check()
 * @used-by \Dfe\Dynamics365\Test\OAuth::discovery()
 * @used-by \Dfe\SecurePay\Refund::process()
 * @param array(string => mixed)|null $config [optional]
 */
function df_zf_http(string $url, array $config = []):C {
	$r = new C($url, $config + [
		'timeout' => 120
		/**
		 * 2017-07-16
		 * By default it is «Zend_Http_Client»: @see C::$config
		 * https://github.com/magento/zf1/blob/1.13.1/library/Zend/Http/Client.php#L126
		 */
		,'useragent' => 'Mage2.PRO'
	]); /** @var C $r */
	if (df_check_https($url) || df_contains($url, 'localhost')) {
		df_zf_http_skip_certificate_verifications($r);
	}
	return $r;
}

/**
 * 2017-07-01
 * @used-by \Dfe\Dynamics365\API\Facade::p()
 */
function df_zf_http_last_req(C $c):string {
	$s = $c->getLastRequest(); /** @var string $s */
	/**
	 * 2017-07-13
	 * @see \Zend_Http_Client_Adapter_Socket::write():
	 *
	 *	foreach ($headers as $k => $v) {
	 * 		if (is_string($k)) $v = ucfirst($k) . ": $v";
	 * 		$request .= "$v\r\n";
	 *	}
	 * https://github.com/magento/zf1/blob/1.13.1/library/Zend/Http/Client/Adapter/Socket.php#L282-L285
	 *
	 * 	$request .= "\r\n" . $body;
	 * https://github.com/magento/zf1/blob/1.13.1/library/Zend/Http/Client/Adapter/Socket.php#L291
	 * @var string[] $sA
	 */
	$sA = explode("\r\n\r\n", $s);
	$a = df_clean(df_explode_n($sA[0])); /** @var string[] $a */
	return df_cc_n(array_merge([df_first($a)], array_map(function(string $s):string {
		if (df_starts_with($s, $b = 'Authorization:')) {
			$s = "$b <...>";
		}
		return $s;
	}, df_sort_names(df_tail($a)))), df_tail($sA));
}

/**
 * 2018-11-11
 * @used-by df_zf_http()
 * @param C $c
 */
function df_zf_http_skip_certificate_verifications(C $c):void {
	/**
	 * 2017-07-16
	 * @see \Zend_Http_Client_Adapter_Socket is the default adapter for Zend_Http_Client:
	 * @see C::$config https://github.com/magento/zf1/blob/1.13.1/library/Zend/Http/Client.php#L126
	 * The used adapter can be changed in $config, so we create another adapter.
	 */
	$c->setAdapter((new \Zend_Http_Client_Adapter_Socket)->setStreamContext(['ssl' => [
		'allow_self_signed' => true, 'verify_peer' => false
	]]));
}