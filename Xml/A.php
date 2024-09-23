<?php
namespace Df\Xml;
use \SimpleXMLElement as X;
# 2024-09-23 "Refactor the `Df_Xml` module": https://github.com/mage2pro/core/issues/437
final class A {
	/**
	 * 2024-09-23
	 * @used-by df_xml2a()
	 * @param X|string $x
	 * @return string|array(string => mixed)
	 */
	static function p($x) {
		$x = df_xml_x($x);
		$r = [];
		if ($aa = df_xml_atts($x)) { /** @var array(string => string)  $aa */
			$r['@'] = $aa;
		}
		if ($x->hasChildren()) {
			foreach ($x->children() as $k => $c) {/** @var X $c */
				$r[$k] = self::p($c);
			}
		}
		elseif (!$r) {
			$r = (string)$x;
		}
		else {
			$r[0] = (string)$x;
		}
		return $r;
	}
}