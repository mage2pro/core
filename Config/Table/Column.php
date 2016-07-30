<?php
namespace Df\Config\Table;
use Magento\Framework\Data\Form\Element\AbstractElement as AE;
abstract class Column extends \Df\Core\O {
	/**
	 * @used-by \Df\Config\Table\Column::renderTemplate()
	 * @see \Df\Config\Table\Column\Select::_render()
	 * @return string
	 */
	abstract protected function _render();

	/**
	 * @used-by https://github.com/mage2pro/core/tree/bd880f02faddcd8a0c2067164137fe2ac9023824/Config/view/adminhtml/templates/dynamicTable/column/select.phtml#L12
	 * @return array(string => mixed)
	 */
	public function jsConfig() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_extend(
				$this->jsConfigDefault(), $this->cfg(self::$P__JS_CONFIG, [])
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by https://github.com/mage2pro/core/tree/65f25abceacb8a7e43a60aa8725ffc87047c0624/Config/view/adminhtml/templates/dynamicTable/column/select.phtml#L4
	 * @return mixed
	 */
	public function name() {return $this[self::$P__NAME];}

	/**
	 * Этот метод вызывается ровно один раз:
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/view/adminhtml/templates/system/config/form/field/array.phtml#L54
	 * @used-by \Df\Config\Table::renderCellTemplate()
	 * @param AE $element
	 * @return string
	 */
	public function renderTemplate(AE $element) {
		$this->_element = $element;
		return df_ejs($this->_render());
	}

	/**
	 * @used-by \Df\Config\Table\Column\Select::renderHtml()
	 * @return array(string => string)
	 */
	protected function attributes() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => string) $attributes */
			$attributes = $this->cfg(self::$P__ATTRIBUTES, []);
			$this->{__METHOD__} = [
				'name' => "{$this->_element->getName()}[<%- _id %>][{$this->name()}]"
				,'class' => implode(' ', array_filter([
					/**
					 * Этот класс затем используется в шаблоне.
					 * https://github.com/mage2pro/core/tree/ce205a2241ec6f7596c9068354390b8dae9195ab/Config/view/adminhtml/templates/dynamicTable/column/select.phtml#L10
						var $select = $('.<?php echo $columnName; ?>', $row);
					 */
					$this->name()
					, dfa($attributes, 'class')
					, str_replace('\\', '_', get_class($this))
				]))
			] + $attributes;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Этот метод предназначен для перекрытия потомками.
	 * @see \Df\Config\Table\Column\Select::jsConfigDefault()
	 * @used-by options()
	 * @return array(string => mixed)
	 */
	protected function jsConfigDefault() {return [];}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		/**
		 * 2015-02-17
		 * Обратите внимание, что у объектов данного класса
		 * свойство «label» обязательно должно быть доступно
		 * в качестве ключа массива @see _data,
		 * потому что оно используется в шаблоне
		 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/view/adminhtml/templates/system/config/form/field/array.phtml#L22
		 * посредством синтаксиса $column['label']
		 * Читайте комментарий к методу
		 * @see \Df\Config\Table::addColumnRm()
		 */
		$this
			->_prop(self::$P__ATTRIBUTES, RM_V_ARRAY, false)
			->_prop(self::$P__JS_CONFIG, RM_V_ARRAY, false)
			->_prop(self::$P__LABEL, RM_V_STRING_NE)
			->_prop(self::$P__NAME, RM_V_STRING_NE)
		;
	}
	/**
	 * 2015-12-14
	 * @used-by \Df\Config\Table\Column::renderTemplate()
	 * @var AE $element
	 */
	private $_element;

	/** @var string */
	protected static $P__ATTRIBUTES = 'attributes';
	/** @var string */
	protected static $P__LABEL = 'label';
	/** @var string */
	protected static $P__NAME = 'string';
	/** @var string */
	protected static $P__JS_CONFIG = 'js_config';
}