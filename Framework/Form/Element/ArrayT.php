<?php
namespace Df\Framework\Form\Element;
/**
 * 2015-11-19
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * This class is not a singleton:
 * @see \Magento\Framework\Data\Form\AbstractForm::addField():
 * 		$element = $this->_factoryElement->create($type, ['data' => $config]);
 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/lib/internal/Magento/Framework/Data/Form/AbstractForm.php#L137-L159
 */
class ArrayT extends Fieldset {
	/**
	 * 2015-11-19
	 * @override
	 * @see \Df\Framework\Form\Element\Fieldset::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 */
	final function onFormInitialized() {
		parent::onFormInitialized();
		$this->addClass('df-array');
		// 2015-12-29 It is an invisible template row.
		df_hide($this->field('template', $this->itemFormElement()));
		$itemId = 0; /** @var int $itemId */
		foreach ($this->v() as $key => $data) {
			/** @var string|int $key */ /** @var string|array(string => mixed) $data */
			/**
			 * 2016-07-30
			 * Раньше тут стоял код:
			 *	// 2015-12-30
			 *	// https://github.com/mage2pro/core/tree/b1f6809b7723d8426636bb892b852f408bdc5650/Framework/view/adminhtml/web/formElement/array/main.js#L131
			 *	if (\Df\Config\A::FAKE !== $key) {
			 *		$this->field($itemId++, $this->itemType(), null, $data);
			 *	}
			 * Теперь у нас ключ @see \Df\Config\A::FAKE удаляется в методе
			 * @see \Df\Config\Backend\ArrayT::processA()
			 * поэтому здесь его уже быть не должно.
			 */
			df_assert_ne(\Df\Config\A::FAKE, $key);
			$this->field($itemId++, $this->itemFormElement(), null, $data);
		}
		df_fe_init($this, __CLASS__, df_fa(), [], 'array');
	}

	/**
	 * 2015-12-29
	 * https://github.com/mage2pro/core/tree/57607cc23405c3dcde50999d063b2a7f49499260/Config/etc/system_file.xsd#L70
	 * https://github.com/mage2pro/currency-format/blob/2d920d0c/etc/adminhtml/system.xml#L53
	 *	<field
	 *		(...)
	 *		type='Df\Framework\Form\Element\ArrayT'
	 *		dfItemFormElement='Dfe\CurrencyFormat\FE'
	 *		(...)
	 *	>(...)</field>
	 * @return string
	 */
	private function itemFormElement() {return $this->fc('dfItemFormElement');}
}