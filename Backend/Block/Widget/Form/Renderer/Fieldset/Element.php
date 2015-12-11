<?php
namespace Df\Backend\Block\Widget\Form\Renderer\Fieldset;
use Df\Framework\Data\Form\Element as DfElement;
use Df\Framework\Data\Form\ElementI;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
/**
 * 2015-11-22
 * @used-by \Df\Framework\Data\Form\Element\Fieldset::addField()
 *
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
 * Вот, например, @see \Df\Framework\Data\Form\Element\Fieldset::checkbox():
		protected function checkbox($name, $label, $value = null) {
			return $this->field($name, 'checkbox', $label, $value, ['checked' => $value])
				// Ядро никакого специфического для checkbox класса не добавляет
				->addClass('df-checkbox')
			;
		}
 * Всем своим чекбоксам я добавляю класс .df-checkbox,
 * и мне очень удобно, чтобы этот же класс был у контейнера.
 * А ещё круче: задавать элементу при необходимости тот класс, который должен быть у контейнера.
 *
 * Также вот смотрите здесь: @see \Df\Framework\Data\Form\Element\Fieldset\Font::onFormInitialized():
		$this->select('letter_case', 'Letter Case', \Df\Config\Source\LetterCase::s())
			->addClass('df-letter-case')
		;
 * Здесь я задаю индивидуальный класс .df-letter-case тому выпадающему списку,
 * который предназначен именно для высоты букв, и хочу, чтобы этот же класс был у контейнера,
 * чтобы, например, можно было задавать индивидуальную ширину конкретно этому контейнеру.
 *
 * У меня есть подозрение, что в ядре такое поведение задумывалось,
 * и что там присуствует дефект.
 * Вот смотрите, в стандартном шаблоне есть вызов магического метода $element->getCssClass():
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Backend/view/adminhtml/templates/widget/form/renderer/fieldset/element.phtml#L17
 * $fieldClass = "admin__field field field-{$element->getId()} {$element->getCssClass()}";
 * Однако у элемента класс CSS находится в чуть другом свойстве:
 * @see \Magento\Framework\Data\Form\Element\AbstractElement::addClass():
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/AbstractElement.php#L249-L260
		public function addClass($class)
		{
			$oldClass = $this->getClass();
			$this->setClass($oldClass . ' ' . $class);
			return $this;
		}
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
	 * @param AbstractElement $element
	 * @return string
	 */
	public function render(AbstractElement $element) {
		/** @var \Df\Backend\Block\Widget\Form\Renderer\Fieldset\Element $i */
		$i = new self([self::$P__E => $element]);
		return $i->_render();
	}

	/**
	 * 2015-11-22
	 * @used-by \Df\Backend\Block\Widget\Form\Renderer\Fieldset\Element::render()
	 * @return string
	 */
	private function _render() {
		return
			$this->e()->getNoDisplay()
			? ''
			: (
				'hidden' === $this->e()->getType()
				? $this->elementHtml()
				:  df_tag('div', $this->outerAttributes(), $this->inner())
			)
		;
	}

	/** @return \Magento\Framework\Data\Form\Element\AbstractElement|\Df\Framework\Data\Form\Element */
	private function e() {return $this[self::$P__E];}

	/**
	 * 2015-11-22
	 * @return string
	 */
	private function elementHtml() {
		if (!isset($this->{__METHOD__})) {
			/** @var \Magento\Framework\Data\Form\Element\AbstractElement|\Df\Framework\Data\Form\Element $e */
			$e = $this->e();
			/** @var string $result */
			$result = $e->getElementHtml();
			if ('hidden' !== $e->getType() && !$this->shouldLabelBePlacedAfterElement()) {
				$result = df_tag('div', ['class' => 'admin__field-control control'],
					$result . $this->note()
				);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-11-22
	 * @used-by \Df\Backend\Block\Widget\Form\Renderer\Fieldset\Element::_render()
	 * @return string
	 */
	private function inner() {
		/** @var \Magento\Framework\Data\Form\Element\AbstractElement|\Df\Framework\Data\Form\Element $e */
		$e = $this->e();
		/** @var string[] $resultA */
		$resultA = [$e->getLabelHtml(), $this->elementHtml()];
		if ($this->shouldLabelBePlacedAfterElement()) {
			$resultA = array_reverse($resultA);
		}
		if ($e->getScopeLabel()) {
			$resultA[]= df_tag('div', ['class' => 'field-service', 'value-scope' => $e->getScopeLabel()]);
		}
		return implode($resultA);
	}

	/**
	 * 2015-11-22
	 * @used-by \Df\Backend\Block\Widget\Form\Renderer\Fieldset\Element::_render()
	 * @return string
	 */
	private function note() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $noteS */
			$noteS = $this->e()->getNote();
			$this->{__METHOD__} =
				$noteS
				? df_tag('div', ['class' => 'note', 'id' => $this->e()->getId() . '-note'], $noteS)
				: ''
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-11-23
	 * @used-by \Df\Backend\Block\Widget\Form\Renderer\Fieldset\Element::_render()
	 * @return array(string => string)
	 */
	private function outerAttributes() {
		return ['id' => $this->e()->getHtmlContainerId(), 'class' => $this->outerCssClasses()];
	}

	/**
	 * 2015-11-22
	 * @used-by \Df\Backend\Block\Widget\Form\Renderer\Fieldset\Element::_render()
	 * @return string
	 */
	private function outerCssClasses() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $resultA */
			$resultA = [
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
				 * @see \Df\Framework\Data\Form\Element\Renderer\Inline::render()
				 */
				,'df-field'
				// 2015-11-23
				// Намеренно удалил класс "field-{$this->e()->getId()}",
				// ибо он только мусорит и мной не используется.
				, $this->e()->getCssClass()
				// 2015-11-23
				// $this->e()->getClass() — моё добавление
				, $this->e()->getClass()
				// 2015-11-23
				// Моё добавление.
				, $this->e()->getContainerClass()
				// 2015-11-26
				// Моё добавление.
				// Все контейнеры выпадающих списков будут иметь, например, класс «df-type-select»:
				// https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/Element/Select.php#L30
				, 'df-type-' . $this->e()->getType()
				, $this->shouldLabelBePlacedAfterElement() ? 'choice' : ''
				, $this->note() ? 'with-note' : ''
				, !$this->e()->getLabelHtml() ? 'no-label' : ''
			];
			if ($this->e()->getRequired()) {
				$resultA[]= 'required';
				$resultA[]= '_required';
			}
			$this->{__METHOD__} = df_concat_clean(' ', $resultA);
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-11-22
	 * @used-by \Df\Backend\Block\Widget\Form\Renderer\Fieldset\Element::_render()
	 * @return bool
	 */
	private function shouldLabelBePlacedAfterElement() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = DfElement::shouldLabelBePlacedAfterElement($this->e());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-11-22
	 * @override
	 * @see \Df\Core\O::_construct()
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__E, 'Magento\Framework\Data\Form\Element\AbstractElement');
	}

	/** @var string */
	private static $P__E = 'element';

	/** @return \Df\Backend\Block\Widget\Form\Renderer\Fieldset\Element */
	public static function s() {static $r; return $r ? $r : $r = df_o(__CLASS__);}
}

