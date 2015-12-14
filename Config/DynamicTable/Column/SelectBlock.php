<?php
namespace Df\Config\DynamicTable\Column;
use Df\Config\DynamicTable\ColumnBlock;
/** @method Select column() */
class SelectBlock extends ColumnBlock {
	/**
	 * Используем наш шаблон @see df/admin/column/select.phtml
	 * только для формирования JavaScript (код инициализации плагина jQuery Select2),
	 * а разметку HTML формируем в методе @see renderHtml()
	 * Родительский метод: @see Mage_Core_Block_Template::renderView()
	 * @override
	 * @return string
	 */
	public function renderView() {return $this->renderHtml() . parent::renderView();}

	/**
	 * @used-by getAttributes()
	 * @override
	 * @return string
	 */
	protected function getAdditionalCssClass() {return 'rm-select';}

	/**
	 * @override
	 * @see \Df\Config\DynamicTable\ColumnBlock::jsConfigDefault()
	 * @used-by \Df\Config\DynamicTable\ColumnBlock::jsConfig()
	 * @return array(string => mixed)
	 */
	protected function jsConfigDefault() {return ['width' => 150];}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/admin/column/select.phtml';}

	/** @return string */
	private function renderHtml() {
		return df_html_select($this->column()->getOptions(), null, $this->htmlAttributes());
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__COLUMN, Select::class);
	}
}