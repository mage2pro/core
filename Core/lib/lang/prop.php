<?php
/**
 * 2019-09-08
 * @used-by df_n_get()
 * @used-by df_n_set()
 * @used-by df_prop()
 * @used-by df_prop_k()
 * @used-by \CanadaSatellite\Bambora\Session::failedCount() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/14)
 * @used-by \Df\API\Client::logging()
 * @used-by \Df\API\FacadeOptions::resC()
 * @used-by \Df\API\FacadeOptions::silent()
 * @used-by \Df\Core\Json::bSort()
 * @used-by \Df\Checkout\Session::customer()
 * @used-by \Df\Checkout\Session::messages()
 * @used-by \Df\Customer\Session::needConfirm()
 * @used-by \Df\Customer\Session::ssoId()
 * @used-by \Df\Customer\Session::ssoProvider()
 * @used-by \Df\Zf\Validate::v()
 * @used-by \Dfe\Sift\API\Client::cfg()
 * @used-by \Dfe\TBCBank\Session::data()
 * @used-by \Frugue\Core\Session::country()
 * @used-by \Frugue\Core\Session::redirected()
 * @used-by \Inkifi\Pwinty\API\Entity\Image::attributes()
 * @used-by \Inkifi\Pwinty\API\Entity\Image::copies()
 * @used-by \Inkifi\Pwinty\API\Entity\Image::sizing()
 * @used-by \Inkifi\Pwinty\API\Entity\Image::type()
 * @used-by \Inkifi\Pwinty\API\Entity\Image::url()
 * @used-by \Inkifi\Pwinty\API\Entity\Order::magentoOrder()
 * @used-by \Wolf\Filter\Customer::categoryPath()
 * @used-by \Wolf\Filter\Customer::garage()
 */
const DF_N = 'df-null';

/**
 * @used-by \Df\Payment\Init\Action::preconfigured()
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
 * @used-by \CanadaSatellite\Bambora\Response::authCode() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by \CanadaSatellite\Bambora\Response::avsResult() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by \CanadaSatellite\Bambora\Response::errorType() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by \CanadaSatellite\Bambora\Response::messageId() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by \CanadaSatellite\Bambora\Response::messageText (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by \CanadaSatellite\Bambora\Response::trnApproved() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by \CanadaSatellite\Bambora\Response::trnId() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by \CanadaSatellite\Bambora\Session::failedCount() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/14)
 * @used-by \Df\API\Client::logging()
 * @used-by \Df\API\FacadeOptions::resC()
 * @used-by \Df\API\FacadeOptions::silent()
 * @used-by \Df\Checkout\Session::customer()
 * @used-by \Df\Checkout\Session::messages()
 * @used-by \Df\Core\Json::bSort()
 * @used-by \Df\Customer\Session::needConfirm()
 * @used-by \Df\Customer\Session::ssoId()
 * @used-by \Df\Customer\Session::ssoProvider()
 * @used-by \Df\Zf\Validate::v()
 * @used-by \Dfe\Sift\API\Client::cfg()
 * @used-by \Dfe\TBCBank\Session::data()
 * @used-by \Frugue\Core\Session::country()
 * @used-by \Frugue\Core\Session::redirected()
 * @used-by \Inkifi\Pwinty\API\Entity\Image::attributes()
 * @used-by \Inkifi\Pwinty\API\Entity\Image::copies()
 * @used-by \Inkifi\Pwinty\API\Entity\Image::sizing()
 * @used-by \Inkifi\Pwinty\API\Entity\Image::type()
 * @used-by \Inkifi\Pwinty\API\Entity\Image::url()
 * @used-by \Inkifi\Pwinty\API\Entity\Order::magentoOrder()
 * @used-by \Wolf\Filter\Customer::categoryPath()
 * @used-by \Wolf\Filter\Customer::garage()
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
 * @used-by \Df\Backend\Model\Auth::loginByEmail()
 * @used-by \Df\User\Plugin\Model\User::aroundAuthenticate()
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
	if ($o instanceof ArrayAccess) {
		if ($isGet) {
			$r = !$o->offsetExists($k) ? $d : $o->offsetGet($k);
		}
		else {
			$o->offsetSet($k, $v);
			$r = $o;
		}
	}
	else {
		$a = '_' . __FUNCTION__; /** @var string $a */
		/**
		 * 2022-10-18
		 * 1) Dynamic properties are deprecated since PHP 8.2:
		 * https://php.net/manual/migration82.deprecated.php#migration82.deprecated.core.dynamic-properties
		 * https://wiki.php.net/rfc/deprecate_dynamic_properties
		 * 2) @see dfc()
		 */
		static $hasWeakMap; /** @var bool $hasWeakMap */
		if (!($hasWeakMap = !is_null($hasWeakMap) ? $hasWeakMap : @class_exists('WeakMap'))) {
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
	}
	return $r;
}