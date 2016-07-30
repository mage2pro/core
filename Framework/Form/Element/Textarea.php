<?php
namespace Df\Framework\Form\Element;
use Df\Framework\Form\ElementI;
use Magento\Framework\Data\Form\Element\Textarea as _Textarea;
class Textarea extends _Textarea implements ElementI {
	/**
	 * 2016-03-09
	 * Мы не можем делать этот метод абстрактным, потому что наш плагин
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 * работает так:
			if ($subject instanceof \Df\Framework\Form\ElementI) {
				$subject->onFormInitialized();
			}
	 * Т.е. будет попытка вызова абстрактного метода.
	 * Также обратите внимание, что для филдсетов этот метод не является абстрактным:
	 * @see \Df\Framework\Form\Element\Fieldset::onFormInitialized()
	 * @override
	 * @see \Df\Framework\Form\ElementI::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {}

	/**
	 * 2016-03-09
	 * @override
	 * @see \Magento\Framework\Data\Form\AbstractForm::_construct()
	 * https://github.com/magento/magento2/blob/487f5f45/lib/internal/Magento/Framework/Data/Form/AbstractForm.php#L64-L73
	 * @return void
	 *
	 * Перекрывать надо именно этот метод, а не getRows(),
	 * потому что @see \Magento\Framework\Data\Form\AbstractForm::serialize()
	 * не вызывает методы-аксессоры, а напрямую работает с @see \Magento\Framework\DataObject::$_data
	 * https://github.com/magento/magento2/blob/487f5f45/lib/internal/Magento/Framework/Data/Form/AbstractForm.php#L256-L260
	 *
	 * https://github.com/mage2pro/core/tree/34f8c108/Config/etc/system_file.xsd#L102
	 * https://code.dmitry-fedyuk.com/m2e/twitter-timeline/blob/604c28e/etc/adminhtml/system.xml#L50
	 */
	protected function _construct() {
		parent::_construct();
		/** @var int $dfRows */
		$dfRows = df_fe_fc_i($this, 'dfRows');
		if ($dfRows) {
			$this['rows'] = $dfRows;
		}
	}
}

