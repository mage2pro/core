<?php
namespace Df\Framework\Data\Form\Element;
use Df\Framework\Data\Form\Element\Renderer\Inline;
use Magento\Framework\Data\Form\AbstractForm;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
/**
 * @method RendererInterface|null getElementRendererDf()
 * @method Fieldset setElementRendererDf(RendererInterface$value)
 */
class Fieldset extends \Magento\Framework\Data\Form\Element\Fieldset {
	/**
	 * 2015-11-19
	 * https://mage2.pro/t/228
	 * «Propose to add a fieldset-specific element renderer»
	 * @override
	 * @param string $elementId
	 * @param string $type
	 * @param array $config
	 * @param bool|false $after
	 * @param bool|false $isAdvanced
	 * @return \Magento\Framework\Data\Form\Element\AbstractElement
	 */
	public function addField($elementId, $type, $config, $after = false, $isAdvanced = false) {
		/** @var \Magento\Framework\Data\Form\Element\AbstractElement $result */
		$result = parent::addField($elementId, $type, $config, $after, $isAdvanced);
		/** @var RendererInterface|null $renderer */
		if ($renderer = $this->getElementRendererDf()) {
			$result->setRenderer($renderer);
		}
		return $result;
	}

	/**
	 * 2015-11-19
	 * Родительский метод почему-то отбраковывает из результата элементы типа «fieldset»:
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/Fieldset.php#L62-L71
		if ($element->getType() != 'fieldset') {
			$elements[] = $element;
		}
	 * @override
	 * @see \Magento\Framework\Data\Form\Element\Fieldset::getChildren()
	 * @return AbstractElement[]
	 */
	public function getChildren() {return iterator_to_array($this->getElements());}

	/**
	 * 2015-11-19
	 * Сначала я пытался добавлять свои элементы внутри перекрытого метода
	 * @see \Magento\Framework\Data\Form\AbstractForm::_construct()
	 * Однако в случае вложенных филдсетов это работает некорректно,
	 * потому что форма ещё не инициализирована.
	 * Причём у этой проблемы две причины:
	 * одна устраняется методом \Df\Framework\Data\Form\Element\Fieldset::addElement()
	 * (подстановка правильной формы), а вторая — данным методом
	 * (создание элементов филдсета только после инициализации формы).
	 * @override
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::setForm()
	 * @param AbstractForm $form
	 * @return Fieldset
	 */
	public function setForm($form) {
		parent::setForm($form);
		if (!$this->_subElementsAdded) {
			$this->addSubElements();
			$this->_subElementsAdded = true;
		}
		return $this;
	}

	/**
	 * 2015-11-17
	 * @override
	 * @see \Magento\Framework\Data\Form\AbstractForm::_construct()
	 * @used-by \Magento\Framework\Data\Form\AbstractForm::__construct()
	 * @return void
	 */
	protected function _construct() {
		$this->addClass('df-fieldset');
		parent::_construct();
	}

	/**
	 * 2015-11-19
	 * @used-by \Df\Framework\Data\Form\Element\Fieldset::setForm()
	 * @return void
	 */
	protected function addSubElements() {}

	/**
	 * 2015-11-17
	 * @param string $name
	 * @param string $label
	 * @param mixed $value [optional]
	 * @return \Magento\Framework\Data\Form\Element\Checkbox
	 */
	protected function checkbox($name, $label, $value = null) {
		return $this->field($name, 'checkbox', $label, $value, ['checked' => $value])
			// Ядро никакого специфического для checkbox класса не добавляет
			->addClass('df-checkbox')
		;
	}

	/**
	 * 2015-11-17
	 * Независимые поля имеют имена: groups[frontend][fields][value__font__emphase__bold][value]
	 * У нас же имя будет: groups[frontend][fields][value__font][df_children][emphase__bold]
	 * @param string $name
	 * @return string
	 */
	protected function cn($name) {
		return "groups[{$this->group()}][fields][{$this->nameShort()}][df_children][{$name}]";
	}

	/**
	 * 2015-11-17
	 * @param string|null $key [optional]
	 * @return array(string => mixed)
	 */
	protected function fc($key = null) {
		/** @var array(string => mixed) $result */
		$result = $this['field_config'];
		return $key ? df_a($result, $key) : $result;
	}

	/**
	 * 2015-11-17
	 * @param string $name
	 * @param string $type
	 * @param string $label
	 * @param mixed $value [optional]
	 * @param array(string => mixed) $data [optional]
	 * @return \Magento\Framework\Data\Form\Element\AbstractElement
	 */
	protected function field($name, $type, $label, $value = null, $data = []) {
		return $this->addField($this->cn($name), $type, [
			'name' => $this->cn($name)
			,'label' => __($label)
			,'title' => __($label)
			,'value' => $value
		] + $data);
	}

	/**
	 * 2015-11-19
	 * @param \Magento\Framework\Data\Form\Element\AbstractElement|\Magento\Framework\Data\Form\Element\AbstractElement[] $elements
	 * @return \Magento\Framework\Data\Form\Element\AbstractElement|\Magento\Framework\Data\Form\Element\AbstractElement[]
	 */
	protected function inline($elements) {
		if (1 < func_num_args()) {
			$elements = func_get_args();
		}
		return
			is_array($elements)
			? array_map([$this, __FUNCTION__], $elements)
			: $elements->setRenderer(Inline::s())
		;
	}

	/**
	 * 2015-11-17
	 * @param string|null $cssClass [optional]
	 * @return \Df\Framework\Data\Form\Element\Fieldset\Inline
	 */
	protected function inlineFieldset($cssClass = null) {
		/** @var \Df\Framework\Data\Form\Element\Fieldset\Inline $result */
		$result = $this->addField('', 'Df\Framework\Data\Form\Element\Fieldset\Inline', [
			'field_config' => $this->fc()
		]);
		if ($cssClass) {
			$result->addClass($cssClass);
		}
		return $result;
	}

	/**
	 * 2015-11-17
	 * @param string $name
	 * @param string $label
	 * @param mixed $value [optional]
	 * @return \Magento\Framework\Data\Form\Element\Select
	 */
	protected function yesNo($name, $label, $value = null) {
		return $this->field($name, 'select', $label, $value, ['values' => df_yes_no()]);
	}

	/** @return string */
	private function group() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_last(explode('/', $this->fc('path')));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function nameShort() {return $this->fc('id');}

	/**
	 * 2015-11-19
	 * @var bool
	 * @used-by \Df\Framework\Data\Form\Element\Fieldset::setForm()
	 */
	private $_subElementsAdded;
}


