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
	abstract protected function template();

	/**
	 * @used-by http://code.dmitry-fedyuk.com/m2/all/blob/bd880f02faddcd8a0c2067164137fe2ac9023824/Config/view/adminhtml/templates/dynamicTable/column/select.phtml#L12
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
	 * @used-by ColumnBlock::getInputName()
	 * @return mixed
	 */
	public function name() {return $this[self::$P__NAME];}

	/**
	 * Этот метод вызывается ровно один раз:
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/view/adminhtml/templates/system/config/form/field/array.phtml#L54
	 * @used-by \Df\Config\DynamicTable::renderCellTemplate()
	 * @param AbstractElement $element
	 * @return string
	 */
	public function renderTemplate(AbstractElement $element) {
		$this->_element = $element;
		return df_ejs(df_block_r($this, [], $this->template()));
	}

	/**
	 * @used-by \Df\Config\DynamicTable\Column\SelectBlock::renderHtml()
	 * @return array(string => string)
	 */
	protected function attributes() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => string) $attributes */
			$attributes = $this->cfg(self::$P__ATTRIBUTES, []);
			$this->{__METHOD__} = [
				'name' => $this->inputName()
				,'class' => implode(' ', array_filter([
					/**
					 * Этот класс затем используется в шаблоне.
					 * http://code.dmitry-fedyuk.com/m2/all/blob/ce205a2241ec6f7596c9068354390b8dae9195ab/Config/view/adminhtml/templates/dynamicTable/column/select.phtml#L10
						var $select = $('.<?php echo $columnName; ?>', $row);
					 */
					$this->name()
					, df_a($attributes, 'class')
					, str_replace('\\', '_', get_class($this))
				]))
			] + $attributes;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Этот метод предназначен для перекрытия потомками.
	 * @see \Df\Config\DynamicTable\Column\Select::jsConfigDefault()
	 * @used-by options()
	 * @return array(string => mixed)
	 */
	protected function jsConfigDefault() {return [];}

	/**
	 * @used-by htmlAttributes()
	 * @return string
	 */
	private function inputName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = strtr('{elementName}[#{_id}][{columnName}]', [
				'{elementName}' => $this->_element->getName()
				,'{columnName}' => $this->name()
			]);
		}
		return $this->{__METHOD__};
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
			->_prop(self::$P__ATTRIBUTES, RM_V_ARRAY, false)
			->_prop(self::$P__JS_CONFIG, RM_V_ARRAY, false)
			->_prop(self::$P__LABEL, RM_V_STRING_NE)
			->_prop(self::$P__NAME, RM_V_STRING_NE)
		;
	}
	/**
	 * 2015-12-14
	 * @used-by \Df\Config\DynamicTable\Column::renderTemplate()
	 * @var AbstractElement $element
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