<?php
namespace Df\Framework\Form\Element;
// 2016-08-10
class Select2 extends Select {
	/**
	 * 2016-08-10
	 * @override
	 * @see \Df\Framework\Form\Element\Select::onFormInitialized()
	 * @return void
	 */
	public function onFormInitialized() {
		parent::onFormInitialized();
		$this->addClass('df-select2');
		df_fe_init($this, __CLASS__, 'Df_Core::lib/Select2/main.css', [
			/**
			 * 2016-08-10
			 * По аналогии с @see \Df\Framework\Form\Element\Fieldset::field()
			 * https://github.com/mage2pro/core/blob/1.6.3/Framework/Form/Element/Fieldset.php#L309
			 */
			'cssClass' => df_cc_s(
				'df-select2'
				,$this->customCssClass()
				, Fieldset::customCssClassByShortName(df_fe_name_short($this->getName()))
			)
			,'options' => $this->getValues()
			// 2016-08-10
			// Выбранное значение.
			,'value' => $this['value']
		]);
	}

	/**
	 * 2016-08-10
	 * @used-by \Df\Framework\Form\Element\Select2::onFormInitialized()
	 * @return string
	 */
	protected function customCssClass() {return '';}
}


