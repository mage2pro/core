<?php
namespace Df\Framework\Form\Element;
use Magento\Framework\Data\Form\Element\Checkbox as _Checkbox;
/**
 * 2015-12-21
 * Переделываем родительский класс,
 * потому что он нормально не работает (да и не используется ядром)
 * в разделе «Stores» → «Configuration»
 */
class Checkbox extends _Checkbox {
	/**
	 * 2015-12-21
	 * @override
	 * Перекрываем магический метод
	 * @return bool
	 */
	function getChecked() {return $this['checked'] || $this['value'];}

	/**
	 * 2016-11-20
	 * @override
	 * Перекрываем магический метод,
	 * потому что к магическим методам не применяются плагины, а нам надо применить плагин
	 * @see \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetComment()
	 * @return string|null
	 */
	function getComment() {return $this['comment'];}

	/**
	 * 2015-12-21
	 * @override
	 * @see \Magento\Framework\Data\Form\Element\Checkbox::getElementHtml()
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
		/** @var string $result */
		$result = '';
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
	 * @override
	 * @see \Magento\Framework\Data\Form\Element\Checkbox::getIsChecked()
	 * @return bool
	 */
	function getIsChecked() {return $this->getChecked();}

	/**
	 * 2015-12-07
	 * Когда галка чекбокса установлена, то значением настроечного поля является пустая строка,
	 * а когда галка не установлена — то ключ значения отсутствует.
	 * 2015-12-21
	 * Всё чуточку изменилось...
	 * @param mixed $value
	 * @param bool|callable $default [optional]
	 * @return bool
	 */
	public static function b($value, $default = false) {
		return df_if1(is_null($value), $default, '' === $value || df_bool($value));
	}
}