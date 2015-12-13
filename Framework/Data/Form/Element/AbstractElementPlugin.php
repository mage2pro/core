<?php
namespace Df\Framework\Data\Form\Element;
use Df\Framework\Data\Form\Element;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Phrase;
/**
 * 2015-12-13
 * Хитрая идея, которая уже давно пришла мне в голову:
 * наследуясь от модифицируемого класса,
 * мы получаем возможность вызывать методы с областью доступа protected
 * у переменной $subject.
 */
class AbstractElementPlugin  extends AbstractElement {
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
	 * Цель перекрытия — поддержка Font Awesome в качестве подписи элемента формы.
	 * Пример использования: http://code.dmitry-fedyuk.com/m2/all/blob/7cb37ab2c4d728bc20d29ca3c7c643e551f6eb0a/Framework/Data/Form/Element/Font.php#L40
	 * @see \Df\Framework\Data\Form\Element\Font::onFormInitialized()
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getLabelHtml()
	 * @param AbstractElement|Element $subject
	 * @param \Closure $proceed
	 * @param string|null $idSuffix
	 * @return string
	 */
	public function aroundGetLabelHtml(AbstractElement $subject, \Closure $proceed, $idSuffix = '') {
		/** @var string $label */
		$label = (string)$subject->getLabel();
		/** @var string $result */
		if (!$label || !df_starts_with($label, 'fa-')) {
			$result = $proceed($idSuffix);
		}
		else {
			/**
			 * 2015-12-13
			 * По сути, мы аккуратно имитируем возвращаемую модифицируемым методом вёрстку,
			 * только добавляем свои классы для Font Awesome и не добавляем исходную подпись,
			 * т.е. выводим, по сути, пустые теги <label><span></span></label>.
			 */
			$result = df_tag('label', [
				'class' => 'label admin__field-label fa ' . $label
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
			], df_tag('span', [])) . "\n";
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
