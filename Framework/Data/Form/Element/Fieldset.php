<?php
namespace Df\Framework\Data\Form\Element;
use Df\Framework\Data\Form\Element;
use Df\Framework\Data\Form\ElementI;
use Df\Framework\Data\Form\Element\Color;
use Df\Framework\Data\Form\Element\Renderer\Inline;
use Magento\Framework\Data\Form\Element\AbstractElement;
use \Magento\Framework\Data\Form\Element\Fieldset as _Fieldset;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\OptionSourceInterface;
/**
 * @method RendererInterface|null getElementRendererDf()
 * @method string|null getLabel()
 * @method Fieldset setElementRendererDf(RendererInterface $value)
 * @method Fieldset setLabel(string $value)
 * @method Fieldset setTitle(string $value)
 * @method Fieldset unsetLabel()
 * @method Fieldset unsetTitle()
 */
class Fieldset extends _Fieldset implements ElementI {
	/**
	 * 2015-12-12
	 * Важно илициализировать дочерний филдсет именно здесь,
	 * а не в методе @see \Df\Framework\Data\Form\Element\Fieldset::addField(),
	 * потому что к моменту завершения вызова @see \Df\Framework\Data\Form\Element\Fieldset::addField()
	 * дочерний филдсет должен быть уже инициализирован:
	 * внутри вызова @see \Df\Framework\Data\Form\Element\Fieldset::addField()
	 * вызывается метод  @see \Df\Framework\Data\Form\Element\Fieldset::onFormInitialized(),
	 * дочерние реализации которого уже требуют полной инициализации дочернего филдсета.
	 * @override
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::addElement()
	 * @used-by \Magento\Framework\Data\Form\AbstractForm::addField()
	 * @param AbstractElement $element
	 * @param bool $after [optional]
	 * @return $this
	 */
	public function addElement(AbstractElement $element, $after = false) {
		/**
		 * 2015-12-12
		 * Экзотическая конструкция «instanceof self» вполне допустима:
		 * https://3v4l.org/nWA6U
		 */
		if ($element instanceof self) {
			$element->addData([
				'field_config' => $this->fc()
				// 2015-12-07
				// Важно скопировать значения опций сюда,
				// чтобы дочерний филдсет мог создавать свои элементы
				// типа $fsCheckboxes->checkbox('bold', 'B');
				,'value' => $this['value']
			]);
		}
		parent::addElement($element, $after);
		return $this;
	}

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
		$renderer = $this->getElementRendererDf();
		if (!$renderer && df_is_admin()) {
			/**
			 * 2015-11-22
			 * По аналогии с https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Backend/Block/Widget/Form.php#L70-L75
			 * https://mage2.pro/t/239
			 * @uses \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element
			 */
			$renderer = \Df\Backend\Block\Widget\Form\Renderer\Fieldset\Element::s();
		}
		if ($renderer) {
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
	 * 2015-11-28
	 * @override
	 * https://mage2.pro/t/248
	 * «Class @see \Magento\Framework\Data\Form\Element\Fieldset
	 * breaks specification of the parent class
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement
	 * by not calling the method getBeforeElementHtml (getAfterElementHtml is called)»
	 * @see \Magento\Framework\Data\Form\Element\Fieldset::getElementHtml()
	 * @return string
	 */
	public function getElementHtml() {
		/** @var string $before */
		$before = $this->getBeforeElementHtml();
		/** @var string $result */
		$result = parent::getElementHtml();
		if (!df_starts_with($result, $before)) {
			$result = $before . $result;
		}
		return $result;
	}

	/**
	 * 2015-11-23
	 * @return \Df\Framework\Data\Form\Element\Fieldset
	 */
	public function hide() {
		/** @var Fieldset|Element $this */
		$this->setContainerClass('df-hidden');
		return $this;
	}

	/**
	 * 2015-11-19
	 * @override
	 * @see \Df\Framework\Data\Form\ElementI::onFormInitialized()
	 * @used-by \Df\Framework\Data\Form\Element\AbstractElementPlugin::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {
		$this['before_element_html'] .= df_link_inline([
			'Df_Framework::formElement/fieldset.css'
		]);
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
	 * 2015-11-17
	 * @param string $name
	 * @param string|null $label [optional]
	 * @return \Magento\Framework\Data\Form\Element\Checkbox|Element
	 */
	protected function checkbox($name, $label = null) {
		return $this->field($name, 'checkbox', $label, ['checked' => $this->vb($name)])
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
	 * 2015-11-24
	 * @param string|null $name [optional]
	 * @param string|null $label [optional]
	 * @return Color|Element
	 */
	protected function color($name = 'color', $label = null) {
		if ('' === $name) {
			$name = 'color';
		}
		if ('' === $label) {
			$label = 'Color';
		}
		return $this->field($name, Color::_C, $label);
	}

	/**
	 * 2015-11-17
	 * @param string|null $key [optional]
	 * @return array(string => mixed)
	 */
	protected function fc($key = null) {
		/** @var array(string => mixed) $result */
		$result = $this['field_config'];
		df_assert_array($result);
		return $key ? df_a($result, $key) : $result;
	}

	/**
	 * 2015-11-17
	 * @param string $name
	 * @param string $type
	 * @param string|null $label [optional]
	 * @param array(string => mixed) $data [optional]
	 * @return AbstractElement|Element
	 */
	protected function field($name, $type, $label = null, $data = []) {
		/** @var array(string => string) $params */
		$params = ['name' => $this->cn($name), 'value' => $this->v($name)];
		/**
		 * 2015-11-24
		 * Намеренно использую !is_null($label) вместо $label,
		 * потому что иногда нам нужен пустой тег label.
		 */
		if (!is_null($label)) {
			$params += ['label' => __($label), 'title' => __($label)];
		}
		/** @var AbstractElement|Element $result */
		$result = $this->addField($this->cn($name), $type, $params + $data);
		/**
		 * 2015-11-25
		 * Позволяет выбирать элементы по их короткому имени.
		 * Полное имя слишком длинно, использовать его в селекторах неудобно:
		 * groups[frontend][fields][value__font][df_children][bold].
		 */
		$result->addClass('df-name-' . $name);
		return $result;
	}

	/**
	 * 2015-11-19
	 * @param \Magento\Framework\Data\Form\Element\AbstractElement|\Magento\Framework\Data\Form\Element\AbstractElement[] $elements
	 * @return AbstractElement|AbstractElement[]|Element|Element[]
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
	 * @param string $name
	 * @param string|null $cssClass [optional]
	 * @return \Df\Framework\Data\Form\Element\Fieldset\Inline
	 */
	protected function inlineFieldset($name, $cssClass = null) {
		/** @var \Df\Framework\Data\Form\Element\Fieldset\Inline $result */
		/**
		 * 2015-12-12
		 * Propose to make the «config» param optional for the
		 * @uses \Magento\Framework\Data\Form\AbstractForm::addField() method
		 * https://mage2.pro/t/308
		 */
		$result = $this->addField($this->cn($name), Fieldset\Inline::_C, []);
		if ($cssClass) {
			$result->addClass($cssClass);
		}
		return $result;
	}

	/**
	 * 2015-11-30
	 * @used-by \Df\Framework\Data\Form\Element\Fieldset::yesNo()
	 * @param string $name
	 * @param string $label
	 * @param array(array(string => string|int))|string|OptionSourceInterface $values
	 * @return \Magento\Framework\Data\Form\Element\Select|Element
	 */
	protected function select($name, $label, $values) {
		if (!is_array($values)) {
			if (!$values instanceof OptionSourceInterface) {
				$values = df_o($values);
			}
			df_assert($values instanceof OptionSourceInterface);
			$values = $values->toOptionArray();
		}
		return $this->field($name, 'select', $label, ['values' => $values]);
	}

	/**
	 * 2015-12-11
	 * @param string|null $name [optional]
	 * @param string|null $label [optional]
	 * @return Size|Element
	 */
	protected function size($name = 'size', $label = null) {
		return $this->field($name, Size::_C, $label);
	}

	/**
	 * 2015-12-12
	 * @param string $name
	 * @param string|null $label [optional]
	 * @return Text|Element
	 */
	protected function text($name, $label = null) {return $this->field($name, Text::_C, $label);}

	/**
	 * 2015-11-17
	 * @param string $name
	 * @param string $label
	 * @return \Magento\Framework\Data\Form\Element\Select
	 */
	protected function yesNo($name, $label) {return $this->select($name, $label, df_yes_no());}

	/**
	 * 2015-12-07
	 * @param string $name
	 * @return string|null
	 */
	private function v($name) {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_a_deep($this->_data, 'value/df_children', []);
		}
		return df_a($this->{__METHOD__}, $name);
	}

	/**
	 * 2015-12-07
	 * Когда галка чекбокса установлена, то значением настроечного поля является пустая строка,
	 * а когда галка не установлена — то ключ значения отсутствует.
	 * @param string $name
	 * @return string
	 */
	private function vb($name) {return !is_null($this->v($name));}

	/** @return string */
	private function group() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_last(explode('/', $this->fc('path')));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function nameShort() {return $this->fc('id');}
}


