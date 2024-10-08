<?php
/**
 * 2019-09-08
 * @used-by df_n_get()
 * @used-by df_n_set()
 * @used-by df_prop()
 * @used-by df_prop_k()
 * @used-by CanadaSatellite\Bambora\Session::failedCount() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/14)
 * @used-by Df\API\Client::logging()
 * @used-by Df\API\FacadeOptions::resC()
 * @used-by Df\API\FacadeOptions::silent()
 * @used-by Df\Checkout\Session::customer()
 * @used-by Df\Checkout\Session::messages()
 * @used-by Df\Core\Exception::context()
 * @used-by Df\Core\Json::bSort()
 * @used-by Df\Customer\Session::needConfirm()
 * @used-by Df\Customer\Session::ssoId()
 * @used-by Df\Customer\Session::ssoProvider()
 * @used-by Df\Paypal\Plugin\Model\Payflow\Service\Gateway::req()
 * @used-by Df\Zf\Validate::v()
 * @used-by Dfe\Omise\Exception\Charge::context()
 * @used-by Dfe\Sift\API\Client::cfg()
 * @used-by Dfe\TBCBank\Session::data()
 * @used-by Frugue\Core\Session::country()
 * @used-by Frugue\Core\Session::redirected()
 * @used-by Inkifi\Pwinty\API\Entity\Image::attributes()
 * @used-by Inkifi\Pwinty\API\Entity\Image::copies()
 * @used-by Inkifi\Pwinty\API\Entity\Image::sizing()
 * @used-by Inkifi\Pwinty\API\Entity\Image::type()
 * @used-by Inkifi\Pwinty\API\Entity\Image::url()
 * @used-by Inkifi\Pwinty\API\Entity\Order::magentoOrder()
 * @used-by Wolf\Filter\Customer::categoryPath()
 * @used-by Wolf\Filter\Customer::garage()
 */
const DF_N = 'df-null';

/**
 * @used-by Df\Payment\Init\Action::preconfigured()
 * @param mixed|string $v
 * @return mixed|null
 */
function df_n_get($v) {return DF_N === $v ? null : $v;}

/**
 * 2022-10-27 @deprecated It is unused.
 * @param mixed|null $v
 * @return mixed|string
 */
function df_n_set($v) {return is_null($v) ? DF_N : $v;}

/**
 * 2019-04-05
 * 2019-09-08 Now it supports static properties.
 * 2023-12-31 For static properties, pass `null` as the first argument, e.g.:
 * 		@see \Df\Core\Json::bSort()
 * 		@see \Df\Paypal\Plugin\Model\Payflow\Service\Gateway::req()
 * @used-by CanadaSatellite\Bambora\Response::authCode() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by CanadaSatellite\Bambora\Response::avsResult() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by CanadaSatellite\Bambora\Response::errorType() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by CanadaSatellite\Bambora\Response::messageId() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by CanadaSatellite\Bambora\Response::messageText (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by CanadaSatellite\Bambora\Response::trnApproved() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by CanadaSatellite\Bambora\Response::trnId() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by CanadaSatellite\Bambora\Session::failedCount() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/14)
 * @used-by Df\API\Client::logging()
 * @used-by Df\API\FacadeOptions::resC()
 * @used-by Df\API\FacadeOptions::silent()
 * @used-by Df\Checkout\Session::customer()
 * @used-by Df\Checkout\Session::messages()
 * @used-by Df\Core\Exception::context()
 * @used-by Df\Core\Json::bSort()
 * @used-by Df\Customer\Session::needConfirm()
 * @used-by Df\Customer\Session::ssoId()
 * @used-by Df\Customer\Session::ssoProvider()
 * @used-by Df\Paypal\Plugin\Model\Payflow\Service\Gateway::req()
 * @used-by Df\Zf\Validate::v()
 * @used-by Dfe\Sift\API\Client::cfg()
 * @used-by Dfe\TBCBank\Session::data()
 * @used-by Frugue\Core\Session::country()
 * @used-by Frugue\Core\Session::redirected()
 * @used-by Inkifi\Pwinty\API\Entity\Image::attributes()
 * @used-by Inkifi\Pwinty\API\Entity\Image::copies()
 * @used-by Inkifi\Pwinty\API\Entity\Image::sizing()
 * @used-by Inkifi\Pwinty\API\Entity\Image::type()
 * @used-by Inkifi\Pwinty\API\Entity\Image::url()
 * @used-by Inkifi\Pwinty\API\Entity\Order::magentoOrder()
 * @used-by Wolf\Filter\Customer::categoryPath()
 * @used-by Wolf\Filter\Customer::garage()
 * @see df_no_rec()
 * @see dfc()
 * @param object|null|ArrayAccess $o
 * @param mixed|string $v [optional]
 * @param string|mixed|null $d [optional]
 * @return mixed|object|ArrayAccess|null
 */
function df_prop($o, $v = DF_N, $d = null, string $type = '') {/** @var object|mixed|null $r */
	/**
	 * 2019-09-08
	 * 1) My 1st solution was comparing $v with `null`,
	 * but it is wrong because it fails for a code like `$object->property(null)`.
	 * 2) My 2nd solution was using @see func_num_args():
	 * «How to tell if optional parameter in PHP method/function was set or not?»
	 * https://stackoverflow.com/a/3471863
	 * It is wrong because the $v argument is alwaus passed to df_prop()
	 */
	$isGet = DF_N === $v; /** @vae bool $isGet */
	if ('int' === $d) {
		$type = $d; $d = null;
	}
	/** @var string $k */
	if (!is_null($o)) {
		$r = df_prop_k($o, df_caller_f(), $v, $d);
	}
	else {# 2019-09-08 A static call.
		$k = df_caller_m();
		# 2023-08-04
		# «dfa(): Argument #1 ($a) must be of type array, null given,
		# called in vendor/mage2pro/core/Core/lib/lang/prop.php on line 109»: https://github.com/mage2pro/core/issues/314
		static $s = []; /** @var array(string => mixed) $s */
		if ($isGet) {
			$r = dfa($s, $k, $d);
		}
		else {
			$s[$k] = $v;
			$r = null;
		}
	}
	return $isGet && 'int' === $type ? intval($r) : $r;
}

/**
 * 2022-10-28
 * 2023-07-29
 * 1) @noinspection PhpVariableVariableInspection
 * 2) "Suppress the «Variable variable used» inspection for the code intended for PHP < 8.2":
 * https://github.com/mage2pro/core/issues/294
 * @used-by df_prop()
 * @used-by Df\Backend\Model\Auth::loginByEmail()
 * @used-by Df\User\Plugin\Model\User::aroundAuthenticate()
 * @param object|ArrayAccess $o
 * @param mixed|string $v [optional]
 * @param string|mixed|null $d [optional]
 * @return mixed|object|ArrayAccess|null
 */
function df_prop_k($o, string $k, $v = DF_N, $d = null) {/** @var object|mixed|null $r */
	/**
	 * 2019-09-08
	 * 1) My 1st solution was comparing $v with `null`,
	 * but it is wrong because it fails for a code like `$object->property(null)`.
	 * 2) My 2nd solution was using @see func_num_args():
	 * «How to tell if optional parameter in PHP method/function was set or not?»
	 * https://stackoverflow.com/a/3471863
	 * It is wrong because the $v argument is alwaus passed to df_prop()
	 */
	$isGet = DF_N === $v; /** @vae bool $isGet */
	/**
	 * 2024-05-20
	 * 1) "Remove the special `ArrayAccess` case's handling from `df_prop_k()`": https://github.com/mage2pro/core/issues/377
	 * 2) I removed the special @see ArrayAccess case's handling,
	 * because sometimes (e.g., in the @see \Df\Core\Exception case)
	 * I do not want the object's primary data to be polluted by `df_prop_k()`.
	 * The previous code:
	 * 		if ($o instanceof ArrayAccess) {
	 * 			if ($isGet) {
	 * 				$r = !$o->offsetExists($k) ? $d : $o->offsetGet($k);
	 * 			}
	 * 			else {
	 *				$o->offsetSet($k, $v);
	 * 				$r = $o;
	 * 			}
	 * 		}
	 * https://github.com/mage2pro/core/blob/10.9.9/Core/lib/lang/prop.php#L153-L161
	 */
	$a = '_' . __FUNCTION__; /** @var string $a */
	/**
	 * 2022-10-18
	 * 1) Dynamic properties are deprecated since PHP 8.2:
	 * https://php.net/manual/migration82.deprecated.php#migration82.deprecated.core.dynamic-properties
	 * https://wiki.php.net/rfc/deprecate_dynamic_properties
	 * 2) @see dfc()
	 * 2024-09-10
	 * 1) "The creation of dynamic properties is deprecated in PHP ≥ 8.2": https://df.tips/t/2360
	 * 2) "Document the deprecation of dynamic properties in PHP ≥ 8.2": https://github.com/mage2pro/core/issues/434
	 */
	static $hasWeakMap; /** @var bool $hasWeakMap */
	# 2024-01-10
	# 1) The previous code: `@class_exists('WeakMap')`.
	# 2) I changed the code by analogy with
	# https://github.com/thehcginstitute-com/m1/blob/2024-01-10-2/app/code/local/Df/Core/lib/cache/dfc.php#L58-L62
	if (!($hasWeakMap = !is_null($hasWeakMap) ? $hasWeakMap : class_exists('WeakMap', false))) {
		if (!isset($o->$a)) {
			$o->$a = [];
		}
		if ($isGet) {
			$r = dfa($o->$a, $k, $d);
		}
		else {
			# 2022-10-18
			# The previous code was:
			# 		$prop =& $o->$a;
			#		$prop[$k] = $v;
			# The new code works correctly in PHP ≤ 8.2: https://3v4l.org/8agSI1
			$o->{$a}[$k] = $v;
			$r = $o;
		}
	}
	else {
		static $map; /** @var WeakMap $map */
		# 2024-03-23
		# "[IntelliJ IDEA] «'WeakMap' is available starting with 8.0 PHP version»":
		# https://github.com/thehcginstitute-com/m1/issues/529
		$map = $map ?: new WeakMap;
		if (!$map->offsetExists($o)) {
			$map[$o] = [];
		}
		# 2022-10-17 https://3v4l.org/6cVAu
		$map2 =& $map[$o]; /** @var array(string => mixed) $map2 */
		if (!isset($map2[$a])) {
			$map2[$a] = [];
		}
		# 2022-10-18 https://3v4l.org/1tS4v
		$prop =& $map2[$a]; /** array(string => mixed) $prop */
		if ($isGet) {
			$r = dfa($prop, $k, $d);
		}
		else {
			$prop[$k] = $v;
			$r = $o;
		}
	}
	return $r;
}