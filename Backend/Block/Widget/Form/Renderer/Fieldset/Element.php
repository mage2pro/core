<?php
namespace Df\Backend\Block\Widget\Form\Renderer\Fieldset;
use Df\Framework\Form\Element as E;
use Magento\Framework\Data\Form\Element\AbstractElement as AE;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
/**
 * 2015-11-22
 * @used-by \Df\Framework\Form\Element\Fieldset::addField()
 * Этот класс я разработал на основе класса
 * @see \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Backend/Block/Widget/Form.php
 * и его шаблона https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Backend/view/adminhtml/templates/widget/form/renderer/fieldset/element.phtml
 * https://mage2.pro/t/239
 *
 * В тоже время мой класс не наследуется от взятого за основу.
 * Мой класс:
 * 1) Повторяет логику взятого за основу.
 * 2) Добавляет требуемое мне особое поведение.
 * Пока единственное, что я добавил: я добавляю к контейнеру элемента те классы CSS,
 * которые я вручную назначил элементу.
 * Вот, например, @see \Df\Framework\Form\Element\Fieldset::checkbox():
 *		protected function checkbox($name, $label, $value = null) {
 *			return $this->field($name, 'checkbox', $label, $value, ['checked' => $value])
 *				# Ядро никакого специфического для checkbox класса не добавляет
 *				->addClass('df-checkbox')
 *			;
 *		}
 * Всем своим чекбоксам я добавляю класс .df-checkbox,
 * и мне очень удобно, чтобы этот же класс был у контейнера.
 * А ещё круче: задавать элементу при необходимости тот класс, который должен быть у контейнера.
 *
 * Также вот смотрите здесь: @see \Df\Framework\Form\Element\Fieldset\Font::onFormInitialized():
 *		$this->select('letter_case', 'Letter Case', \Df\Config\Source\LetterCase::s())
 *			->addClass('df-letter-case')
 *		;
 * Здесь я задаю индивидуальный класс .df-letter-case тому выпадающему списку,
 * который предназначен именно для высоты букв, и хочу, чтобы этот же класс был у контейнера,
 * чтобы, например, можно было задавать индивидуальную ширину конкретно этому контейнеру.
 *
 * У меня есть подозрение, что в ядре такое поведение задумывалось, и что там присуствует дефект.
 * Вот смотрите, в стандартном шаблоне есть вызов магического метода $element->getCssClass():
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Backend/view/adminhtml/templates/widget/form/renderer/fieldset/element.phtml#L17
 * $fieldClass = "admin__field field field-{$element->getId()} {$element->getCssClass()}";
 * Однако у элемента класс CSS находится в чуть другом свойстве:
 * @see \Magento\Framework\Data\Form\Element\AbstractElement::addClass():
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/AbstractElement.php#L249-L260
 *		function addClass($class)
 *		{
 *			$oldClass = $this->getClass();
 *			$this->setClass($oldClass . ' ' . $class);
 *			return $this;
 *		}
 * Т.е. на самом деле надо вызывать $element->getClass(), а не $element->getCssClass()
 * https://mage2.pro/t/245
 *
 * 2015-11-22
 * Шаблон https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Backend/view/adminhtml/templates/widget/form/renderer/fieldset/element.phtml#L26
 * поддерживает тут ещё $e->getFieldExtraAttributes():
 * . ($element->getFieldExtraAttributes() ? ' ' . $element->getFieldExtraAttributes() : '')
 * Однако эта возможность используется в коде лишь в двух местах одного класса:
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/ConfigurableProduct/Block/Adminhtml/Product/Edit/AttributeSet/Form.php#L87
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/ConfigurableProduct/Block/Adminhtml/Product/Edit/AttributeSet/Form.php#L110
 * и не будет использоваться мной, поэтому я для своего рендерера её убрал.
 */
class Element extends \Df\Core\O implements RendererInterface {
	/**
	 * 2015-11-22
	 * @override
	 * @see \Magento\Framework\Data\Form\Element\Renderer\RendererInterface::render()
	 */
	function render(AE $element):string {return (new self([self::$P__E => $element]))->_render();}

	/**
	 * 2015-11-22
	 * @used-by self::render()
	 */
	private function _render():string {return $this->e()->getNoDisplay() ? '' : (
		'hidden' === $this->e()->getType()
		? $this->elementHtml()
		: df_tag('div', ['class' => $this->outerCssClasses()], $this->inner())
	);}

	/** @return AE|E */
	private function e() {return $this[self::$P__E];}

	/**
	 * 2015-11-22
	 * @used-by self::_render()
	 */
	private function elementHtml():string {return dfc($this, function() {
		/**
		 * 2015-12-11
		 * Класс .df-label-sibling означает: элемент рядом с label.
		 * Инлайновым элементам я тоже добавляю класс .df-label-sibling:
		 * @see \Df\Framework\Form\Element\Renderer\Inline::render()
		 */
		$this->e()->addClass('df-label-sibling');
		/**
		 * 2015-12-28
		 * К сожалению, мы не можем назначать классы для label:
		 * @uses \Magento\Framework\Data\Form\Element\AbstractElement::getLabelHtml()
		 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/AbstractElement.php#L425
		 * Потому ситуацию, когда label расположена справа от элемента,
		 * помечаем классом для элемента.
		 * При этом сама label справа может быть выбрана селектором .df-label-sibling ~ label
		 */
		if ($this->shouldLabelBeAtRight()) {
			$this->e()->addClass('df-label-at-right');
		}
		return $this->e()->getElementHtml();
	});}

	/**
	 * 2015-11-22
	 * @used-by self::_render()
	 */
	private function inner():string {return $this->innerRow($this->inner1()) . $this->innerRow($this->note());}

	/**
	 * 2015-11-22
	 * @used-by self::_render()
	 */
	private function inner1():string {
		$e = $this->e(); /** @var AE|E $e */
		$resultA = [$e->getLabelHtml(), $this->elementHtml()]; /** @var string[] $resultA */
		if ($this->shouldLabelBeAtRight()) {
			$resultA = array_reverse($resultA);
		}
		if ($e->getScopeLabel()) {
			$resultA[]= df_tag('div', ['class' => 'field-service', 'value-scope' => $e->getScopeLabel()]);
		}
		return implode($resultA);
	}

	/**
	 * 2015-12-28
	 */
	private function innerRow(string $s):string {return !$s ? '' : df_tag('div', 'df-element-row', $s);}

	/**
	 * 2015-11-22
	 * @used-by self::_render()
	 */
	private function note():string {return dfc($this, function() {return
		!($n = $this->e()->getNote()) ? '' : df_tag('p', 'note', df_tag('span', [], $n))
	;});}

	/**
	 * 2015-11-22
	 * @used-by self::_render()
	 */
	private function outerCssClasses():string {
		$ra = [
			'admin__field field'
			/**
			 * 2015-12-11
			 * Тонкий момент.
			 * Я пришёл к выводу, что не могу опираться на селектор .field,
			 * потому что этот селектор отсутствует у полей внутри inline fieldset,
			 * а у полей внутри inline fieldset я этот селектор убрал намеренно,
			 * потому что это селектор слишком уж заточек ядром под блочные поля,
			 * и для нормальной работы в инлайновом режиме
			 * слишком много правил CSS пришлось бы переопределять.
			 * @see \Df\Framework\Form\Element\Renderer\Inline::render()
			 * https://github.com/mage2pro/core/tree/489029cab0b8be03e4a79f0d33ce9afcdec6a76c/Framework/Data/Form/Element/Renderer/Inline.php#L50
			 */
			,'df-field'
			# 2015-11-23
			# Намеренно удалил класс "field-{$this->e()->getId()}",
			# ибо он только мусорит и мной не используется.
			, $this->e()->getCssClass()
			,E::getClassDfOnly($this->e()) # 2015-11-23 Моё добавление.
			, $this->e()->getContainerClass() # 2015-11-23 Моё добавление.
			# 2015-11-26
			# Моё добавление.
			# Все контейнеры выпадающих списков будут иметь, например, класс «df-type-select»:
			# https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/Select.php#L30
			, 'df-type-' . $this->e()->getType()
			, $this->shouldLabelBeAtRight() ? 'choice' : ''
			, $this->note() ? 'with-note' : ''
			, !$this->e()->getLabelHtml() ? 'no-label' : ''
		]; /** @var string[] $ra */
		if ($this->e()->getRequired()) {
			$ra[]= 'required';
			$ra[]= '_required';
		}
		return df_cc_s($ra);
	}

	/**
	 * 2015-11-22
	 * @used-by self::_render()
	 * @used-by self::elementHtml()
	 * @used-by self::inner1()
	 */
	private function shouldLabelBeAtRight():bool {return dfc($this, function() {return E::shouldLabelBeAtRight($this->e());});}

	/** @var string */
	private static $P__E = 'element';

	/**
	 * 2020-10-31
	 * @used-by \Df\Framework\Form\Element\Fieldset::addField()
	 */
	static function s():self {static $r; return $r ? $r : $r = new self;}
}