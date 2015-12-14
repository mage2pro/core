<?php
namespace Df\Config\DynamicTable\Column;
use Df\Config\DynamicTable\ColumnBlock;
/** @method Select getColumn() */
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
	 * Этот метод предназначен для перекрытия потомками.
	 * @see Df_Admin_Block_Column_Select::getDefaultRenderOptions()
	 * @used-by getRenderOptions()
	 * @override
	 * @return array(string => mixed)
	 */
	protected function getDefaultRenderOptions() {return ['width' => 150];}

	/**
	 * @override
	 * @see Df_Core_Block_Template::defaultTemplate()
	 * @used-by Df_Core_Block_Template::getTemplate()
	 * @return string
	 */
	protected function defaultTemplate() {return 'df/admin/column/select.phtml';}

	/** @return string */
	private function renderHtml() {
		return df_html_select($this->getColumn()->getOptions(), null, $this->getHtmlAttributes());
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