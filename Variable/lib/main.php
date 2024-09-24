<?php
use Magento\Variable\Model\Variable as V;
use Magento\Variable\Model\ResourceModel\Variable\Collection as VC;

/**
 * 2024-01-02
 * @used-by df_mvar_name()
 * @return V|null
 */
function df_mvar(string $c) {return dfa(df_mvars(), $c);}

/**
 * 2024-01-02
 * 2024-06-10 If $c is an array, then the `df_mvar_name()` result uses $c as keys.
 * @used-by df_mvar_n()
 * @used-by CabinetsBay\Catalog\B\Category::images() (https://github.com/cabinetsbay/catalog/issues/2)
 * @param string|string[] ...$c
 * @return string|string[]
 */
function df_mvar_name(...$c) {
	$r = df_call_a($c, function(string $c):string {return !($v = df_mvar($c)) ? '' : $v->getName();});
	return !is_array($r) ? $r : array_combine(df_args($c), $r);
}

/**
 * 2024-01-02
 * @used-by df_mvar()
 * @return array(string => V)
 */
function df_mvars():array {return dfcf(function():array {return df_index('code', df_new_om(VC::class));});}