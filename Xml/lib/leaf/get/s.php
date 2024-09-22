<?php
use Df\Xml\G;
use SimpleXMLElement as CX;

/**
 * @used-by df_leaf_sne()
 * @used-by \Df\Xml\G::map()
 * @used-by \Dfe\Robokassa\Api\Options::p()
 * @param string|callable $d [optional]
 */
function df_leaf_s(?CX $e = null, $d = ''):string {return (string)df_leaf($e, $d);}

/**
 * @used-by \Df\Xml\G::map()
 * @used-by \Df\Xml\G::xpathMap()
 * @used-by \Dfe\SecurePay\Refund::process()
 * @param string|callable $d [optional]
 */
function df_leaf_sne(?CX $e = null, $d = ''):string {/** @var string $r */
	if (df_es($r = df_leaf_s($e, $d))) {
		df_error('Лист дерева XML должен быть непуст, однако он пуст.');
	}
	return $r;
}