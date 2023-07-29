<?php
use Closure as F;
use Df\Core\Exception as DFE;
use Magento\Config\Model\Config\Structure\AbstractElement as AE;
use Magento\Framework\Api\AbstractSimpleObject as oAPI;
use Magento\Framework\DataObject as _DO;

/**
 * 2020-02-04
 * @used-by df_gd()
 * @param mixed $v
 * @return _DO|AE|oAPI
 * @throws DFE
 */
function df_assert_gd($v) {return df_has_gd($v) ? $v : df_error(df_ucfirst(
	'Getting data from %s is not supported by `df_gd()`.', df_type($v)
));}

/**
 * 2020-02-04
 * @used-by dfad()
 * @used-by dfa_remove_objects()
 * @used-by \Df\Qa\Dumper::dumpObject()
 * @used-by \Df\Sentry\Extra::adjust()
 * @param mixed|_DO|AE|oAPI $v
 * @param F|bool|mixed $onE [optional]
 * @return array(string => mixed)
 */
function df_gd($v, $onE = true):array {return df_try(function() use($v) {return
	# 2023-07-28
	# "`df_gd()` / `df_has_gd()` / `df_assert_gd` should treat `Magento\Framework\Api\AbstractSimpleObject`
	# similar to `Magento\Framework\DataObject`": https://github.com/mage2pro/core/issues/290
	df_is_api_o(df_assert_gd($v)) ? $v->__toArray() : $v->getData()
;}, $onE);}

/**
 * 2020-02-04
 * 2023-07-28
 * "`df_gd()` / `df_has_gd()` / `df_assert_gd` should treat `Magento\Framework\Api\AbstractSimpleObject`
 * similar to `Magento\Framework\DataObject`": https://github.com/mage2pro/core/issues/290
 * @used-by df_assert_gd()
 * @used-by df_call()
 * @used-by \Df\Sentry\Extra::adjust()
 * @param mixed $v
 */
function df_has_gd($v):bool {return $v instanceof _DO || $v instanceof AE || df_is_api_o($v);}