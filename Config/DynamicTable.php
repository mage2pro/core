<?php
namespace Df\Config;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Df\Config\DynamicTable\Column;
use Df\Framework\Data\Form\Element;
/**
 * @singleton
 * КЭШИРОВАНИЕ НАДО РЕАЛИЗОВЫВАТЬ КРАЙНЕ ОСТОРОЖНО!!!
 * Обратите внимание, что Magento не создаёт отдельные экземпляры данного класса
 * для вывода каждого поля!
 * Magento использует ЕДИНСТВЕННЫЙ экземпляр данного класса для вывода всех полей!
 * Поэтому в объектах данного класса нельзя кешировать информацию,
 * которая индивидуальна для поля конкретного поля!
 * https://mage2.pro/t/219
 *
 * Все классы, которые мы указываем в качестве «frontend_model» для интерфейсного поля,
 * в том числе и данный класс, используются как объекты-одиночки.
 * Конструируются «frontend_model» в методе
 * @used-by \Magento\Config\Block\System\Config\Form::initFields():
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Block/System/Config/Form.php#L317-L322
	$fieldRendererClass = $field->getFrontendModel();
	if ($fieldRendererClass) {
		$fieldRenderer = $this->_layout->getBlockSingleton($fieldRendererClass);
	} else {
		$fieldRenderer = $this->_fieldRenderer;
	}
 * Обратите внимание, что для конструирования используется метод
 * @uses \Magento\Framework\View\Layout::getBlockSingleton()
 * Он-то как раз и обеспечивает одиночество объектов.
 *
 * Рисование полей происходит в методе
 * @see \Magento\Config\Block\System\Config\Form\Field::_renderValue()
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Block/System/Config/Form/Field.php#L76
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Block/System/Config/Form/Field.php#L81
 * https://mage2.pro/t/222
 * $this->_getElementHtml($element);
 *
 * @method AbstractElement|Element getElement()
 */
abstract class DynamicTable extends AbstractFieldArray {
	/**
	 * 2015-02-06
	 * Этот метод используеся для колонок,
	 * требующих вместо стандартного текстового поля ввода
	 * некий другой элемент управления.
	 * Например, выпадающий список: @see \Df\Config\DynamicTable\Column\Select
	 *
	 * 2015-02-17
	 * Ядро Magento не знает, что у нас $column — не массив, а объект,
	 * и ядро Magento в одном (только одном!) месте указанного выше шаблона
	 * применяет к $column нотацию работы с массивами: $column['label'] (только для ключа «label»):
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/view/adminhtml/templates/system/config/form/field/array.phtml#L22
	 * В нашем случае это работает потому, что базовый класс @see \Magento\Framework\DataObject
	 * поддерживает такую нотацию посредством реализации интерфейса @see \ArrayAccess
	 * используется метод @uses \Magento\Framework\DataObject::offsetGet(),
	 * который аналогичен методу @see \Magento\Framework\DataObject::getData()
	 *
	 * @param Column $column
	 * @return void
	 */
	public function addColumnRm(Column $column) {$this->_columns[$column->getName()] = $column;}

	/**
	 * @override
	 * @see \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray::renderCellTemplate()
	 * @used-by https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/view/adminhtml/templates/system/config/form/field/array.phtml#L54
	 * Обратите внимание, что в строке, которую возвращает данный метод,
	 * одиночные кавычки должны быть экранированы,
	 * потому что данный метод вызывается в виде php-вставки в программный код JavaScript
	 * следующим образом:
		+'<td>'
			+'<?php echo $this->renderCellTemplate($columnName)?>'
		+'<\/td>'
	 * Как мы видим, результат данного метода тупо обрамляется одинарными кавычками
	 * без их экранирования и затем интерпретируется как строка в программном коде на JavaScript.
	 * @param string $columnName
	 * @return string
	*/
	public function renderCellTemplate($columnName) {
		/** @var Column|array(string=>mixed) $column */
		$column = $this->_columns[$columnName];
		return
			$column instanceof Column
			? $column->renderTemplate($this->getElement())
			: parent::renderCellTemplate($columnName)
		;
	}

	/**
	 *
	 * Обёртываем результат родительского метода
	 * @uses \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray::_getElementHtml()
	 * тегом «div» с идентификатором $element->getHtmlId()
	 * Назначение конкретно этому элементу конкретно этого идентификатора
	 * необходимо для корректного скрытия/показа элемента посредством JavaScript
	 * при зависимости его от значений других элементов.
	 * Такая зависимость назначается директивой <depends> в файле etc/system.xml модуля, например:
			<depends><popular__enable>1</popular__enable></depends>
	 * Странно, что родительский метод этого не делает: видимо, в родительском методе дефект.
	 * Используется это идентификатор в методе JavaScript
	 * @see FormElementDependenceController::trackChange():
		if (!$(idTo)) {
			return;
		}
	 *
	 * @used-by \Magento\Config\Block\System\Config\Form\Field::_renderValue()
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Block/System/Config/Form/Field.php#L76
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Block/System/Config/Form/Field.php#L81
	 * @override
	 * @see \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray::_getElementHtml()
	 * @param AbstractElement $element
	 * @return string
	 */
	protected function _getElementHtml(AbstractElement $element) {
		return df_tag('div'
			/**
			 * 2015-04-18
			 * Класс CSS используется для задания ширины таблицы и колонок:
			 * @see skin/adminhtml/rm/default/df/css/source/forms/_grid.scss
			 */
			, ['id' => $element->getHtmlId(), 'class' => get_class($this)]
			, parent::_getElementHtml($element)
		);
	}
}