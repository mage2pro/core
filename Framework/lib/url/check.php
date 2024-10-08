<?php
use Magento\Framework\Exception\LocalizedException as LE;
use Throwable as Th; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2016-07-12
 * @used-by df_webhook()
 * @param string|Th $msg [optional]
 * @throws Th|LE
 */
function df_assert_https(string $u, $msg = null):string {return df_check_https_strict($u) ? $u : df_error(
	$msg ?: "The URL «{$u}» is invalid, because the system expects an URL which starts with «https://»."
);}

/**
 * 2016-07-16
 * @used-by df_zf_http()
 */
function df_check_https(string $u):bool {return df_starts_with(strtolower($u), 'https');}

/**
 * 2016-05-30
 * http://framework.zend.com/manual/1.12/en/zend.uri.chapter.html#zend.uri.instance-methods.getscheme
 * @uses \Zend_Uri::getScheme() always returns a lowercased value:
 * @see \Zend_Uri::factory()
 * https://github.com/zendframework/zf1/blob/release-1.12.16/library/Zend/Uri.php#L100
 * $scheme = strtolower($uri[0]);
 * @used-by df_assert_https()
 * @used-by Df\Framework\Form\Element\Url::messageForOthers()
 */
function df_check_https_strict(string $u):bool {return 'https' === df_zuri($u)->getScheme();}

/**
 * http://stackoverflow.com/a/15011528
 * http://www.php.net/manual/en/function.filter-var.php
 * filter_var('/C/A/CA559AWLE574_1.jpg', FILTER_VALIDATE_URL) returns `false`.
 * 2023-07-26
 * 1) "`df_check_url` → `df_is_url`": https://github.com/mage2pro/core/issues/276
 * 2) df_is_url('php://input') returns `true`:
 * https://github.com/mage2pro/core/issues/277
 * https://3v4l.org/mTt87
 * @used-by df_contents()
 * @used-by df_url_bp()
 */
function df_is_url(string $s):bool {return false !== filter_var($s, FILTER_VALIDATE_URL);}

/**   
 * 2017-10-16    
 * @used-by df_asset_create()
 * @used-by df_js()
 */
function df_is_url_absolute(string $u):bool {return df_starts_with($u, ['http', '//']);}

/**
 * 2018-05-11
 * df_contains(df_url(), $s)) does not work properly for some requests.
 * E.g.: df_url() for the `/us/stores/store/switch/___store/uk` request will return `<website>/us/`
 * 2021-02-23 @deprecated
 * Use @see df_rp_has() instead.
 * @see df_action_has()
 * @see df_action_is()
 * @used-by Frugue\Store\Plugin\Framework\App\FrontControllerInterface::aroundDispatch()
 * @used-by Magenest\QuickBooksDesktop\Observer\Adminhtml\Customer\Update::execute()
 * @used-by Magenest\QuickBooksDesktop\Observer\Customer\Address::execute()
 * @used-by Magenest\QuickBooksDesktop\Observer\Customer\Edit::execute()
 */
function df_url_path_contains(string $s):bool {return df_rp_has($s);}