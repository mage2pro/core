<?php
namespace Df\Framework\Form\Element\Fieldset;
use Df\Framework\Form\Element\Fieldset;
use Df\Framework\Form\Element\Renderer\Inline as InlineRenderer;
/**
 * 2015-11-19
 * @see \Df\Framework\Form\Element\Quantity
 */
class Inline extends Fieldset {
	/**
	 * 2015-11-19
	 * @override
	 * @see \Df\Framework\Form\Element\Fieldset::_construct()
	 * @used-by \Magento\Framework\Data\Form\AbstractForm::__construct()
	 */
	final protected function _construct():void {$this->addClass('df-fieldset-inline'); parent::_construct();}

	/**
	 * 2015-11-19
	 * @override
	 * @see \Df\Framework\Form\Element\Fieldset::elementRendererDf()
	 * @used-by \Df\Framework\Form\Element\Fieldset::addField()
	 */
	final protected function elementRendererDf():InlineRenderer {return InlineRenderer::s();}
}