<?php
namespace Df\Framework\Data\Form\Element;
use Df\Framework\Data\Form\Element;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Multiline;
use Magento\Framework\Phrase;
/**
 * 2015-12-13
 * Хитрая идея, которая уже давно пришла мне в голову:
 * наследуясь от модифицируемого класса,
 * мы получаем возможность вызывать методы с областью доступа protected
 * у переменной $subject.
 */
class AbstractElementPlugin extends AbstractElement {
	/**
	 * 2015-10-09
	 * Цель метода — отключение автозаполнения полей.
	 * https://developers.google.com/web/fundamentals/input/form/label-and-name-inputs?hl=en#recommended-input-name-and-autocomplete-attribute-values
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getHtmlAttributes()
	 * @param AbstractElement $subject
	 * @param string[] $result
	 * @return string[]
	 */
	public function afterGetHtmlAttributes(AbstractElement $subject, $result) {
		$result[]= 'autocomplete';
		return $result;
	}

	/**
	 * 2015-11-24
	 * Многие операции над элементом допустимы только при наличии формы,
	 * поэтому мы выполняем их в обработчике @see \Df\Framework\Data\Form\Element::onFormInitialized
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::setForm()
	 * @param AbstractElement $subject
	 * @param AbstractElement $result
	 * @return string[]
	 */
	public function afterSetForm(AbstractElement $subject, AbstractElement $result) {
		if (!isset($subject->{__METHOD__}) && $subject instanceof \Df\Framework\Data\Form\ElementI) {
			$subject->onFormInitialized();
			$subject->{__METHOD__} = true;
		}
		return $result;
	}

	/**
	 * 2015-12-13
	 * Отличия от модицицируемого метода
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getLabelHtml():
	 * 1) Добавляем свои классы для Font Awesome.
	 * 2) При использовании Font Awesome не добавляем исходную подпись
	 * (значением которой является класс Font Awesome)
	 * и выводим, по сути, пустые теги <label><span></span></label>.
	 * 3) Добавляем атрибут title.
	 *
	 * Пример использования Font Awesome: http://code.dmitry-fedyuk.com/m2/all/blob/7cb37ab2c4d728bc20d29ca3c7c643e551f6eb0a/Framework/Data/Form/Element/Font.php#L40
	 *
	 * @see \Df\Framework\Data\Form\Element\Font::onFormInitialized()
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getLabelHtml()
	 * @param AbstractElement|Element $subject
	 * @param \Closure $proceed
	 * @param string|null $idSuffix
	 * @return string
	 */
	public function aroundGetLabelHtml(AbstractElement $subject, \Closure $proceed, $idSuffix = '') {
		/** @var string|null|Phrase $label */
		$label = $subject->getLabel();
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
			if ('' === $idSuffix && $subject instanceof Multiline) {
				$idSuffix = 0;
			}
			/** @var bool $isFontAwesome */
			$isFontAwesome = df_starts_with($label, 'fa-');
			/** @var string $class */
			$class = 'label admin__field-label';
			if ($isFontAwesome) {
				$class .=  ' fa ' . $label;
				$label = '';
			}
			/** @var array(string => string) $params */
			$params = [
				'class' => $class
				,'for' => $subject->getHtmlId() . $idSuffix
				/**
				 * 2015-12-13
				 * Метод @uses \Magento\Framework\Data\Form\Element\AbstractElement::_getUiId()
				 * возвращает атрибут и его значение уже в виже слитной строки,
				 * поэтому парсим её.
				 * Обратите внимание, что метод — protected, и чтобы получить к нему доступ,
				 * мы унаследовали наш плагин от носителя метода.
				 */
				,'data-ui-id' => df_trim(df_last(explode('=', $subject->_getUiId('label'))), '"')
			];
			/** @var string $title */
			$title = (string)$subject->getTitle();
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
	 * @param AbstractElement $subject
	 * @return array()
	 */
	public function beforeGetElementHtml(AbstractElement $subject) {
		$subject['autocomplete'] = 'new-password';
		return [];
	}
}
