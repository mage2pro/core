<?php
namespace Df\Config\DynamicTable;
use Magento\Framework\Data\Form\Element\AbstractElement;
abstract class ColumnBlock extends \Df\Core\O {
	/**
	 * @used-by getAttributes()
	 * @used-by getInputName()
	 * @used-by rm/default/template/df/admin/column/select.phtml
	 * @return Column
	 */
	public function column() {return $this[self::$P__COLUMN];}

	/**
	 * @used-by http://code.dmitry-fedyuk.com/m2/all/blob/bd880f02faddcd8a0c2067164137fe2ac9023824/Config/view/adminhtml/templates/dynamicTable/column/select.phtml#L12
	 * @return array(string => mixed)
	 */
	public function jsConfig() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_extend($this->jsConfigDefault(), $this->column()->jsConfig());
		}
		return $this->{__METHOD__};
	}

	/**
	 * Этот метод предназначен для перекрытия потомками.
	 * @see Df_Admin_Block_Column_Select::getAdditionalCssClass()
	 * @used-by getHtmlAttributes()
	 * @return string
	 */
	protected function getAdditionalCssClass() {return '';}

	/**
	 * Этот метод предназначен для перекрытия потомками.
	 * @see \Df\Config\DynamicTable\Column\SelectBlock::defaultOptions()
	 * @used-by options()
	 * @return array(string => mixed)
	 */
	protected function jsConfigDefault() {return [];}

	/** @return AbstractElement */
	private function getField() {return $this[self::$P__FIELD];}

	/**
	 * @used-by \Df\Config\DynamicTable\Column\SelectBlock::renderHtml()
	 * @return array(string => string)
	 */
	protected function htmlAttributes() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => string) $attributes */
			$attributes = $this->column()->htmlAttributes();
			$attributes['class'] = implode(' ', array_filter([
				/**
				 * Этот класс затем используется в шаблоне.
				 * @used-by df/admin/column/select.phtml
						var $select = $('.<?php echo $columnName; ?>', $row);
				 */
				$this->column()->name()
				, df_a($attributes, 'class')
				, $this->getAdditionalCssClass()
			]));
			$this->{__METHOD__} = ['name' => $this->getInputName()] + $attributes;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by getAttributes()
	 * @return string
	 */
	private function getInputName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = strtr('{elementName}[#{_id}][{columnName}]', array(
				'{elementName}' => $this->getField()->getName()
				,'{columnName}' => $this->column()->name()
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__COLUMN, Column::class)
			->_prop(self::$P__FIELD, AbstractElement::class)
		;
	}
	/**
	 * @used-by Df_Admin_Block_Column_Select::_construct()
	 * @var string
	 */
	protected static $P__COLUMN = 'object';
	/** @var string */
	private static $P__FIELD = 'field';

	/**
	 * @used-by \Df\Config\DynamicTable\Column::renderTemplate()
	 * @param string $class
	 * @param Column $column
	 * @param AbstractElement $field
	 * @return string
	 */
	public static function render($class, Column $column, AbstractElement $field) {
		return df_block_r($class, [self::$P__COLUMN => $column, self::$P__FIELD => $field]);
	}
}