<?php
namespace Df\Framework\Data\Form\Element;
use Df\Framework\Data\Form\ElementI;
use Magento\Framework\Data\Form\Element\Text as _Text;
class Text extends _Text implements ElementI {
	/**
	 * 2015-11-24
	 * @override
	 * @see \Df\Framework\Data\Form\ElementI::onFormInitialized()
	 * @used-by \Df\Framework\Data\Form\Element\AbstractElementPlugin::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {}
}