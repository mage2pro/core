<?php
namespace Df\Framework\Form\Element;
use Df\Framework\Form\ElementI;
use Magento\Framework\Data\Form\Element\Text as _Text;
class Text extends _Text implements ElementI {
	/**
	 * 2015-11-24
	 * 2015-12-12
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
}