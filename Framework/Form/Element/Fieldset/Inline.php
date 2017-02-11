<?php
namespace Df\Framework\Form\Element\Fieldset;
use Df\Framework\Form\Element\Fieldset;
use Df\Framework\Form\Element\Renderer\Inline as InlineRenderer;
class Inline extends Fieldset {
	/**
	 * 2015-11-19
	 * @override
	 * @see \Df\Framework\Form\Element\Fieldset::getElementRendererDf()
	 * @return InlineRenderer
	 */
	function getElementRendererDf() {return InlineRenderer::s();}

	/**
	 * 2015-11-19
	 * @override
	 * @see \Df\Framework\Form\Element\Fieldset::_construct()
	 * @used-by \Magento\Framework\Data\Form\AbstractForm::__construct()
	 * @return void
	 */
	protected function _construct() {
		$this->addClass('df-fieldset-inline');
		parent::_construct();
	}
}