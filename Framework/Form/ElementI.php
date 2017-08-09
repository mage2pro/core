<?php
namespace Df\Framework\Form;
/**
 * 2015-11-24
 * @see \Df\Framework\Form\Element\Fieldset
 * @see \Df\Framework\Form\Element\Text
 * @see \Df\Framework\Form\Element\Multiselect
 * @see \Df\Framework\Form\Element\Select
 * @see \Dfe\Moip\FE\Webhooks
 */
interface ElementI {
	/**
	 * 2015-11-24
	 * Многие операции над элементом допустимы только при наличии формы,
	 * поэтому мы выполняем их в этом обработчике.
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 * @see \Df\Framework\Form\Element::onFormInitialized()
	 * @see \Df\Framework\Form\Element\Fieldset ::onFormInitialized()
	 * @see \Df\Framework\Form\Element\Text::onFormInitialized()
	 * @see \Df\Framework\Form\Element\Multiselect::onFormInitialized()
	 * @see \Df\Framework\Form\Element\Select::onFormInitialized()
	 * @see \Dfe\Moip\FE\Webhooks::onFormInitialized()
	 */
	function onFormInitialized();

	// 2015-11-24
	const AFTER = 'after';
	const BEFORE = 'before';
}


