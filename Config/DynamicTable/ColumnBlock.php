<?php
namespace Df\Config\DynamicTable;
use Magento\Framework\Data\Form\Element\AbstractElement;
abstract class ColumnBlock extends \Df\Core\O {
	/**
	 * @used-by
	 * @return array(string => mixed)
	 */
	public function getRenderOptions() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_extend(
				$this->getDefaultRenderOptions(), $this->getColumn()->getRenderOptions()
			);
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
	 * @used-by getAttributes()
	 * @used-by getInputName()
	 * @used-by rm/default/template/df/admin/column/select.phtml
	 * @return Column
	 */
	protected function getColumn() {return $this[self::$P__COLUMN];}

	/**
	 * Этот метод предназначен для перекрытия потомками.
	 * @see Df_Admin_Block_Column_Select::getDefaultRenderOptions()
	 * @used-by getRenderOptions()
	 * @return array(string => mixed)
	 */
	protected function getDefaultRenderOptions() {return [];}

	/** @return AbstractElement */
	private function getField() {return $this[self::$P__FIELD];}

	/** @return array(string => string) */
	protected function getHtmlAttributes() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => string) $attributes */
			$attributes = $this->getColumn()->getHtmlAttributes();
			$attributes['class'] = implode(' ', array_filter([
				/**
				 * Этот класс затем используется в шаблоне.
				 * @used-by df/admin/column/select.phtml
						var $select = $('.<?php echo $columnName; ?>', $row);
				 */
				$this->getColumn()->getName()
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
				,'{columnName}' => $this->getColumn()->getName()
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
	 * @param string $class
	 * @param Column $column
	 * @param AbstractElement $field
	 * @return string
	 */
	public static function render($class, Column $column, AbstractElement $field) {
		/** @var ColumnBlock $block */
		$block = new $class([self::$P__COLUMN => $column, self::$P__FIELD => $field]);
		df_assert($block instanceof ColumnBlock);
		return df_block_r($block);
	}
}