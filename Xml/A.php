<?php
namespace Df\Xml;
use \SimpleXMLElement as X;
# 2024-09-23 "Refactor the `Df_Xml` module": https://github.com/mage2pro/core/issues/437
final class A {
	/**
	 * 2024-09-23
	 * @used-by df_xml2a()
	 * @used-by self::p()
	 * @param X|string $x
	 * @return string|array(string => mixed)
	 */
	static function p($x) {return df_xml_empty($x = df_xml_x($x)) ? (string)$x :
		(!($aa = df_xml_atts($x)) ? [] : ['@' => $aa])
		+ (!$x->hasChildren() ? [0 => (string)$x] : df_map(__METHOD__, $x->children()))
	;}
}