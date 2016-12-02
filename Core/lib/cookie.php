<?php
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Store\Model\StoreCookieManager;
use Magento\Store\Api\StoreCookieManagerInterface;
/**
 * 2016-11-07
 * @param string $name
 * @param string|null $d [optional]
 * @return string|null
 */
function df_cookie_get($name = null, $d = null) {return df_cookie_m()->getCookie($name, $d);}

/**
 * 2016-06-06
 * @return CookieManagerInterface|PhpCookieManager
 */
function df_cookie_m() {return df_o(CookieManagerInterface::class);}

/**
 * 2016-06-06
 * @return CookieMetadataFactory
 */
function df_cookie_metadata() {return df_o(CookieMetadataFactory::class);}

/**
 * 2016-06-06
 * @return PublicCookieMetadata
 */
function df_cookie_metadata_standard() {
	/** @var PublicCookieMetadata $result */
	$result = df_cookie_metadata()->createPublicCookieMetadata();
	$result->setDurationOneYear();
	$result->setPath('/');
	$result->setHttpOnly(false);
	return $result;
}

/**
 * 2016-12-02
 * Cookie VS Session: http://stackoverflow.com/questions/6253633
 * @used-by \Dfe\AmazonLogin\Controller\Index\Index::postProcess()
 * Устанавливает куку, которая будет доступна из JavaScript.
 * @param string $name
 * @param string $value
 */
function df_cookie_set_js($name, $value) {
	df_cookie_m()->setPublicCookie($name, $value, df_cookie_metadata_standard());
}

/**
 * 2015-11-04
 * @return StoreCookieManagerInterface|StoreCookieManager
 */
function df_store_cookie_m() {return df_o(StoreCookieManagerInterface::class);}

