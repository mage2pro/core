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

	/**
	 * 2015-11-23
	 * Я же хочу иметь возможность изначально скрывать вложенный fieldset
	 * и раскрывать его лишь по желанию администратора.
	 * Сначала я делал это через JavaScript:
	 * т.е. сначала fieldset видим, потом загружается JavaScript и скрывает его.
	 * Это выглядит не очень эстетично: fielset мелькает.
	 * Поэтому я добавляю здесь возможность изначально скрывать fieldset.
	 * Это можно было бы сделать добавлением соответствующего класса css.
	 */
	const CONTAINER_CLASS = 'container_class';

	/**
	 * 2015-11-24
	 */
	const LABEL_POSITION = 'label_position';
}


