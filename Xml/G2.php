<?php
namespace Df\Xml;
use \SimpleXMLElement as X;
# 2024-09-22
# 1) "Refactor `Df\Xml\X`": https://github.com/mage2pro/core/issues/436
# 2) "Refactor the `Df_Xml` module": https://github.com/mage2pro/core/issues/437
final class G2 {
	/**
	 * 2024-09-22
	 * @param X|string $x
	 */
	function __construct($x) {$this->_x = df_xml_x($x);}

	/**
	 * 2021-12-13
	 * @used-by self::addAttributes()
	 * @used-by self::addChildX()
	 */
	function addAttribute(string $k, string $v = '', string $ns = ''):void {$this->_x->addAttribute($this->k($k), $v, $ns);}

	/**
	 * @used-by df_xml_node()
	 * @used-by self::importArray()
	 * @param array(string => string) $atts
	 */
	function addAttributes(array $aa):void {
		foreach ($aa as $k => $v) {/** @var string $k */ /** @var mixed $v */
			df_assert(
				!is_object($v) && !is_array($v)
				, sprintf("The attribute «{$k}» has a value of a forbidden type %s.", df_type($v))
				, ['attributes' => $aa]
			);
			$this->addAttribute(df_assert_sne($k), $v);
		}
	}

	/**
	 * 2021-12-16
	 * https://stackoverflow.com/a/9391673
	 * https://stackoverflow.com/a/43566078
	 * https://stackoverflow.com/a/6928183
	 * @used-by self::addAttribute()
	 * @used-by self::addChild()
	 */
	private function k(string $s):string {return !df_contains($s, ':') ? $s : "xmlns:$s";}

	/**
	 * 2024-09-22
	 * @used-by self::__construct()
	 * @used-by self::addAttribute()
	 * @var X
	 */
	private $_x;
}