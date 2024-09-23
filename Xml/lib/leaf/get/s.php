<?php
use SimpleXMLElement as X;

/**
 * @used-by df_leaf_sne()
 * @used-by \Dfe\Robokassa\Api\Options::p()
 * @param string|callable $d [optional]
 */
function df_leaf_s(?X $x = null, $d = ''):string {return (string)df_leaf($x, $d);}

/**
 * @used-by \Dfe\SecurePay\Refund::process()
 * @param string|callable $d [optional]
 */
function df_leaf_sne(?X $x = null, $d = ''):string {/** @var string $r */
	if (df_es($r = df_leaf_s($x, $d))) {
		df_error('Лист дерева XML должен быть непуст, однако он пуст.');
	}
	return $r;
}