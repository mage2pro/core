<?php
use Df\Core\Exception as DFE;
use Throwable as Th; # 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2016-11-10
 * @used-by df_con_heir()
 * @used-by df_con_hier()
 * @used-by df_eav_update()
 * @used-by df_load()
 * @used-by df_newa()
 * @used-by df_trans()
 * @used-by dfpex_args()
 * @used-by \Df\Payment\Choice::f()
 * @used-by \Df\Payment\Operation\Source\Creditmemo::cm()
 * @used-by \Df\Payment\Operation\Source\Order::ii()
 * @used-by \Df\Payment\Operation\Source\Quote::ii()
 * @used-by \Df\Payment\W\Strategy::handle()
 * @used-by \Df\Payment\W\Strategy::m()
 * @used-by \Df\Payment\W\Strategy\Refund::_handle()
 * @param string|object $v
 * @param string|object|null $c [optional]
 * @param string|Th|null $m [optional]
 * @return string|object
 * @throws DFE
 */
function df_ar($v, $c = null, $m = null) {return dfcf(function($v, $c = null, $m = null) {
	if ($c) {
		$c = df_cts($c);
		!is_null($v) ?: df_error($m ?: "Expected class: «{$c}», given `null`.");
		is_object($v) || is_string($v) ?: df_error($m ?: "Expected class: «{$c}», given: %s.", df_type($v));
		$cv = df_assert_class_exists(df_cts($v)); /** @var string $cv */
		if (!is_a($cv, $c, true)) {
			df_error($m ?: "Expected class: «{$c}», given class: «{$cv}».");
		}
	}
	return $v;
}, func_get_args());}

/**
 * 2016-08-03
 * @used-by df_ar()
 * @used-by \Df\Config\Backend\Serialized::entityC()
 * @param string|Th $m [optional]
 * @throws DFE
 */
function df_assert_class_exists(string $c, $m = null):string {
	df_param_sne($c, 0);
	return df_class_exists($c) ? $c : df_error($m ?: "The required class «{$c}» does not exist.");
}