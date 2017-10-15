<?php
namespace Df\Framework\Form\Element;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface as IRenderer;
/**        
 * 2016-08-10
 * @see \Df\Directory\FE\Dropdown
 * @see \Df\Framework\Form\Element\Select2\Number
 */
class Select2 extends Select {
	/**
	 * 2016-09-03
	 * @override
	 * Неправильно вызывать @uses df_fe_init() в методе
	 * @see \Df\Framework\Form\Element\Select2::onFormInitialized(),
	 * потому что onFormInitialized() вызывается на
	 * @see \Df\Framework\Form\Element\Select2::setForm()
	 * плагином @see \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetElementHtml()
	 * https://github.com/mage2pro/core/blob/1.7.33/Framework/Plugin/Data/Form/Element/AbstractElement.php?ts=4#L77-L83
	 * и туда мы попадаем из метода @see \Magento\Config\Block\System\Config\Form::_initElement()
	 * в точке https://github.com/magento/magento2/blob/2.1.1/app/code/Magento/Config/Block/System/Config/Form.php#L347-L367
	 * перед инициализацией опций выпадающего списка,
	 * которая происходит в том же методе позже, в точке
	 * https://github.com/magento/magento2/blob/2.1.1/app/code/Magento/Config/Block/System/Config/Form.php#L376-L378
	 *
	 * Поэтому вызываем @uses df_fe_init() в методе @see \Df\Framework\Form\Element\Select2::setRenderer(),
	 * который вызывается из метода @see \Magento\Config\Block\System\Config\Form::_initElement()
	 * уже после инициализации опций выпадающего списка, в точке
	 * https://github.com/magento/magento2/blob/2.1.1/app/code/Magento/Config/Block/System/Config/Form.php#L379
	 *
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::setRenderer()
	 * @used-by \Magento\Config\Block\System\Config\Form::_initElement()
	 * https://github.com/magento/magento2/blob/2.1.1/app/code/Magento/Config/Block/System/Config/Form.php#L379
	 * @param IRenderer $renderer
	 * @return $this
	 */
	function setRenderer(IRenderer $renderer) {
		/**
		 * 2016-09-03
		 * В первый раз мы попадаем сюда отсюда:
		 * @see \Magento\Framework\Data\Form\Element\Fieldset::addField()
		 * https://github.com/magento/magento2/blob/2.1.1/lib/internal/Magento/Framework/Data/Form/Element/Fieldset.php#L213
		 * В этот момент опции выпадающего списка ещё не инициализированы,
		 * поэтому дожидаемся их инициализации.
		 */
		if (!isset($this->{__METHOD__}) && $this->getValues()) {
			/**
			 * 2016-09-03
			 * Этот класс присваивается оригинальному элементу select
			 * (который при использовании select2 вроде бы роли не играет),
			 * и родительскому контейнеру .df-field, который присутствует в том случае,
			 * если наш элемент управления был создан внутри нашего нестандартного филдсета,
			 * и осутствует, если наш элемент управления является элементом управления вернхнего уровня
			 * (то есть, указан в атрибуте «type» тега <field>).
			 */
			$this->addClass(df_cc_s('df-select2', $this->customCssClass()));
			df_fe_init($this, __CLASS__, df_asset_third_party('Select2/main.css'), [
				/**
				 * 2016-08-10
				 * Этот стиль присваивается выпадающему списку select2.
				 * By analogy with @see \Df\Framework\Form\Element\Fieldset::field()
				 * https://github.com/mage2pro/core/blob/1.6.3/Framework/Form/Element/Fieldset.php#L309
				 */
				'cssClass' => df_cc_s(
					'df-select2'
					,$this->customCssClass()
					,Fieldset::customCssClassByShortName(df_fe_name_short($this->getName()))
				)
				,'disabled' => $this->disabled()
				,'options' => $this->getValues()
				,'value' => $this->getValue() // 2016-08-10 Выбранное значение.
				,'width' => $this->width()
			]);
			$this->{__METHOD__} = true;
		}
		return parent::setRenderer($renderer);
	}

	/**
	 * 2016-08-10
	 * 2016-09-03
	 * Этот стиль присваивается:
	 * 1) Выпадающему списку select2.
	 * 2) Оригинальному элементу select (который при использовании select2 вроде бы роли не играет).
	 * 3) Родительскому контейнеру .df-field, который присутствует в том случае,
	 * если наш элемент управления был создан внутри нашего нестандартного филдсета,
	 * и осутствует, если наш элемент управления является элементом управления вернхнего уровня
	 * (то есть, указан в атрибуте «type» тега <field>).
	 * @used-by \Df\Framework\Form\Element\Select2::onFormInitialized()
	 * @see \Df\Framework\Form\Element\Select2\Number::customCssClass()
	 * @return string
	 */
	protected function customCssClass() {return '';}

	/**
	 * 2017-10-15
	 * @used-by setRenderer()
	 * @see \Dfe\Stripe\FE\Currency::disabled()
	 * @return bool
	 */
	protected function disabled() {return false;}

	/**
	 * 2016-08-12
	 * @used-by \Df\Framework\Form\Element\Select2::onFormInitialized()
	 * @see \Df\Framework\Form\Element\Select2\Number::width()
	 * @return string
	 */
	protected function width() {return null;}
}