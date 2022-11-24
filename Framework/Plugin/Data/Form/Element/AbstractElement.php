<?php
namespace Df\Framework\Plugin\Data\Form\Element;
use Df\Framework\Form\Element as E;
use Df\Framework\Form\ElementI;
use Magento\Framework\Data\Form\Element\AbstractElement as Sb;
use Magento\Framework\Data\Form\Element\Multiline;
use Magento\Framework\Phrase;
# 2015-12-13
# Хитрая идея, которая уже давно пришла мне в голову: наследуясь от модифицируемого класса,
# мы получаем возможность вызывать методы с областью доступа protected у переменной $s.
class AbstractElement extends Sb {
	/**
	 * 2016-01-01
	 * An empty constructor allows us to skip the parent's one.
	 * Magento (at least at 2016-01-01) is unable to properly inject arguments into a plugin's constructor,
	 * and it leads to the error like: «Missing required argument $amount of Magento\Framework\Pricing\Amount\Base».
	 */
	function __construct() {}

	/**
	 * 2016-11-20
	 * У класса @see \Magento\Framework\Data\Form\Element\AbstractElement метод getComment() — магический.
	 * К магическим методам плагины не применяются.
	 * Поэтому для задействования плагина необходимо унаследоваться от класса ядра
	 * и явно объявить в своём классе метод getComment().
	 * Примеры:
	 * @see \Df\Framework\Form\Element\Checkbox::getComment()
	 * @see \Df\Framework\Form\Element\Text::getComment()
	 * @used-by \Magento\Config\Block\System\Config\Form\Field::_renderValue()
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Config/Block/System/Config/Form/Field.php#L82-L84
	 */
	function afterGetComment(Sb $sb, string $r):string {
		if ($vc = df_fe_fc($sb, 'dfValidator')) { /** @var string|null $vc */
			$v = df_o($vc); /** @var \Df\Framework\IValidator $v */
			if (true !== ($messages = $v->check($sb))) { /** @var string|string[]|true $messages */
				$r .= df_tag_list(df_array($messages), false, 'df-enabler-warnings');
			}	
		}
		return $r;
	}	
	
	/**
	 * 2016-03-08
	 * 1) Many built-in classes do not call getBeforeElementHtml():
	 * *) @see \Magento\Framework\Data\Form\Element\Textarea::getElementHtml()
	 * https://mage2.pro/t/150
	 * *) @see \Magento\Framework\Data\Form\Element\Fieldset::getElementHtml()
	 * https://mage2.pro/t/248
	 * *) @see \Magento\Framework\Data\Form\Element\Multiselect::getElementHtml()
	 * https://mage2.pro/t/902
	 * I need getBeforeElementHtml() for @see df_fe_init()
	 * 2) @see \Magento\Framework\Data\Form\Element\AbstractElement::getElementHtml()
	 * places before_element_html into a <label>:
	 * https://github.com/magento/magento2/blob/487f5f45/lib/internal/Magento/Framework/Data/Form/Element/AbstractElement.php#L350-L353
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getElementHtml()
	 */
	function afterGetElementHtml(Sb $sb, string $r):string {return
		df_starts_with($r, '<label class="addbefore"') ? $r : df_prepend($r, $sb->getBeforeElementHtml())
	;}

	/**
	 * 2015-10-09
	 * Цель метода — отключение автозаполнения полей.
	 * https://developers.google.com/web/fundamentals/input/form/label-and-name-inputs?hl=en#recommended-input-name-and-autocomplete-attribute-values
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getHtmlAttributes()
	 * @param string[] $r
	 * @return string[]
	 */
	function afterGetHtmlAttributes(Sb $sb, array $r):array {return array_merge($r, ['autocomplete']);}

	/**
	 * 2015-11-24
	 * Many operations on the element require the form's existance, so we do them in
	 * @see \Df\Framework\Form\ElementI::onFormInitialized()
	 * 2016-03-08
	 * «@see \Magento\Framework\Data\Form\Element\AbstractElement::setForm() is called 3 times for the same element and form.»
	 * https://mage2.pro/t/901
	 * That is why we use @uses dfc()
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::setForm()
	 */
	function afterSetForm(Sb $sb, Sb $r):Sb {
		if ($sb instanceof ElementI) {
			dfc($sb, function() use($sb) {$sb->onFormInitialized();});
		}
		return $r;
	}

	/**
	 * 2015-12-13
	 * Отличия от модифицируемого метода
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getLabelHtml():
	 * 1) Добавляем свои классы для Font Awesome.
	 * 2) При использовании Font Awesome не добавляем исходную подпись
	 * (значением которой является класс Font Awesome)
	 * и выводим, по сути, пустые теги <label><span></span></label>.
	 * 3) Добавляем атрибут title.
	 * Пример использования Font Awesome: https://github.com/mage2pro/core/tree/7cb37ab2c4d728bc20d29ca3c7c643e551f6eb0a/Framework/Data/Form/Element/Font.php#L40
	 * 2015-12-28 4) Добавляем класс, соответствующий типу элемента.
	 * @see \Df\Framework\Form\Element\Font::onFormInitialized()
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getLabelHtml()
	 */
	function aroundGetLabelHtml(Sb $sb, \Closure $f, string $idSuffix = ''):string {/** @var string $r */
		/** @var string|null|Phrase $l */
		if (is_null($l = $sb->getLabel())) {
			$r = '';
		}
		else {
			$l = (string)$l;
			/**
			 * 2015-12-25
			 * @see \Magento\Framework\Data\Form\Element\Multiline::getLabelHtml()
			 * имеет другое значение по-умолчанию параметра $idSuffix:
			 * function getLabelHtml($suffix = 0)
			 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/Multiline.php#L59
			 */
			if ('' === $idSuffix && $sb instanceof Multiline) {
				$idSuffix = 0;
			}
			$classA = ['label', "admin__field-label df-element-{$sb->getType()}"]; /** @var string[] $classA */
			if (df_starts_with($l, 'fa-')) {
				$classA = array_merge($classA, ['fa', $l]);
				$l = '';
			}
			$params = [
				'class' => df_cc_s($classA), 'for' => $sb->getHtmlId() . $idSuffix, 'data-ui-id' => E::uidSt($sb, 'label')
			]; /** @var array(string => string) $params */
			$title = (string)$sb->getTitle(); /** @var string $title */
			if ($title !== $l) {
				$params['title'] = $title;
			}
			$r = df_tag('label', $params, df_tag('span', [], $l)) . "\n";
		}
		return $r;
	}

	/**
	 * 2015-10-09
	 * Цель метода — отключение автозаполнения полей.
	 * https://developers.google.com/web/fundamentals/input/form/label-and-name-inputs?hl=en#recommended-input-name-and-autocomplete-attribute-values
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getElementHtml()
	 * @param Sb $sb
	 */
	function beforeGetElementHtml(Sb $sb):void {$sb['autocomplete'] = 'new-password';}
}