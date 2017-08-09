<?php
namespace Df\Framework\Form\Element;
use Magento\Framework\Data\Form\Element\Checkbox as _Checkbox;
/**
 * 2015-12-21
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * Переделываем родительский класс, потому что он нормально не работает (да и не используется ядром)
 * в разделе «Stores» → «Configuration».
 */
class Checkbox extends _Checkbox {
	/**
	 * 2015-12-21 Перекрываем магический метод.
	 * 2017-08-09 We can safely mark this method as «final» because this method is magic in the parent class.
	 * https://github.com/mage2pro/core/issues/20
	 * @override
	 * @see _Checkbox::getChecked() It is a magic method.
	 * @used-by _Checkbox::getElementHtml():
	 *		public function getElementHtml() {
	 *			if ($checked = $this->getChecked()) {
	 *				$this->setData('checked', true);
	 *			}
	 *			else {
	 *				$this->unsetData('checked');
	 *			}
	 *			return parent::getElementHtml();
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/lib/internal/Magento/Framework/Data/Form/Element/Checkbox.php#L55-L67
	 * @return bool
	 */
	final function getChecked() {return $this['checked'] || $this['value'];}

	/**
	 * 2016-11-20
	 * Перекрываем магический метод,
	 * потому что к магическим методам не применяются плагины, а нам надо применить плагин.
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see _Checkbox::getComment() It is a magic method.
	 * @see \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetComment()
	 * @used-by \Magento\Config\Block\System\Config\Form\Field::_renderValue()
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/app/code/Magento/Config/Block/System/Config/Form/Field.php#L79-L81
	 *	if ((string)$element->getComment()) {
	 *		$html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
	 *	}
	 * @return string|null
	 */
	function getComment() {return $this['comment'];}

	/**
	 * 2015-12-21
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see _Checkbox::getElementHtml()
	 * @used-by \Magento\Framework\Data\Form\Element\AbstractElement::getDefaultHtml():
	 *		public function getDefaultHtml() {
	 *			$html = $this->getData('default_html');
	 *			if ($html === null) {
	 *				$html = $this->getNoSpan() === true ? '' : '<div class="admin__field">' . "\n";
	 *				$html .= $this->getLabelHtml();
	 *				$html .= $this->getElementHtml();
	 *				$html .= $this->getNoSpan() === true ? '' : '</div>' . "\n";
	 *			}
	 *			return $html;
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/lib/internal/Magento/Framework/Data/Form/Element/AbstractElement.php#L426-L441
	 * @return string
	 */
	function getElementHtml() {
		$this->addClass('df-checkbox');
		if ($this->getChecked()) {
			$this->setData('checked', 'checked');
		}
		else {
			$this->unsetData('checked');
		}
		$this->unsetData('value');
		$result = ''; /** @var string $result */
		$htmlId = $this->getHtmlId();
		if (($before = $this->getBeforeElementHtml())) {
			$result .= df_tag('label', ['class' => 'addbefore', 'for' => $htmlId], $before);
		}
		$result .= df_tag('input', df_fe_attrs($this));
		if (($afterElementJs = $this->getAfterElementJs())) {
			$result .= $afterElementJs;
		}
		if (($after = $this->getAfterElementHtml())) {
			$result .= df_tag('label', ['class' => 'addafter', 'for' => $htmlId], $after);
		}
		return $result;
	}

	/**
	 * 2015-12-21
	 * 2017-08-09
	 * This method is not used anywhere, but I have redefined it, because it exists in the parent class.
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see _Checkbox::getIsChecked()
	 * @return bool
	 */
	function getIsChecked() {return $this->getChecked();}

	/**
	 * 2015-12-07
	 * Когда галка чекбокса установлена, то значением настроечного поля является пустая строка,
	 * а когда галка не установлена — то ключ значения отсутствует.
	 * 2015-12-21 Всё чуточку изменилось...
	 * @param mixed $value
	 * @param bool|callable $default [optional]
	 * @return bool
	 */
	final static function b($value, $default = false) {return df_if1(
		is_null($value), $default, '' === $value || df_bool($value)
	);}
}