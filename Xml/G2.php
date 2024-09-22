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
	 * 2024-09-22
	 * @used-by __construct()
	 * @var X
	 */
	private $_x;
}