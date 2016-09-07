<?php
namespace Df\Framework\Form\Element;
use Df\Config\Source\SizeUnit;
use Df\Framework\Form\Element as E;
use Df\Framework\Form\ElementI;
use Df\Framework\Form\Element\Renderer\Inline;
use Df\Framework\Form\Element\Select2\Number as Select2Number;
use Magento\Framework\App\Config\Data as ConfigData;
use Magento\Framework\App\Config\DataInterface as IConfigData;
use Magento\Framework\Data\Form\AbstractForm;
use Magento\Framework\Data\Form\Element\AbstractElement as AE;
use Magento\Framework\Data\Form\Element\Fieldset as _Fieldset;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Phrase;
/**
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
	public function addElement(AE $element, $after = false) {
		/**
		 * 2015-12-12
		 * Экзотическая конструкция «instanceof self» вполне допустима:
		 * https://3v4l.org/nWA6U
		 */
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
	 * 2015-11-19
	 * https://mage2.pro/t/228
	 * «Propose to add a fieldset-specific element renderer»
	 * @override
	 * @param string $elementId
	 * @param string $type
	 * @param array $config
	 * @param bool|false $after
	 * @param bool|false $isAdvanced
	 * @return AE
	 */
	public function addField($elementId, $type, $config, $after = false, $isAdvanced = false) {
		/** @var AE $result */
		$result = parent::addField($elementId, $type, $config, $after, $isAdvanced);
		/** @var RendererInterface|null $renderer */
		$renderer = $this->getElementRendererDf();
		if (!$renderer && df_is_backend()) {
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
	 * @return AE[]
	 */
	public function getChildren() {return iterator_to_array($this->getElements());}

	/**
	 * 2015-11-23
	 * @return $this
	 */
	public function hide() {
		df_hide($this);
		return $this;
	}

	/**
	 * 2015-11-19
	 * @override
	 * @see \Df\Framework\Form\ElementI::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 * @return void
	 */
	public function onFormInitialized() {df_fe_init($this, __CLASS__);}

	/**
	 * 2015-12-12
	 * @used-by df_fe_top()
	 * Возвращает филдсет самого верхнего уровня.
	 * У филдсета самого верхнего уровня метод getContainer() возвращает форму.
	 * @return Fieldset
	 */
	public function top() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->isTop() ? $this : $this->_parent->top();
		}
		return $this->{__METHOD__};
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
		if ('' === $name) {
			$name = 'color';
		}
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
				> label:not(.addafter) {
					display: inline-block;
					font-family: FontAwesome;
					// http://fortawesome.github.io/Font-Awesome/icon/text-width/
					&:before {content: "\f035";}
				}
		 */
		if (!is_null($label) && '' === (string)$label) {
			$label = 'Color';
		}
		return $this->field($name, Color::class, $label, $data);
	}

	/**
	 * 2015-11-17
	 * @used-by \Df\Framework\Form\Element\ArrayT::itemType()
	 * @param string|null $key [optional]
	 * @param string|null|callable $default [optional]
	 * @return string|null|array(string => mixed)
	 */
	protected function fc($key = null, $default = null) {return df_fe_fc($this, $key, $default);}

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
			> label:not(.addafter) {
				display: inline-block;
				font-family: FontAwesome;
				// http://fortawesome.github.io/Font-Awesome/icon/text-width/
				&:before {content: "\f035";}
			}
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
		/** @var mixed $value */
		$value = $this->v($name);
		if (is_null($value)) {
			$value = dfa($data, 'value');
		}
		unset($data['value']);
		/** @var array(string => string) $params */
		$params = ['name' => $this->cn($name), 'value' => $value];
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
				> label:not(.addafter) {
					display: inline-block;
					font-family: FontAwesome;
					// http://fortawesome.github.io/Font-Awesome/icon/text-width/
					&:before {content: "\f035";}
				}
		 */
		if (!is_null($label)) {
			$params += ['label' => __($label)];
		}
		/** @var string|null $additionalClass */
		$additionalClass = dfa($data, self::$FD__CSS_CLASS);
		unset($data[self::$FD__CSS_CLASS]);
		/**
		 * 2016-07-30
		 * Здесь происходит рекурсия, потому что добавление поля к внутреннему филдсету
		 * сводится (через десяток вызовов) к добавлению этого поля к филдсету самого верхнего уровня.
		 */
		/** @var AE|E $result */
		$result = $this->addField($this->cn($name), $type, $params + $data);
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
				public function getValue() {return $this->top()->getData('value');}
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
	protected function fieldsetInline($cssClass = null) {
		return $this->fieldset(Fieldset\Inline::class, $cssClass);
	}

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
	 * @param string $name
	 * @param string|null|Phrase $label [optional]
	 * @param int|float|null $default [optional]
	 * @param array(string => mixed) $data [optional]
	 * @return Quantity|E
	 */
	protected function money($name, $label = null, $default = null, $data = []) {
		return $this->number($name, $label, $data + [
			'value' => $default, Number::LABEL_RIGHT => df_currency_base($this->scope())->getCode()
		]);
	}

	/**
	 * 2016-08-02
	 * @param string $name
	 * @param string|null|Phrase $label [optional]
	 * @param array(string => mixed) $data [optional]
	 * @return E|Number
	 */
	protected function number($name, $label = null, $data = []) {
		return $this->field($name, Number::class, $label, $data);
	}

	/**
	 * 2015-12-13
	 * @param string $name
	 * @param string|null|Phrase $label [optional]
	 * @param int|null $default [optional]
	 * @param array(string => mixed) $data [optional]
	 * @return Number|E
	 */
	protected function percent($name, $label = null, $default = null, $data = []) {
		return $this->number($name, $label, $data + ['value' => $default, Number::LABEL_RIGHT => '%']);
	}

	/**
	 * 2016-07-30
	 * @param string $name
	 * @param string|null|Phrase $label [optional]
	 * @param array(string => mixed) $data [optional]
	 * @return Quantity|E
	 */
	protected function quantity($name, $label = null, $data = []) {
		return $this->field($name, Quantity::class, $label, $data);
	}

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
			> label:not(.addafter) {
				display: inline-block;
				font-family: FontAwesome;
				// http://fortawesome.github.io/Font-Awesome/icon/text-width/
				&:before {content: "\f035";}
			}
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
	protected function select2($name, $label, $values, $data = []) {
		return $this->select($name, $label, $values, $data, Select2::class);
	}

	/**
	 * 2016-08-10
	 * @param string $name
	 * @param string|null|Phrase $label
	 * @param array(array(string => string|int))|string[]|string|OptionSourceInterface $values
	 * @param array(string => mixed)|string $data [optional]
	 * @return \Magento\Framework\Data\Form\Element\Select|E
	 */
	protected function select2Number($name, $label, $values, $data = []) {
		return $this->select($name, $label, $values, $data, Select2Number::class);
	}

	/**
	 * 2015-12-11
	 * @param string $name
	 * @param string|null|Phrase $label [optional]
	 * @param array(string => mixed) $data [optional]
	 * @return Quantity|E
	 */
	protected function size($name, $label = null, $data = []) {
		return $this->quantity($name, $label, $data + [
			Quantity::P__VALUES => SizeUnit::s()->toOptionArray()
		]);
	}

	/**
	 * 2015-12-12
	 * @param string $name
	 * @param string|null|Phrase $label [optional]
	 * @param array(string => mixed) $data [optional]
	 * @return Text|E
	 */
	protected function text($name, $label = null, $data = []) {
		return $this->field($name, Text::class, $label, $data);
	}

	/**
	 * 2015-12-07
	 * @used-by \Df\Framework\Form\Element\ArrayT::onFormInitialized()
	 * @param string|null $name [optional]
	 * @return string|null
	 */
	protected function v($name = null) {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = dfa($this->_data, 'value', []);
			/**
			 * 2016-06-29
			 * Что интересно, при смене области действия настроек с глобальной на другую (сайт или магазин)
			 * поле «value» может почему-то содержпть не массив,
			 * а строку JSON, соответствующую запакованному в JSON массиву:
			 * https://code.dmitry-fedyuk.com/m2e/currency-format/issues/1
			 * Заметил это только для модуля «Price Format».
			 */
			if (is_string($this->{__METHOD__})) {
				$this->{__METHOD__} = df_json_decode($this->{__METHOD__});
			}
		}
		return is_null($name) ? $this->{__METHOD__} : dfa($this->{__METHOD__}, $name);
	}

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
	private function isTop() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = !$this->_parent instanceof self;
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-12
	 * Для филдсета верхнего уровня:
	 * *) getName() возвращает «groups[frontend][fields][value_font][value]»
	 * *) getId() возвращает dfe_sku_frontend_value_font
	 * Для подчинённых филдсетов мы getId() равно getName()
	 * @return string
	 */
	private function nameFull() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->isTop()
				? df_trim_text_right($this->getName(), '[value]')
				// Анонимные филдсеты не добавляют своё имя в качестве префикса имён полей.
				: (!$this->_anonymous ? $this->getId() : $this->_parent->nameFull())
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-30
	 * Там же ещё есть параметр «scope», который по умолчанию равен «default»,
	 * а для магазина — «stores».
	 * @used-by \Df\Framework\Form\Element\Fieldset::money()
	 * @return ConfigData|IConfigData
	 */
	private function scope() {
		if (!isset($this->{__METHOD__})) {
			// 2016-07-30
			// По умолчанию «scope_id» равно пустой строке.
			/** @var Fieldset $t */
			$t = $this->top();
			$this->{__METHOD__} = df_scope($t['scope_id'], $t['scope']);
		}
		return $this->{__METHOD__};
	}

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
	public static function customCssClassByShortName($name) {return 'df-name-' . $name;}

	/**
	 * 2016-07-30
	 * Синтаксис вызова таков: self::fdCssClass($data, 'df-fe-money');
	 * В настоящее время нигде не используется.
	 * @param array(string => mixed) $data
	 * @param string $class
	 * @return void
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


