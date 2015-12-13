<?php
namespace Df\Framework\Data\Form;
interface ElementI {
	/**
	 * 2015-11-24
	 * Многие операции над элементом допустимы только при наличии формы,
	 * поэтому мы выполняем их в этом обработчике.
	 * @used-by \Df\Framework\Data\Form\Element\AbstractElementPlugin::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized();

	/**
	 * 2015-11-24
	 */
	const AFTER = 'after';
	const BEFORE = 'before';
}


