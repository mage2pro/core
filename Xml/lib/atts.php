<?php
use SimpleXMLElement as X;

/**
 * 2024-09-23 https://www.php.net/manual/en/simplexmlelement.attributes.php
 * @used-by \Df\Xml\A::p()
 * @return array(string => string)
 */
function df_xml_atts(X $x):array {/** @var ?X  $aa */ return !($aa = $x->attributes()) ? [] : df_clean_r(
	df_map($aa, function(?X $v):?string {return !$v ? null : (string)$v;})
);}