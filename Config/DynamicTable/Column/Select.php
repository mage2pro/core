<?php
namespace Df\Config\DynamicTable\Column;
use Df\Config\DynamicTable\Column;
class Select extends Column {
	/**
	 * @used-by SelectBlock::renderHtml()
	 * @return array(array(string => string))
	 */
	public function getOptions() {return $this[self::$P__OPTIONS];}

	/**
	 * @override
	 * @see \Df\Config\DynamicTable\Column::blockClass()
	 * @return string
	 */
	protected function blockClass() {return SelectBlock::class;}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__OPTIONS, RM_V_ARRAY);
	}
	/** @var string */
	private static $P__OPTIONS = 'options';
	/**
	 * @param string $name
	 * @param string $label
	 * @param array(array(string => string)) $options
	 * @param array(string => string) $htmlAttributes [optional]
	 * @param array(string => string) $jsConfig [optional]
	 * @return Select
	 */
	public static function i(
		$name, $label, array $options, array $htmlAttributes = [], array $jsConfig = []
	) {
		df_param_string_not_empty($name, 0);
		df_param_string_not_empty($label, 1);
		return new self([
			self::$P__NAME => $name
			, self::$P__LABEL => $label
			, self::$P__OPTIONS => $options
			, self::$P__HTML_ATTRIBUTES => $htmlAttributes
			, self::$P__JS_CONFIG => $jsConfig
		]);
	}
}