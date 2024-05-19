<?php
use Magento\Variable\Model\Variable as V;
use Magento\Variable\Model\ResourceModel\Variable\Collection as VC;

/**
 * 2024-01-02
 * @used-by df_mvar_n()
 * @return V|null
 */
function df_mvar(string $c) {return dfa(df_mvars(), $c);}

/**
 * 2024-01-02
 * @used-by df_mvar_n()
 * @used-by \Sharapov\Cabinetsbay\Block\Category\View::images() (https://github.com/cabinetsbay/catalog/issues/2)
 * @param string|string[] $c
 * @return string|string[]
 */
function df_mvar_n($c) {return is_array($c) ? df_map(__FUNCTION__, $c) : (!($v = df_mvar($c)) ? '' : $v->getName());}

/**
 * 2024-01-02
 * @used-by df_mvar()
 * @return array(string => V)
 */
function df_mvars():array {return dfcf(function():array {return df_index('code', df_new_om(VC::class));});}