<?php
namespace Df\Directory\FE;
/**
 * 2017-01-21   
 * @see \Df\Directory\FE\Country
 * @see \Df\Directory\FE\Currency
 */
abstract class Dropdown extends \Df\Framework\Form\Element\Select2 {
	/**
	 * 2016-09-03
	 * @override
	 * @see \Df\Framework\Form\Element\Select2::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 */
	final function onFormInitialized() {parent::onFormInitialized(); df_fe_init($this, __CLASS__);}
	
	/**
	 * 2016-09-03
	 * Этот стиль присваивается:
	 * 1) Выпадающему списку select2.
	 * 2) Оригинальному элементу select (который при использовании select2 вроде бы роли не играет).
	 * 3) Родительскому контейнеру .df-field, который присутствует в том случае,
	 * если наш элемент управления был создан внутри нашего нестандартного филдсета,
	 * и осутствует, если наш элемент управления является элементом управления вернхнего уровня
	 * (то есть, указан в атрибуте «type» тега <field>).
	 * @override
	 * @see \Df\Framework\Form\Element\Select2::customCssClass()
	 * @used-by \Df\Framework\Form\Element\Select2::setRenderer()
	 * @return string
	 */
	final protected function customCssClass() {return 'df-directory-dropdown';}

	/**
	 * 2016-11-13
	 * Поддержка фиксированного списка значений (валют, стран).
	 * Используется модулями:
	 * *) Klarna: https://github.com/mage2pro/klarna/blob/510b5b62/etc/adminhtml/system.xml?ts=4#L207
	 * *) Omise: https://github.com/mage2pro/omise/tree/0.0.6/etc/adminhtml/system.xml#L154
	 * *) Square: https://github.com/mage2pro/square/tree/1.0.13/etc/adminhtml/system.xml#L226
	 * При таком синтаксисе выпадающий список будет содержать только перечисленные значения
	 * (в список валют значения «Base Currency» и «Order Currency» в выпадающий список не включаются,
	 * если они явно не перечислены).
	 * @used-by \Df\Directory\FE\Currency::getValue()
	 * @used-by \Df\Directory\FE\Currency::getValues()
	 * @return string[]
	 */
	final protected function dfValues() {return dfc($this, function() {return
		df_fe_fc_csv($this, 'dfValues')
	;});}
}