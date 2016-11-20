<?php
namespace Df\Framework\Plugin\Data\Form\Element;
use Df\Framework\Form\Element as E;
use Df\Framework\Form\ElementI;
use Magento\Framework\Data\Form\Element\AbstractElement as Sb;
use Magento\Framework\Data\Form\Element\Multiline;
use Magento\Framework\Phrase;
// 2015-12-13
// Хитрая идея, которая уже давно пришла мне в голову: наследуясь от модифицируемого класса,
// мы получаем возможность вызывать методы с областью доступа protected у переменной $s.
class AbstractElement extends Sb {
	/**
	 * 2016-01-01
	 * Потрясающая техника, которую я изобрёл только что.
	 */
	public function __construct() {}

	/**
	 * 2016-11-20
	 * У класса @see \Magento\Framework\Data\Form\Element\AbstractElement
	 * метод getComment() — магический.
	 * К магическим методам плагины не применяются.
	 * Поэтому для задействования плагина необходимо унаследоваться от класса ядра
	 * и явно объявить в своём классе метод getComment().
	 * Примеры:
	 * @see \Df\Framework\Form\Element\Checkbox::getComment()
	 * @used-by \Magento\Config\Block\System\Config\Form\Field::_renderValue()
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Config/Block/System/Config/Form/Field.php#L82-L84
	 * @param Sb $sb
	 * @param string $result
	 * @return string
	 */
	public function afterGetComment(Sb $sb, $result) {
		/** @var string|null $vc */
		$vc = df_fe_fc($sb, 'dfValidator');
		if ($vc) {
			/** @var \Df\Framework\Validator $v */
			$v = df_o($vc);
			/** @var string[]|true $messages */
			$messages = $v->check();
			if (is_array($messages)) {
				$result .= df_tag_list($messages, false, 'df-enabler-warnings');
			}	
		}
		return $result;
	}	
	
	/**
	 * 2016-03-08
	 * Многие стандартные классы не вызывают getBeforeElementHtml():
	 * *) @see \Magento\Framework\Data\Form\Element\Textarea::getElementHtml()
	 * https://mage2.pro/t/150
	 * *) @see \Magento\Framework\Data\Form\Element\Fieldset::getElementHtml()
	 * https://mage2.pro/t/248
	 * *) @see \Magento\Framework\Data\Form\Element\Multiselect::getElementHtml()
	 * https://mage2.pro/t/902
	 * А нам этот вызов нужен, в частности, для @see df_fe_init()
	 *
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getElementHtml()
	 * @param Sb $sb
	 * @param string $result
	 * @return string
	 */
	public function afterGetElementHtml(Sb $sb, $result) {
		/**
		 * 2016-03-08
		 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getElementHtml()
		 * запихивает before_element_html в label:
		 * https://github.com/magento/magento2/blob/487f5f45/lib/internal/Magento/Framework/Data/Form/Element/AbstractElement.php#L350-L353
		 */
		return
			df_starts_with($result, '<label class="addbefore"')
			? $result
			: df_prepend($result, $sb->getBeforeElementHtml())
		;
	}

	/**
	 * 2015-10-09
	 * Цель метода — отключение автозаполнения полей.
	 * https://developers.google.com/web/fundamentals/input/form/label-and-name-inputs?hl=en#recommended-input-name-and-autocomplete-attribute-values
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getHtmlAttributes()
	 * @param Sb $sb
	 * @param string[] $result
	 * @return string[]
	 */
	public function afterGetHtmlAttributes(Sb $sb, $result) {
		return array_merge($result, ['autocomplete']);
	}

	/**
	 * 2015-11-24
	 * Многие операции над элементом допустимы только при наличии формы,
	 * поэтому мы выполняем их в обработчике @see \Df\Framework\Form\Element::onFormInitialized
	 *
	 * 2016-03-08
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::setForm()
	 * is called 3 times for the same element and form.
	 * https://mage2.pro/t/901
	 * Поэтому используем флаг $sb->{__METHOD__}.
	 *
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::setForm()
	 * @param Sb $sb
	 * @param Sb $result
	 * @return Sb
	 */
	public function afterSetForm(Sb $sb, Sb $result) {
		if (!isset($sb->{__METHOD__}) && $sb instanceof ElementI) {
			$sb->onFormInitialized();
			$sb->{__METHOD__} = true;
		}
		return $result;
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
	 * 2015-12-28
	 * 4) Добавляем класс, соответствующий типу элемента.
	 *
	 * Пример использования Font Awesome: https://github.com/mage2pro/core/tree/7cb37ab2c4d728bc20d29ca3c7c643e551f6eb0a/Framework/Data/Form/Element/Font.php#L40
	 *
	 * @see \Df\Framework\Form\Element\Font::onFormInitialized()
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getLabelHtml()
	 * @param Sb|E $sb
	 * @param \Closure $proceed
	 * @param string|null $idSuffix
	 * @return string
	 */
	public function aroundGetLabelHtml(Sb $sb, \Closure $proceed, $idSuffix = '') {
		/** @var string|null|Phrase $label */
		$label = $sb->getLabel();
		/** @var string $result */
		if (is_null($label)) {
			$result = '';
		}
		else {
			$label = (string)$label;
			/**
			 * 2015-12-25
			 * @see \Magento\Framework\Data\Form\Element\Multiline::getLabelHtml()
			 * имеет другое значение по-умолчанию параметра $idSuffix:
			 * public function getLabelHtml($suffix = 0)
			 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/Multiline.php#L59
			 */
			if ('' === $idSuffix && $sb instanceof Multiline) {
				$idSuffix = 0;
			}
			/** @var bool $isFontAwesome */
			$isFontAwesome = df_starts_with($label, 'fa-');
			/** @var string[] $classA */
			$classA = ['label', 'admin__field-label', 'df-element-' . $sb->getType()];
			if ($isFontAwesome) {
				$classA[]= 'fa';
				$classA[]= $label;
				$label = '';
			}
			/** @var array(string => string) $params */
			$params = [
				'class' => df_cc_s($classA)
				,'for' => $sb->getHtmlId() . $idSuffix
				,'data-ui-id' => E::uidSt($sb, 'label')
			];
			/** @var string $title */
			$title = (string)$sb->getTitle();
			if ($title !== $label) {
				$params['title'] = $title;
			}
			$result = df_tag('label', $params, df_tag('span', [], $label)) . "\n";
		}
		return $result;
	}

	/**
	 * 2015-10-09
	 * Цель метода — отключение автозаполнения полей.
	 * https://developers.google.com/web/fundamentals/input/form/label-and-name-inputs?hl=en#recommended-input-name-and-autocomplete-attribute-values
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getElementHtml()
	 * @param Sb $sb
	 * @return void
	 */
	public function beforeGetElementHtml(Sb $sb) {$sb['autocomplete'] = 'new-password';}
}
