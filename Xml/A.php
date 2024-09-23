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
		# 2024-09-23 https://www.php.net/manual/en/simplexmlelement.attributes.php
		/** @var ?X  $aa */
		if ($aa = $x->attributes()) {
			foreach ($aa as $k => $v) {/** @var string $k */ /** @var mixed $v */
				if ($v) {
					$r['@'][$k] = (string)$v;
				}
			}
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
	 * 2024-09-23
	 * @used-by self::p()
	 */
	private static function atts(X $x):array {
		$r = []; /** @var array(string => string) $r */
		# 2024-09-23 https://www.php.net/manual/en/simplexmlelement.attributes.php
		/** @var ?X  $aa */
		if ($aa = $x->attributes()) {
			foreach ($aa as $k => $v) {/** @var string $k */
				/**
				 * 2024-09-23
				 * @var ?X $v
				 * @see \SimpleXMLElement::current()
				 * https://www.php.net/manual/en/simplexmlelement.current.php
				 */
				if ($v) {
					$r[$k] = (string)$v;
				}
			}
		}
		return $r;
	}
}