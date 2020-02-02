<?php
use Closure as F;
use Df\Core\Exception as DFE;
use Magento\Config\Model\Config\Structure\AbstractElement as AE;
use Magento\Framework\DataObject as _DO;
use Traversable as T;

/**
 * Раньше функция @see dfa() была универсальной:
 * она принимала в качестве аргумента $entity как массивы, так и объекты.
 * В 99.9% случаев в качестве параметра передавался массив.
 * Поэтому ради ускорения работы системы вынес обработку объектов в отдельную функцию @see dfo().
 * 2020-01-29
 * If $k is an array, then the function returns a subset of $a with $k keys in the same order as in $k.
 * We heavily rely on it, e.g., in some payment methods: @see \Dfe\Dragonpay\Signer\Request::values()
 * @see dfac()
 * @see dfad()
 * @see dfaoc()
 * @see dfo()
 * @used-by df_ci_get()
 * @used-by df_countries_options()
 * @used-by df_currencies_options()
 * @used-by df_fe_attrs()
 * @used-by df_fe_fc()
 * @used-by df_github_request()
 * @used-by df_oi_get()
 * @used-by df_package()
 * @used-by df_trd()
 * @used-by dfa_prepend()
 * @used-by dfac()
 * @used-by dfad()
 * @used-by dfaoc()
 * @used-by \Df\API\Document::a()
 * @used-by \Df\Config\Source::options()
 * @used-by \Df\Core\Controller\Index\Index::execute()
 * @used-by \Df\Framework\Form\Element\Fieldset::select()
 * @used-by \Df\Framework\Request::extra()
 * @used-by \Df\GoogleFont\Font\Variant\Preview\Params::fromRequest()
 * @used-by \Df\Payment\Charge::metadata()
 * @used-by \Df\Payment\W\Reader::r()
 * @used-by \Df\Payment\W\Reader::test()
 * @used-by \Df\PaypalClone\Signer::v()
 * @used-by \Df\Sso\CustomerReturn::mc()
 * @used-by \Dfe\AlphaCommerceHub\W\Event::providerRespL()
 * @used-by \Dfe\CurrencyFormat\O::postProcess()
 * @used-by \Dfe\Dragonpay\Signer\Request::values()
 * @used-by \Dfe\Dragonpay\Signer\Response::values()
 * @used-by \Dfe\IPay88\Signer\Request::values()
 * @used-by \Dfe\IPay88\Signer\Response::values()
 * @used-by \Dfe\Robokassa\Signer\Request::values()
 * @used-by \Dfe\Robokassa\Signer\Response::values()
 * @used-by \Dfe\SecurePay\Signer\Request::values()
 * @used-by \Dfe\TwoCheckout\Method::charge()
 * @param array(int|string => mixed) $a
 * @param string|string[]|int|null $k
 * @param mixed|callable $d
 * @return mixed|null|array(string => mixed)
 */
function dfa(array $a, $k, $d = null) {return
	// 2016-02-13
	// Нельзя здесь писать `return df_if2(isset($array[$k]), $array[$k], $d);`
	// потому что получим «Notice: Undefined index».
	// 2016-08-07
	// В \Closure мы можем безнаказанно передавать параметры,
	// даже если closure их не поддерживает https://3v4l.org/9Sf7n
	is_null($k) ? $a : (is_array($k) ? dfa_select_ordered($a, $k) : (isset($a[$k]) ? $a[$k] : (
		df_contains($k, '/') ? dfa_deep($a, $k, $d) : df_call_if($d, $k)
	)))
;}

/**
 * 2020-01-29
 * @used-by df_credentials()
 * @used-by dfe_portal_module()
 * @used-by \Df\Framework\Request::clean()
 * @used-by \Df\Framework\Request::extra()
 * @param F $f
 * @param string|string[]|null $k [optional]
 * @param mixed|callable|null $d [optional]
 * @return mixed
 */
function dfac(F $f, $k = null, $d = null) {return dfa(dfcf($f, [], [], false, 1), $k, $d);}

/**
 * 2020-01-29
 * @used-by \Df\Config\Backend::fc()
 * @used-by \Df\Payment\Block\Info::ii()
 * @used-by \Df\Payment\Method::ii()
 * @param _DO|AE $o
 * @param string|string[]|null $k [optional]
 * @param mixed|callable|null $d [optional]
 * @return _DO|AE|mixed
 */
function dfad($o, $k = null, $d = null) {return is_null($k) ? $o : dfa($o->getData(), $k, $d);}

/**
 * 2020-01-29
 * @used-by \Df\Config\A::get()
 * @used-by \Df\Framework\Form\Element\Fieldset::v()
 * @used-by \Df\Payment\TM::req()
 * @used-by \Df\Payment\TM::res0()
 * @used-by \Dfe\CheckoutCom\Response::a()
 * @param object $o
 * @param F $f
 * @param string|string[]|null $k [optional]
 * @param mixed|callable|null $d [optional]
 * @return mixed
 */
function dfaoc($o, F $f, $k = null, $d = null) {return dfa(dfc($o, $f, [], false, 1), $k, $d);}

/**
 * 2015-02-08
 * 2020-01-29
 * 1) It returns a subset of $a with $k keys in the same order as in $k.
 * 2) Normally, you should use @see dfa() instead because it is shorter and calls dfa_select_ordered() internally.
 * @used-by dfa()
 * @param array(string => string)|T $a
 * @param string[] $k
 * @return array(string => string)
 */
function dfa_select_ordered($a, array $k)  {
	$resultKeys = array_fill_keys($k, null); /** @var array(string => null) $resultKeys */
	/**
	 * 2017-10-28
	 * During the last 2.5 years, I had the following code here:
	 * 		array_merge($resultKeys, df_ita($source))
	 * It worked wronly, if $source contained SOME numeric-string keys like "99":
	 * https://github.com/mage2pro/core/issues/40#issuecomment-340139933
	 *
	 * «A key may be either an integer or a string.
	 * If a key is the standard representation of an integer, it will be interpreted as such
	 * (i.e. "8" will be interpreted as 8, while "08" will be interpreted as "08").»
	 * http://php.net/manual/en/language.types.array.php
	 *
	 * «If, however, the arrays contain numeric keys, the later value will not overwrite the original value,
	 * but will be appended.
	 * Values in the input array with numeric keys will be renumbered
	 * with incrementing keys starting from zero in the result array.»
	 * http://php.net/manual/en/function.array-merge.php
	 * https://github.com/mage2pro/core/issues/40#issuecomment-340140297
	 * `df_ita($source) + $resultKeys` does not solve the problem,
	 * because the result keys are ordered in the `$source` order, not in the `$resultKeys` order:
	 * https://github.com/mage2pro/core/issues/40#issuecomment-340140766
	 * @var array(string => string) $resultWithGarbage
	 */
	$resultWithGarbage = dfa_merge_numeric($resultKeys, df_ita($a));
	return array_intersect_key($resultWithGarbage, $resultKeys);
}