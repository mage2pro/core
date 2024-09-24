<?php
use Magento\Framework\DataObject as _DO;

/**
 * 2024-06-03
 * 1) The `object` type requires PHP ≥ 7.2: https://github.com/mage2pro/core/issues/174#user-content-object
 * 2) We need to support PHP ≥ 7.1: https://github.com/mage2pro/core/issues/368
 * @used-by df_sc()
 * @used-by Df\Config\Backend\Serialized::processI()
 * @param string|null|array(string => mixed) $a2 [optional]
 * @param array(string => mixed) $a3 [optional]
 * @return _DO|object
 */
function df_ic(string $resultClass, $a2 = null, array $a3 = []) {
	/** @var string|null $expectedClass */ /** @var array(string => mixed) $params */
	# 2020-03-02, 2022-10-31
	# 1) Symmetric array destructuring requires PHP ≥ 7.1:
	#		[$expectedClass, $params] = is_array($a2) ? [null, $a2] : [$a2, $a3];
	# https://github.com/mage2pro/core/issues/96#issuecomment-593392100
	# We should support PHP 7.0.
	# https://3v4l.org/3O92j
	# https://php.net/manual/migration71.new-features.php#migration71.new-features.symmetric-array-destructuring
	# https://stackoverflow.com/a/28233499
	# 2024-06-06 "Use the «Symmetric array destructuring» PHP 7.1 feature": https://github.com/mage2pro/core/issues/379
	[$expectedClass, $params] = is_array($a2) ? [null, $a2] : [$a2, $a3];
	return df_ar(new $resultClass($params), $expectedClass);
}

/**
 * 2017-04-08
 * 2024-06-03
 * 1) The `object` type requires PHP ≥ 7.2: https://github.com/mage2pro/core/issues/174#user-content-object
 * 2) We need to support PHP ≥ 7.1: https://github.com/mage2pro/core/issues/368
 * @used-by df_oq_sa()
 * @used-by CanadaSatellite\Core\Plugin\Magento\Sales\Api\Data\OrderInterface::afterGetPayment() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/29)
 * @used-by Df\Widget\P\Wysiwyg::prepareElementHtml() (https://github.com/mage2pro/core/issues/392)
 * @used-by Dfe\Markdown\Plugin\Ui\Component\Form\Element\Wysiwyg::beforePrepare()
 * @used-by Doormall\Shipping\Method::collectRates()
 * @used-by Frugue\Core\Plugin\Sales\Model\Quote::afterGetAddressesCollection()
 * @used-by Frugue\Shipping\Method::collectRates()
 * @used-by KingPalm\B2B\Block\Registration::e()
 * @used-by KingPalm\B2B\Block\Registration::form()
 * @param array(string => mixed) $d [optional]
 * @return _DO|object
 */
function df_new_omd(string $c, array $d = []) {return df_om()->create($c, ['data' => $d]);}

/**
 * 2015-03-23
 * 2024-06-03
 * 1) The `object` type requires PHP ≥ 7.2: https://github.com/mage2pro/core/issues/174#user-content-object
 * 2) We need to support PHP ≥ 7.1: https://github.com/mage2pro/core/issues/368
 * @see df_ic()
 * @used-by Dfe\GingerPaymentsBase\Settings::os()
 * @used-by Df\Payment\Settings::_options()
 * @param array(string => mixed) $params [optional]
 * @return _DO|object
 */
function df_sc(string $resultClass, string $expectedClass = '', array $params = [], string $cacheKeySuffix = '') {
	static $cache; /** @var array(string => object) $cache */
	$key = $resultClass . $cacheKeySuffix; /** @var string $key */
	if (!isset($cache[$key])) {
		$cache[$key] = df_ic($resultClass, $expectedClass, $params);
	}
	return $cache[$key];
}

/**
 * 2016-08-23
 * 2017-10-08
 * isset($object->{$key}) returns false for the non-public properties: https://3v4l.org/bRAcp
 * E.g., the following code returns `0`:
 * 		class A {private $b = 3;}
 * 		$a = new A;
 * 		echo intval(isset($a->{'b'}));
 * 2022-11-17
 * `object` as an argument type is not supported by PHP < 7.2: https://github.com/mage2pro/core/issues/174#user-content-object
 * 2024-06-03 We need to support PHP ≥ 7.1: https://github.com/mage2pro/core/issues/368
 * @see dfa()
 * @used-by Alignet\Paymecheckout\Model\Client\Classic\Order\DataGetter::userCodePayme() (innomuebles.com, https://github.com/innomuebles/m2/issues/17)
 * @used-by Df\StripeClone\Facade\Charge::cardData()
 * @used-by MageSuper\Faq\Observer\CheckRecaptcha3::execute() (canadasatellite.ca)
 * @param object $o
 * @param string|int $k
 * @param mixed|callable|null $d [optional]
 * @return mixed|null
 */
function dfo($o, $k, $d = null) {return
	# 2022-10-29
	# It works even in PHP 8.2 (despite dynamic properties are deprecated since PHP 8.2): https://3v4l.org/2Q8Fm
	# https://php.net/manual/migration82.deprecated.php#migration82.deprecated.core.dynamic-properties
	isset($o->{$k}) ? $o->{$k} : df_call_if($d, $k)
;}

/**
 * 2017-07-11
 * It returns a singleton of a class from the $caller module with the $owner or $suf suffix.
 * The result should be a descendant of the $owner, and should exist (it is not defaulted to $owner).
 * 2022-10-31 `object` as a return type is not supported by PHP < 7.2: https://3v4l.org/dAmcs
 * @used-by df_oauth_app()
 * @used-by Dfe\Zoho\App::s()
 * @used-by Dfe\ZohoBI\API\Facade::s()
 * @param string|object $caller
 * @return object
 */
function dfs_con($caller, string $suf = '') {
	$bt = df_bt(0, 2)[1]; /** @var array(string => mixed) $bt */
	# 2020-02-25
	# "«Undefined index: class in vendor/mage2pro/core/Core/lib/object/objects.php on line 214»":
	# https://github.com/mage2pro/core/issues/95
	if (!($owner = dfa($bt, 'class')) && !$suf) { /** @var string|null $owner */
		df_error("The backtrace frame is invalid for dfs_con() because it lacks the `class` key:\n%s\nThe key should exist if the `suf` argument is not passed to dfs_con().", df_dump($bt));
	}
	return dfcf(function($owner, $m, string $suf) {return
		df_newa(df_con($m, $suf), $owner)
	;}, [$owner, df_module_name_c($caller), $suf ?: df_class_suffix($owner)]);
}