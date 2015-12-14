<?php
namespace Df\Config\DynamicTable;
use Magento\Framework\Data\Form\Element\AbstractElement;
/**
 * 2015-02-13
 * Системные пользователи:
 * @used-by Df_Admin_Block_Field_DynamicTable::addColumn()
 * @used-by Df_Admin_Block_Field_DynamicTable::_renderCellTemplate()
 * Прикладные пользователи:
 * @used-by Df_1C_Config_Block_MapFromCustomerGroupToPriceType::_construct()
 * @used-by Df_1C_Config_Block_NonStandardCurrencyCodes::_construct()
 * @used-by Df_Directory_Block_Field_CountriesOrdered::_construct()
 */
abstract class Column extends \Df\Core\O {
	/**
	 * @used-by \Df\Config\DynamicTable\Column::renderTemplate()
	 * @return string
	 */
	abstract protected function blockClass();

	/**
	 * @used-by ColumnBlock::htmlAttributes()
	 * @return array(string => string)
	 */
	public function htmlAttributes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				['name' => $this->name()] + $this->cfg(self::$P__HTML_ATTRIBUTES, []);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by \Df\Config\DynamicTable\ColumnBlock::jsConfig()
	 * @return array(string => mixed)
	 */
	public function jsConfig() {return $this->cfg(self::$P__JS_CONFIG, []);}

	/**
	 * @used-by ColumnBlock::getInputName()
	 * @return mixed
	 */
	public function name() {return $this[self::$P__NAME];}

	/**
	 * @used-by Df_Admin_Block_Field_DynamicTable::_renderCellTemplate()
	 * @param AbstractElement $element
	 * @return string
	 */
	public function renderTemplate(AbstractElement $element) {
		return df_ejs(ColumnBlock::render($this->blockClass(), $this, $element));
	}

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
		 * @see \Df\Config\DynamicTable::addColumnRm()
		 */
		$this
			->_prop(self::$P__HTML_ATTRIBUTES, RM_V_ARRAY, false)
			->_prop(self::$P__JS_CONFIG, RM_V_ARRAY, false)
			->_prop(self::$P__LABEL, RM_V_STRING_NE)
			->_prop(self::$P__NAME, RM_V_STRING_NE)
		;
	}
	/** @var string */
	protected static $P__HTML_ATTRIBUTES = 'html_attributes';
	/** @var string */
	protected static $P__LABEL = 'label';
	/** @var string */
	protected static $P__NAME = 'string';
	/** @var string */
	protected static $P__JS_CONFIG = 'js_config';
}