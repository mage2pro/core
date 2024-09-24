<?php
use SimpleXMLElement as X;

/**
 * @used-by \Dfe\Robokassa\Api\Options::p()
 * @param string|callable $d [optional]
 */
function df_leaf_s(?X $x = null, $d = ''):string {return (string)df_leaf($x, $d);}
