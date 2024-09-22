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
	 * 2024-09-22
	 * @used-by self::__construct()
	 * @used-by self::addAttribute()
	 * @var X
	 */
	private $_x;
}