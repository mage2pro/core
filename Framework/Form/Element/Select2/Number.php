<?php
namespace Df\Framework\Form\Element\Select2;
use Df\Framework\Form\Element\Select2;
// 2016-08-10
class Number extends Select2 {
	/**
	 * 2016-08-10
	 * @override
	 * @see \Df\Framework\Form\Element\Select2::onFormInitialized()
	 * @return void
	 */
	public function onFormInitialized() {
		parent::onFormInitialized();
		$this->addClass('df-select2-number');
		df_fe_init($this, __CLASS__, [], [], 'select2/number');
	}

	/**
	 * 2016-08-10
	 * @override
	 * @see \Df\Framework\Form\Element\Select2::customCssClass()
	 * @used-by \Df\Framework\Form\Element\Select2::onFormInitialized()
	 * @return string
	 */
	protected function customCssClass() {return 'df-select2-number';}

	/**
	 * 2016-08-12
	 * @override
	 * @see \Df\Framework\Form\Element\Select2::width()
	 * @used-by \Df\Framework\Form\Element\Select2::onFormInitialized()
	 * @return string
	 */
	protected function width() {return '5em';}
}


