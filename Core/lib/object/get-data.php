<?php
use Closure as F;
use Df\Core\Exception as DFE;
use Magento\Config\Model\Config\Structure\AbstractElement as AE;
use Magento\Framework\DataObject as _DO;

/**
 * 2020-02-04
 * @used-by df_gd()
 * @param mixed $v
 * @return _DO|AE
 * @throws DFE
 */
function df_assert_gd($v) {return df_has_gd($v) ? $v : df_error(df_ucfirst(
	'%s does not support a proper getData().', df_type($v)
));}

/**
 * 2020-02-04
 * @used-by dfad()
 * @used-by dfa_remove_objects()
 * @param mixed|_DO|AE $v
 * @param F|bool|mixed $onE [optional]
 * @return array(string => mixed)
 */
function df_gd($v, $onE = true):array {return df_try(function() use($v) {return df_assert_gd($v)->getData();}, $onE);}

/**
 * 2020-02-04
 * @used-by df_assert_gd()
 * @used-by df_call()
 * @used-by \Df\Qa\Dumper::dumpObject()
 * @param mixed $v
 */
function df_has_gd($v):bool {return $v instanceof _DO || $v instanceof AE;}