<?php
use Df\Xml\X;
use SimpleXMLElement as CX;

/**
 * 2022-11-15 @deprecated It is unused.
 * @param string|mixed|null|callable $d [optional]
 * @return string|mixed|null
 */
function df_leaf_child(CX $e, string $child, $d = null) {return df_leaf($e->{$child}, $d);}

/**
 * @used-by df_leaf_sne()
 * @used-by \Df\Xml\X::map()
 * @used-by \Df\Xml\X::xpathMap()
 * @used-by \Dfe\Robokassa\Api\Options::p()
 * @param CX|null $e [optional]
 * @param string|callable $d [optional]
 */
function df_leaf_s(CX $e = null, $d = ''):string {return (string)df_leaf($e, $d);}

/**
 * @used-by \Df\Xml\X::map()
 * @used-by \Df\Xml\X::xpathMap()
 * @used-by \Dfe\SecurePay\Refund::process()
 * @param string|callable $d [optional]
 */
function df_leaf_sne(CX $e = null, $d = ''):string {/** @var string $r */
	if (df_es($r = df_leaf_s($e, $d))) {
		df_error('Лист дерева XML должен быть непуст, однако он пуст.');
	}
	return $r;
}