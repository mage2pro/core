<?php
namespace Df\Xml;
use \SimpleXMLElement as X;
# 2024-09-23 "Refactor the `Df_Xml` module": https://github.com/mage2pro/core/issues/437
final class A {
	/**
	 * 2024-09-23
	 * @param X|string $x
	 * @return array(string => mixed)
	 */
	static function p($x):array {
		$x = df_xml_x($x);
		$r = [];
		if ($aa = self::atts($x)) { /** @var array(string => string)  $aa */
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

	/**
	 * 2024-09-23 https://www.php.net/manual/en/simplexmlelement.attributes.php
	 * @used-by self::p()
	 * @return array(string => string)
	 */
	private static function atts(X $x):array {/** @var ?X  $aa */ return !($aa = $x->attributes()) ? [] : df_clean_null(
		df_map($aa, function(?X $v):?string {return !$v ? null : (string)$v;})
	);}
}