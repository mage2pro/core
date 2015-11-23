<?php
namespace Df\Framework\Data\Form\Element;
use Magento\Framework\Data\Form\Element\Text;
class Color extends Text {
	/**
	 * 2015-11-23
	 * @override
	 * @see \Magento\Framework\Data\Form\Element\Text::_construct()
	 * @used-by \Magento\Framework\Data\Form\AbstractForm::__construct()
	 * @return void
	 */
	protected function _construct() {
		$this->addClass('df-color');
		parent::_construct();
	}
}