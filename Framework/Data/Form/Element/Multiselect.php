<?php
namespace Df\Framework\Data\Form\Element;
use Df\Framework\Data\Form\ElementI;
use Magento\Framework\Data\Form\Element\Multiselect as _Multiselect;
// 2016-03-08
class Multiselect extends _Multiselect implements ElementI {
	/**
	 * 2016-03-08
	 * @override
	 * @see \Df\Framework\Data\Form\ElementI::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {
		$this->addClass('df-multiselect');
		df_fe_init($this, __CLASS__, 'Df_Core::lib/Select2/main.css');
	}
}

