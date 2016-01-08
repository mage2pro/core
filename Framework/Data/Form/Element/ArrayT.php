<?php
namespace Df\Framework\Data\Form\Element;
/**
 * Этот класс не является одиночкой:
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/AbstractForm.php#L155
 */
class ArrayT extends Fieldset {
	/**
	 * 2015-11-19
	 * @override
	 * @see \Df\Framework\Data\Form\Element\Fieldset::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {
		parent::onFormInitialized();
		$this->addClass('df-array');
		// 2015-12-29
		// Невидимая строка-шаблон.
		df_hide($this->field('template', $this->itemType()));
		/** @var int $itemId */
		$itemId = 0;
		foreach ($this->v() as $key => $data) {
			/** @var string|int $key */
			/** @var string|array(string => mixed) $data */
			// 2015-12-30
			// http://code.dmitry-fedyuk.com/m2/all/blob/b1f6809b7723d8426636bb892b852f408bdc5650/Framework/view/adminhtml/web/formElement/array/main.js#L131
			if (\Df\Config\A::FAKE !== $key) {
				$this->field($itemId++, $this->itemType(), null, $data);
			}
		}
		df_fe_init($this, __CLASS__, DF_FA, [], 'array');
	}

	/**
	 * 2015-12-29
	 * http://code.dmitry-fedyuk.com/m2/all/blob/57607cc23405c3dcde50999d063b2a7f49499260/Config/etc/system_file.xsd#L70
	 * http://code.dmitry-fedyuk.com/m2e/currency-format/blob/2d920d0c0579a134b140eb28b9a1dc3d11467df1/etc/adminhtml/system.xml#L53
		<field
			(...)
			type='Df\Framework\Data\Form\Element\ArrayT'
			dfItemType='Dfe\CurrencyFormat\FormElement'
			(...)
		>(...)</field>
	 * @return string
	 */
	private function itemType() {return $this->fc('dfItemType');}
}