<?php
namespace Df\Framework\Form\Element;
use Df\Backend\Block\Widget\Form\Renderer\Fieldset\Element as BackendRenderer;
use Df\Config\Source\SizeUnit;
use Df\Core\Exception as DFE;
use Df\Framework\Form\Element as E;
use Df\Framework\Form\Element\Renderer\Inline;
use Df\Framework\Form\Element\Select2\Number as Select2Number;
use Df\Framework\Form\ElementI;
use Magento\Framework\Data\Form\AbstractForm;
use Magento\Framework\Data\Form\Element\AbstractElement as AE;
use Magento\Framework\Data\Form\Element\Fieldset as _Fieldset;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\Form\Element\Textarea;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Phrase;
/**
 * 2015-11-17
 * @see \Df\Framework\Form\Element\Fieldset\Inline
 * @see \Df\Framework\Form\Element\Font
 * @see \Dfe\AllPay\InstallmentSales\Plan\FE
 * @see \Dfe\CurrencyFormat\FE
 * @see \Doormall\Shipping\Partner\FE
 * @method AbstractForm|Fieldset getContainer()
 * @method RendererInterface|null getElementRendererDf()
 * @method mixed[] getFieldConfig()
 * @method string|null|Phrase getLabel()
 * @method string|null|Phrase getTitle()
 * @method mixed getValue()
 * @method Fieldset setElementRendererDf(RendererInterface $value)
 * @method Fieldset setLabel(string $value)
 * @method Fieldset setTitle(string $value)
 * @method Fieldset setValue(mixed $value)
 * @method Fieldset unsLabel()
 * @method Fieldset unsTitle()
 * @method Fieldset unsValue()
 */
class Fieldset extends _Fieldset implements ElementI {
	/**
	 * 2015-12-12
	 * Важно инициализировать дочерний филдсет именно здесь,
	 * а не в методе @see \Df\Framework\Form\Element\Fieldset::addField(),
	 * потому что к моменту завершения вызова @see \Df\Framework\Form\Element\Fieldset::addField()
	 * дочерний филдсет должен быть уже инициализирован:
	 * внутри вызова @see \Df\Framework\Form\Element\Fieldset::addField()
	 * вызывается метод  @see \Df\Framework\Form\Element\Fieldset::onFormInitialized(),
	 * дочерние реализации которого уже требуют полной инициализации дочернего филдсета.
	 * @override
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::addElement()
	 * @used-by \Magento\Framework\Data\Form\AbstractForm::addField()
	 * @param AE $element
	 * @param bool $after [optional]
	 * @return $this
	 */
	function addElement(AE $element, $after = false) {
		// 2015-12-12 An exotic expression «instanceof self» is totally valid: https://3v4l.org/nWA6U
		if ($element instanceof self) {
			/**
			 * 2015-12-12
			 * В ядре уже есть магические методы setContainer() / getContainer(),
			 * и я сначала пробовал использовать их, однако порой ядро пихает туда
			 * не родительский филдсет, а чёрти чё:
			 * @see \Magento\Framework\Data\Form\Element\Collection::add()
			 * $element->setContainer($this->_container);
			 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/Collection.php#L110
			 * Здесь вот ядро пихает туда форму: объект класса @see \Magento\Framework\Data\Form
			 * Поэтому разработал свой способ учёта иерархии.
			 */
			$element->_parent = $this;
		}
		parent::addElement($element, $after);
		return $this;
	}

	/**
	 * 2015-11-19 «Propose to add a fieldset-specific element renderer» https://mage2.pro/t/228
	 * @override
	 * @param string $elementId
	 * @param string $type
	 * @param array $config
	 * @param bool|false $after
	 * @param bool|false $isAdvanced
	 * @return AE
	 */
	function addField($elementId, $type, $config, $after = false, $isAdvanced = false) {
		$result = parent::addField($elementId, $type, $config, $after, $isAdvanced); /** @var AE $result */
		/** @var RendererInterface|null $renderer */
		if ($renderer = $this->getElementRendererDf() ?: (!df_is_backend() ? null :
			/**
			 * 2015-11-22
			 * By analogy with https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Backend/Block/Widget/Form.php#L70-L75
			 * https://mage2.pro/t/239
			 * @uses \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element
			 */
			BackendRenderer::s()
		)) {
			$result->setRenderer($renderer);
		}
		return $result;
	}

	/**
	 * 2015-11-19
	 * Родительский метод почему-то отбраковывает из результата элементы типа «fieldset»:
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/Fieldset.php#L62-L71
	 *	if ($element->getType() != 'fieldset') {
	 *		$elements[] = $element;
	 *	}
	 * @override
	 * @see \Magento\Framework\Data\Form\Element\Fieldset::getChildren()
	 * @return AE[]
	 */
	function getChildren() {return iterator_to_array($this->getElements());}

	/**
	 * 2015-11-23
	 * @return $this
	 */
	function hide() {df_hide($this); return $this;}

	/**
	 * 2015-11-19
	 * @override
	 * @see \Df\Framework\Form\ElementI::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 * @see \Df\Framework\Form\Element\Font::onFormInitialized()
	 * @see \Df\Framework\Form\Element\Quantity::onFormInitialized()
	 * @see \Dfe\AllPay\InstallmentSales\Plan\FE::onFormInitialized()
	 * @see \Dfe\CurrencyFormat\FE::onFormInitialized()
	 * @see \Doormall\Shipping\Partner\FE::onFormInitialized()
	 */
	function onFormInitialized() {df_fe_init($this, __CLASS__);}

	/**
	 * 2015-12-12
	 * @used-by df_fe_top()
	 * Возвращает филдсет самого верхнего уровня.
	 * У филдсета самого верхнего уровня метод getContainer() возвращает форму.
	 * @return Fieldset
	 */
	function top() {return dfc($this, function() {return $this->isTop() ? $this : $this->_parent->top();});}

	/**
	 * 2015-11-17
	 * @override
	 * @see \Magento\Framework\Data\Form\AbstractForm::_construct()
	 * @used-by \Magento\Framework\Data\Form\AbstractForm::__construct()
	 */
	protected function _construct() {$this->addClass('df-fieldset'); parent::_construct();}

	/**
	 * 2015-11-17
	 * @param string $name
	 * @param string|null|Phrase $label [optional]
	 * @param array(string => mixed)|bool|string $value [optional]
	 * @param string|null $note [optional]
	 * @return \Magento\Framework\Data\Form\Element\Checkbox|E
	 */
	protected function checkbox($name, $label = null, $value = null, $note = null) {
		$data = is_array($value) ? $value + ['note' => $note] : (
			is_bool($value)
			? ['checked' => $value, 'note' => $note]
			: ['note' => $value]
		);
		return $this->field($name, Checkbox::class, $label, [
			'checked' => Checkbox::b($this->v($name), df_bool(dfa($data, 'checked')))
		] + $data);
	}

	/**
	 * 2015-11-17
	 * Независимые поля имеют имена: groups[frontend][fields][value__font__emphase__bold][value]
	 * У нас же имя будет: groups[frontend][fields][value__font][df_children][emphase__bold]
	 * @param string $name
	 * @return string
	 */
	protected function cn($name) {return $this->nameFull() . "[{$name}]";}

	/**
	 * 2015-11-24
	 * @param string|null $name [optional]
	 * @param string|null|Phrase $label [optional]
	 * @param array(string => mixed) $data [optional]
	 * @return Color|E
	 */
	protected function color($name = 'color', $label = null, $data = []) {
		/**
		 * 2015-12-13
		 * Намеренно использую строгое стравнение с пустой строкой,
		 * потому что $label может быть как пустой строкой, так и null,
		 * и система будет вести себя по-разному в этих случаях.
		 * Если $label равно null, то подпись у элемента будет отсутствовать.
		 * Если $label равно пустой строке, то у элемента будет пустая подпись:
		 * пустые теги <label><span></span></label>
		 * Пустая подпись позволяет нам задействовать в качестве подписи FontAwesome:
		 * мы цепляем к пустому тегу label правила типа:
		 *		> label:not(.addafter) {
		 *			display: inline-block;
		 *			font-family: FontAwesome;
		 *			// http://fortawesome.github.io/Font-Awesome/icon/text-width/
		 *			&:before {content: "\f035";}
		 *		}
		 */
		if (!is_null($label) && '' === (string)$label) {
			$label = 'Color';
		}
		return $this->field('' === $name ? 'color' : $name, Color::class, $label, $data);
	}

	/**
	 * 2015-11-17
	 * @used-by \Df\Framework\Form\Element\ArrayT::itemType()
	 * @param string|null $k [optional]
	 * @param string|null|callable $d [optional]
	 * @return string|null|array(string => mixed)
	 */
	final protected function fc($k = null, $d = null) {return df_fe_fc($this, $k, $d);}

	/**
	 * 2015-11-17
	 * 2015-12-13
	 * $label может быть как пустой строкой, так и null,
	 * и система будет вести себя по-разному в этих случаях.
	 * Если $label равно null, то подпись у элемента будет отсутствовать.
	 * Если $label равно пустой строке, то у элемента будет пустая подпись:
	 * пустые теги <label><span></span></label>
	 * Пустая подпись позволяет нам задействовать в качестве подписи FontAwesome:
	 * мы цепляем к пустому тегу label правила типа:
	 *		> label:not(.addafter) {
	 *			display: inline-block;
	 *			font-family: FontAwesome;
	 *			// http://fortawesome.github.io/Font-Awesome/icon/text-width/
	 *			&:before {content: "\f035";}
	 *		}
	 * 2015-12-13
	 * Отныне в качестве подписи можно указывать название класса Font Awesome.
	 * @param string $name
	 * @param string $type
	 * @param string|null|Phrase $label [optional]
	 * @param array(string => mixed) $data [optional]
	 * @return AE|E
	 */
	protected function field($name, $type, $label = null, $data = []) {
		/**
		 * 2015-12-13
		 * Приходящее из $data значение $value будем использовать только как значение по умолчанию
		 * при отсутствии ранее сохранённого в базе данных значения.
		 * Пример использования: @see \Df\Framework\Form\Element\Fieldset::sizePercent()
		 * https://github.com/mage2pro/core/tree/b73b3cfb6f75f89a3864fe619e6a125535574ac2/Framework/Data/Form/Element/Fieldset.php#L415
		 */
		$value = $this->v($name); /** @var mixed $value */
		if (is_null($value)) {
			$value = dfa($data, 'value');
		}
		unset($data['value']);
		$params = ['name' => $this->cn($name), 'value' => $value]; /** @var array(string => string) $params */
		/**
		 * 2015-11-24
		 * Намеренно использую !is_null($label) вместо $label,
		 * потому что иногда нам нужен пустой тег label.
		 * 2015-12-13
		 * $label может быть как пустой строкой, так и null,
		 * и система будет вести себя по-разному в этих случаях.
		 * Если $label равно null, то подпись у элемента будет отсутствовать.
		 * Если $label равно пустой строке, то у элемента будет пустая подпись:
		 * пустые теги <label><span></span></label>
		 * Пустая подпись позволяет нам задействовать в качестве подписи FontAwesome:
		 * мы цепляем к пустому тегу label правила типа:
		 *		> label:not(.addafter) {
		 *			display: inline-block;
		 *			font-family: FontAwesome;
		 *			// http://fortawesome.github.io/Font-Awesome/icon/text-width/
		 *			&:before {content: "\f035";}
		 *		}
		 */
		if (!is_null($label)) {
			$params += ['label' => __($label)];
		}
		$additionalClass = dfa($data, self::$FD__CSS_CLASS); /** @var string|null $additionalClass */
		unset($data[self::$FD__CSS_CLASS]);
		/**
		 * 2016-07-30
		 * Здесь происходит рекурсия, потому что добавление поля к внутреннему филдсету
		 * сводится (через десяток вызовов) к добавлению этого поля к филдсету самого верхнего уровня.
		 */
		$result = $this->addField($this->cn($name), $type, $params + $data); /** @var AE|E $result */
		/**
		 * 2015-11-25
		 * Позволяет выбирать элементы по их короткому имени.
		 * Полное имя слишком длинно, использовать его в селекторах неудобно:
		 * groups[frontend][fields][value__font][df_children][bold].
		 */
		$result->addClass(self::customCssClassByShortName($name));
		if ($additionalClass) {
			$result->addClass($additionalClass);
		}
		return $result;
	}

	/**
	 * 2015-12-29
	 * @todo Видимо, от этого метода надо избавляться.
	 * Обратите внимание, как работает, например,
	 * @see \Df\Framework\Form\Element\Fieldset::size()
	 * Этот метод использует способ, который кажется мне более оптимальным:
	 * https://github.com/mage2pro/core/tree/e7fcbd9c04a904e9e0d196c56e6a60d6eab0835a/Framework/Data/Form/Element/Fieldset.php#L443
	 * @param string|null $class [optional]
	 * @param string|null $cssClass [optional]
	 * @return Fieldset
	 */
	protected function fieldset($class = null, $cssClass = null) {
		if (!$class) {
			$class = __CLASS__;
		}
		/** @var Fieldset $result */
		// 2015-12-29
		// Раньше имя создавалось так: df_uid(4, 'fs')
		$result = $this->addField($this->cn('fs' . $this->_childFieldsetNextId++), $class, [
			/**
			 * 2015-12-07
			 * Важно скопировать значения опций сюда,
			 * чтобы дочерний филдсет мог создавать свои элементы
			 * типа $fsCheckboxes->checkbox('bold', 'B');
			 * Что интересно, добавление вместо этого метода getValue
			 * почему-то не работает:
			 *	function getValue() {return $this->top()->getData('value');}
			 */
			'value' => $this['value']
		]);
		/**
		 * 2015-12-12
		 * Флаг анонимности филдсета.
		 * Анонимные филдсеты не добавляют своё имя в качестве префикса имён полей.
		 */
		$result->_anonymous = true;
		if ($cssClass) {
			$result->addClass($cssClass);
		}
		return $result;
	}

	/**
	 * 2015-11-17
	 * @param string|null $cssClass [optional]
	 * @return Fieldset\Inline
	 */
	protected function fieldsetInline($cssClass = null) {return $this->fieldset(
		Fieldset\Inline::class, $cssClass
	);}

	/**
	 * 2015-12-28
	 * @param string $name
	 * @param string $value
	 * @param string|null|Phrase $label [optional]
	 * @return Hidden
	 */
	protected function hidden($name, $value, $label = null) {
		$result = $this->field($name, Hidden::class, $label, ['value' => $value]);
		$result->setAfterElementHtml($label);
		return $result;
	}

	/**
	 * 2015-11-19
	 * @param \Magento\Framework\Data\Form\Element\AbstractElement|\Magento\Framework\Data\Form\Element\AbstractElement[] $elements
	 * @return AE|AE[]|E|E[]
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
	 * 2016-07-30
	 * @used-by \Dfe\AllPay\InstallmentSales\Plan\FE::onFormInitialized()
	 * @used-by \Doormall\Shipping\Partner\FE::onFormInitialized()
	 * @param string $name
	 * @param string|null|Phrase $label [optional]
	 * @param int|float|null $default [optional]
	 * @param array(string => mixed) $data [optional]
	 * @return Quantity|E
	 */
	protected function money($name, $label = null, $default = null, $data = []) {return $this->number(
		$name, $label, $data + [
			'value' => $default, Number::LABEL_RIGHT => df_currency_base($this->scope())->getCode()
		])
	;}

	/**
	 * 2016-08-02
	 * @used-by money()
	 * @used-by percent()
	 * @param string $name
	 * @param string|null|Phrase $label [optional]
	 * @param array(string => mixed) $data [optional]
	 * @return E|Number
	 */
	protected function number($name, $label = null, $data = []) {return $this->field(
		$name, Number::class, $label, $data
	);}

	/**
	 * 2015-12-13
	 * @used-by \Df\Framework\Form\Element\Font::onFormInitialized()
	 * @used-by \Dfe\AllPay\InstallmentSales\Plan\FE::onFormInitialized()
	 * @param string $name
	 * @param string|null|Phrase $label [optional]
	 * @param int|null $default [optional]
	 * @param array(string => mixed) $data [optional]
	 * @return Number|E
	 */
	protected function percent($name, $label = null, $default = null, $data = []) {return $this->number(
		$name, $label, $data + ['value' => $default, Number::LABEL_RIGHT => '%']
	);}

	/**
	 * 2016-07-30
	 * @param string $name
	 * @param string|null|Phrase $label [optional]
	 * @param array(string => mixed) $data [optional]
	 * @return Quantity|E
	 */
	protected function quantity($name, $label = null, $data = []) {return $this->field(
		$name, Quantity::class, $label, $data
	);}

	/**
	 * 2015-11-30
	 * 2015-12-13
	 * Обратите внимание, что $label может быть как пустой строкой, так и null,
	 * и система будет вести себя по-разному в этих случаях.
	 * Если $label равно null, то подпись у элемента будет отсутствовать.
	 * Если $label равно пустой строке, то у элемента будет пустая подпись:
	 * пустые теги <label><span></span></label>
	 * Пустая подпись позволяет нам задействовать в качестве подписи FontAwesome:
	 * мы цепляем к пустому тегу label правила типа:
	 *		> label:not(.addafter) {
	 *			display: inline-block;
	 *			font-family: FontAwesome;
	 *			// http://fortawesome.github.io/Font-Awesome/icon/text-width/
	 *			&:before {content: "\f035";}
	 *		}
	 *
	 * 2015-12-28
	 * Добавил возможность передачи в качестве $values простого одномерного массива,
	 * например: $this->select('decimalSeparator', 'Decimal Separator', ['.', ',']);
	 *
	 * @used-by \Df\Framework\Form\Element\Fieldset::yesNo()
	 * @param string $name
	 * @param string|null|Phrase $label
	 * @param array(array(string => string|int))|string[]|string|OptionSourceInterface $values
	 * @param array(string => mixed)|string $data [optional]
	 * @param string|null $type [optional]
	 * @return \Magento\Framework\Data\Form\Element\Select|E
	 */
	protected function select($name, $label, $values, $data = [], $type = 'select') {
		if (!is_array($values)) {
			if (!$values instanceof OptionSourceInterface) {
				$values = df_o($values);
			}
			df_assert($values instanceof OptionSourceInterface);
			$values = $values->toOptionArray();
		}
		if (!is_array($data)) {
			$data = ['note' => $data];
		}
		return $this->field($name, $type, $label, $data + [
			'values' => df_a_to_options($values)
		]);
	}

	/**
	 * 2016-08-10
	 * @param string $name
	 * @param string|null|Phrase $label
	 * @param array(array(string => string|int))|string[]|string|OptionSourceInterface $values
	 * @param array(string => mixed)|string $data [optional]
	 * @return \Magento\Framework\Data\Form\Element\Select|E
	 */
	protected function select2($name, $label, $values, $data = []) {return $this->select(
		$name, $label, $values, $data, Select2::class
	);}

	/**
	 * 2016-08-10
	 * @param string $name
	 * @param string|null|Phrase $label
	 * @param array(array(string => string|int))|string[]|string|OptionSourceInterface $values
	 * @param array(string => mixed)|string $data [optional]
	 * @return \Magento\Framework\Data\Form\Element\Select|E
	 */
	protected function select2Number($name, $label, $values, $data = []) {return $this->select(
		$name, $label, $values, $data, Select2Number::class
	);}

	/**
	 * 2015-12-11
	 * @param string $name
	 * @param string|null|Phrase $label [optional]
	 * @param array(string => mixed) $data [optional]
	 * @return Quantity|E
	 */
	protected function size($name, $label = null, $data = []) {return $this->quantity(
		$name, $label, $data + [Quantity::P__VALUES => SizeUnit::s()->toOptionArray()]
	);}

	/**
	 * 2015-12-12
	 * @used-by \Df\Framework\Form\Element\Quantity::onFormInitialized()
	 * @param string $name
	 * @param string|null|Phrase $label [optional]
	 * @param array(string => mixed) $data [optional]
	 * @return Text|E
	 */
	final protected function text($name, $label = null, $data = []) {return $this->field(
		$name, Text::class, $label, $data
	);}

	/**
	 * 2018-04-20
	 * @used-by \Doormall\Shipping\Partner\FE::onFormInitialized()
	 * @param string $name
	 * @param string|null|Phrase $label [optional]
	 * @param array(string => mixed) $data [optional]
	 * @return Textarea|E
	 */
	final protected function textarea($name, $label = null, $data = []) {return $this->field(
		$name, Textarea::class, $label, $data
	);}

	/**
	 * 2015-12-07
	 * @used-by \Df\Framework\Form\Element\ArrayT::onFormInitialized()
	 * @param string|null $name [optional]
	 * @return string|null  
	 * @throws DFE
	 */
	final protected function v($name = null) {return dfak($this, function() {
		$result = dfa($this->_data, 'value', []);
		/**
		 * 2016-06-29
		 * Что интересно, при смене области действия настроек с глобальной на другую (сайт или магазин)
		 * поле «value» может почему-то содержать не массив,
		 * а строку JSON, соответствующую запакованному в JSON массиву:
		 * https://code.dmitry-fedyuk.com/m2e/currency-format/issues/1
		 * Заметил это только для модуля «Price Format».
		 */
		return is_array($result) ? $result : df_json_decode($result);
	}, $name);}

	/**
	 * 2015-11-17
	 * @param string $name
	 * @param string|Phrase $label
	 * @return \Magento\Framework\Data\Form\Element\Select
	 */
	protected function yesNo($name, $label) {return $this->select($name, $label, df_yes_no());}

	/**
	 * 2015-12-12
	 * @return bool
	 */
	private function isTop() {return dfc($this, function() {return !$this->_parent instanceof self;});}

	/**
	 * 2015-12-12
	 * Для филдсета верхнего уровня:
	 * *) getName() возвращает «groups[frontend][fields][value_font][value]»
	 * *) getId() возвращает dfe_sku_frontend_value_font
	 * Для подчинённых филдсетов мы getId() равно getName()
	 * @return string
	 */
	private function nameFull() {return dfc($this, function() {return
		$this->isTop()
		? df_trim_text_right($this->getName(), '[value]')
		// Анонимные филдсеты не добавляют своё имя в качестве префикса имён полей.
		: (!$this->_anonymous ? $this->getId() : $this->_parent->nameFull())
	;});}

	/**
	 * 2016-12-15
	 * По умолчанию «scope» равно «default», а для магазина — «stores».
	 * По умолчанию «scope_id» равно пустой строке.
	 * @return array(int|string)
	 */
	private function scope() {return dfc($this, function() {$t = $this->top(); return [
		$t['scope'], $t['scope_id']
	];});}

	/**
	 * 2015-12-29
	 * @used-by \Df\Framework\Form\Element\Fieldset::fieldset()
	 * @var int
	 */
	private $_childFieldsetNextId = 0;
	/**
	 * 2015-12-12
	 * @used-by \Df\Framework\Form\Element\Fieldset::addElement()
	 * @used-by \Df\Framework\Form\Element\Fieldset::top()
	 * @var Fieldset|null
	 */
	private $_parent;
	/**
	 * 2015-12-12
	 * Флаг анонимности филдсета.
	 * Анонимные филдсеты не добавляют своё имя в качестве префикса имён полей.
	 * @used-by \Df\Framework\Form\Element\Fieldset::fieldsetInline()
	 * @var bool
	 */
	private $_anonymous;

	/**
	 * 2016-08-10
	 * @used-by \Df\Framework\Form\Element\Fieldset::field()
	 * @used-by \Df\Framework\Form\Element\Select2::onFormInitialized()
	 * @param string $name
	 * @return string
	 */
	static function customCssClassByShortName($name) {return 'df-name-' . $name;}

	/**
	 * 2016-07-30
	 * Синтаксис вызова таков: self::fdCssClass($data, 'df-fe-money');
	 * В настоящее время нигде не используется.
	 * @param array(string => mixed) $data
	 * @param string $class
	 */
	private static function fdCssClass(&$data, $class) {
		$data[self::$FD__CSS_CLASS] = df_cc_s(dfa($data, self::$FD__CSS_CLASS), $class);
	}

	/**
	 * 2016-07-30
	 * @used-by \Df\Framework\Form\Element\Fieldset::fdCssClass()
	 * @used-by \Df\Framework\Form\Element\Fieldset::field()
	 * @var string
	 */
	private static $FD__CSS_CLASS = 'df-css-class';
}