<?php
namespace Df\Config;
use Df\Core\Exception as DFE;
use Df\Framework\Form\Element\Checkbox;
class ArrayItem extends O {
	/**
	 * 2015-12-31
	 * @override
	 * @see \Df\Core\O::getId()
	 * @used-by \Df\Config\A::get()
	 * https://github.com/mage2pro/core/tree/dcc75ea95b8644548d8b2a5c5ffa71c891f97e60/Config/A.php#L26
	 * @return string
	 */
	public function getId() {df_abstract($this);}

	/**
	 * 2016-08-07
	 * @used-by \Df\Config\Backend\ArrayT::processI()
	 * @return int
	 */
	public function sortWeight() {return 0;}
}