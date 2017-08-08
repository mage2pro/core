<?php
namespace Df\Framework\Form\Element;
use Df\Framework\Form\ElementI;
use Magento\Framework\Data\Form\Element\Hidden as _Hidden;
/** 
 * 2015-11-28
 * @see \Df\Framework\Form\Element\Table
 * @see \Dfe\SalesSequence\Config\Matrix\Element
 */
class Hidden extends _Hidden implements ElementI {
	/**
	 * 2015-11-28
	 * 2015-12-12
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
	 * @see \Df\Framework\Form\Element\Table::onFormInitialized()
	 * @see \Dfe\SalesSequence\Config\Matrix\Element::onFormInitialized()
	 */
	function onFormInitialized() {}
}