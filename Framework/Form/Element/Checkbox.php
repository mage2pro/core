<?php
namespace Df\Framework\Form\Element;
use Magento\Framework\Data\Form\Element\Checkbox as _P;
/**
 * 2015-12-21
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * Переделываем родительский класс, потому что он нормально не работает (да и не используется ядром)
 * в разделе «Stores» → «Configuration».
 * @used-by \KingPalm\B2B\Block\Registration::cb()
 */
class Checkbox extends _P {
	/**
	 * 2015-12-21 Перекрываем магический метод.
	 * 2017-08-09 We can safely mark this method as «final» because this method is magic in the parent class.
	 * https://github.com/mage2pro/core/issues/20
	 * @override
	 * @see _P::getChecked() It is a magic method.
	 * @used-by _P::getElementHtml():
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
	 * @see _P::getComment() It is a magic method.
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
	 * @see _P::getElementHtml()
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
		/** @var string|null $label */ /** @var string|null $before */ /** @var string|null $after */
		# 2020-03-02
		# The square bracket syntax for array destructuring assignment (`[…] = […]`) requires PHP ≥ 7.1:
		# https://github.com/mage2pro/core/issues/96#issuecomment-593392100
		# We should support PHP 7.0.
		list($before, $after) =
			!($label = $this[self::LABEL]) ? [$this->getBeforeElementHtml(), $this->getAfterElementHtml()] : (
				!!$this[self::LABEL_POSITION_BEFORE] ? [$label, null] : [null, $label]
			)
		;
		if ($before) {
			$result .= df_tag('label', ['class' => 'addbefore', 'for' => $htmlId], $before);
		}
		$result .= df_tag('input', df_fe_attrs($this));
		if (($afterElementJs = $this->getAfterElementJs())) {
			$result .= $afterElementJs;
		}
		if ($after) {
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
	 * 2019-05-30
	 * The `label` key is internally used by Magento, so I use another name for my key.
	 * Actually, the Magento's backend configuration section renders the `label` key as a label,
	 * but in a separate control:
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getLabelHtml()
	 *		if ($this->getLabel() !== null) {
	 *			$html = '<label class="label admin__field-label" for="' .
	 *			$this->getHtmlId() . $idSuffix . '"' . $this->_getUiId(
	 *				'label'
	 *			) . '><span' . $scopeLabel . '>' . $this->_escape(
	 *				$this->getLabel()
	 *			) . '</span></label>' . "\n";
	 *		} else {
	 *			$html = '';
	 *		}
	 * https://github.com/magento/magento2/blob/2.3.1/lib/internal/Magento/Framework/Data/Form/Element/AbstractElement.php#L417-L439
	 * @used-by self::getElementHtml()
	 * @used-by vendor/kingpalm/b2b/view/frontend/templates/registration.phtml
	 */
	const LABEL = 'df_label';

	/**
	 * 2019-05-30
	 * @used-by self::getElementHtml()
	 */
	const LABEL_POSITION_BEFORE = 'label_position_before';

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