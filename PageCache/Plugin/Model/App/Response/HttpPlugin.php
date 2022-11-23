<?php
namespace Df\PageCache\Plugin\Model\App\Response;
use Magento\Framework\App\Response\Http;
use Magento\PageCache\Model\App\Response\HttpPlugin as Sb;
# 2021-04-22
# «Cannot modify header information - headers already sent by
# (output started at vendor/magento/module-downloadable/Helper/Download.php:285)
# in vendor/magento/framework/Stdlib/Cookie/PhpCookieManager.php on line 148»:
# https://github.com/canadasatellite-ca/site/issues/5
final class HttpPlugin {
	/**
	 * 2021-04-22
	 * @see \Magento\PageCache\Model\App\Response\HttpPlugin::beforeSendResponse():
	 *		if ($subject instanceof \Magento\Framework\App\PageCache\NotCacheableInterface) {
	 *			return;
	 *		}
	 *		$subject->sendVary();
	 * https://github.com/magento/magento2/blob/2.4.2/lib/internal/Magento/Framework/App/Response/HeaderManager.php#L34-L46
	 */
	function aroundBeforeSendResponse(Sb $sb, \Closure $f, Http $http):void {
		if (!headers_sent()) {
			$f($http);
		}
	}
}