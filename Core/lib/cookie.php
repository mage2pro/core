<?php
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Store\Model\StoreCookieManager;
use Magento\Store\Api\StoreCookieManagerInterface;
/**
 * 2016-11-07
 * 2022-10-29 @deprecated It is unused.
 * @param string|null $d [optional]
 * @return string|null
 */
function df_cookie_get(string $name, $d = null) {return df_cookie_m()->getCookie($name, $d);}

/**
 * 2016-06-06
 * @used-by df_cookie_get()
 * @used-by df_cookie_set_js()
 * @return CookieManagerInterface|PhpCookieManager
 */
function df_cookie_m() {return df_o(CookieManagerInterface::class);}

/**
 * 2016-06-06
 * @used-by df_cookie_metadata_standard()
 */
function df_cookie_metadata():CookieMetadataFactory {return df_o(CookieMetadataFactory::class);}

/**
 * 2016-06-06
 * @used-by df_cookie_set_js()
 */
function df_cookie_metadata_standard():PublicCookieMetadata {
	$r = df_cookie_metadata()->createPublicCookieMetadata(); /** @var PublicCookieMetadata $r */
	$r->setDurationOneYear();
	$r->setPath('/');
	$r->setHttpOnly(false);
	return $r;
}

/**
 * 2016-12-02
 * 1) Устанавливает куку, которая будет доступна из JavaScript.
 * 2) Cookie vs Session: http://stackoverflow.com/questions/6253633
 * @used-by Dfe\AmazonLogin\Controller\Index\Index::postProcess()
 */
function df_cookie_set_js(string $k, string $v):void {df_cookie_m()->setPublicCookie($k, $v, df_cookie_metadata_standard());}

/**
 * 2015-11-04
 * @used-by df_store()
 * @return StoreCookieManagerInterface|StoreCookieManager
 */
function df_store_cookie_m() {return df_o(StoreCookieManagerInterface::class);}