<?php
namespace Df\Framework\Form\Element;
use Df\Framework\Form\ElementI;
use Magento\Framework\Data\Form\Element\Textarea as _Textarea;
/**
 * 2016-03-09
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * @used-by \KingPalm\B2B\Block\Registration::textarea()
 * @used-by vendor/mage2pro/ach/etc/adminhtml/system.xml
 * @used-by vendor/mage2pro/allpay/etc/adminhtml/system.xml
 * @used-by vendor/mage2pro/blackbaud-netcommunity/etc/adminhtml/system.xml
 * @used-by vendor/mage2pro/checkout.com/etc/adminhtml/system.xml
 * @used-by vendor/mage2pro/ginger-payments/etc/adminhtml/system.xml
 * @used-by vendor/mage2pro/ipay88/etc/adminhtml/system.xml
 * @used-by vendor/mage2pro/kassa-compleet/etc/adminhtml/system.xml
 * @used-by vendor/mage2pro/moip/etc/adminhtml/system.xml
 * @used-by vendor/mage2pro/tbc-bank/etc/adminhtml/system.xml
 * @used-by vendor/mage2pro/twitter-timeline/etc/adminhtml/system.xml
 * @used-by vendor/mage2pro/yandex-kassa/etc/adminhtml/system.xml
 */
class Textarea extends _Textarea implements ElementI {
	/**
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see \Magento\Framework\Data\Form\Element\Textarea::getHtmlAttributes()
	 * @used-by \Magento\Framework\Data\Form\Element\Textarea::getElementHtml()
	 * @return string[]
	 */
	function getHtmlAttributes() {return array_merge(['placeholder'], parent::getHtmlAttributes());}

	/**
	 * 2016-03-09
	 * Мы не можем делать этот метод абстрактным, потому что наш плагин
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 * работает так:
	 *		if ($subject instanceof \Df\Framework\Form\ElementI) {
	 *			$subject->onFormInitialized();
	 *		}
	 * Т.е. будет попытка вызова абстрактного метода.
	 * Также обратите внимание, что для филдсетов этот метод не является абстрактным:
	 * @see \Df\Framework\Form\Element\Fieldset::onFormInitialized()
	 * @override
	 * @see \Df\Framework\Form\ElementI::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 */
	final function onFormInitialized():void {}

	/**
	 * 2016-03-09
	 * @override
	 * @see \Magento\Framework\Data\Form\AbstractForm::_construct()
	 * https://github.com/magento/magento2/blob/487f5f45/lib/internal/Magento/Framework/Data/Form/AbstractForm.php#L64-L73
	 *
	 * Перекрывать надо именно этот метод, а не getRows(),
	 * потому что @see \Magento\Framework\Data\Form\AbstractForm::serialize()
	 * не вызывает методы-аксессоры, а напрямую работает с @see \Magento\Framework\DataObject::$_data
	 * https://github.com/magento/magento2/blob/487f5f45/lib/internal/Magento/Framework/Data/Form/AbstractForm.php#L256-L260
	 *
	 * https://github.com/mage2pro/core/tree/34f8c108/Config/etc/system_file.xsd#L102
	 * https://github.com/mage2pro/twitter-timeline/blob/604c28e/etc/adminhtml/system.xml#L50
	 */
	final protected function _construct() {
		parent::_construct();
		if ($dfRows = df_fe_fc_i($this, 'dfRows')) {  /** @var int $dfRows */
			$this['rows'] = $dfRows;
		}
	}
}