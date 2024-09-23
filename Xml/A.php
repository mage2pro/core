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
		if ($aa = df_xml_atts($x)) { /** @var array(string => string)  $aa */
			$r['@'] = $aa;
		}
		if ($x->hasChildren()) {
			foreach ($x->children() as $k => $c) {/** @var X $c */ /** @var string|array $v */
				if ($c->hasChildren($c)) {
					$v = self::p($c);
				}
				else {
					$cs = (string)$c; /** @var string $cs */
					$aa = df_xml_atts($c); /** @var array(string => string)  $aa */
					$v = !$aa ? $cs : [0 => $cs] + $aa;
				}
				$r[$k] = $v;
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
	 * 2024-09-23
	 */
	private static function isText(X $x):bool {return !df_xml_atts($x) && (!$x->hasChildren() || !count($x->children()));}
}