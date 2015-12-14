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
	 * @return string
	 */
	protected function getRendererClass() {return SelectBlock::class;}

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
	 * @param array(string => string) $renderOptions [optional]
	 * @return Select
	 */
	public static function i(
		$name, $label, array $options, array $htmlAttributes = [], array $renderOptions = []
	) {
		df_param_string_not_empty($name, 0);
		df_param_string_not_empty($label, 1);
		return new self([
			self::$P__NAME => $name
			, self::$P__LABEL => $label
			, self::$P__OPTIONS => $options
			, self::$P__HTML_ATTRIBUTES => $htmlAttributes
			, self::$P__RENDER_OPTIONS => $renderOptions
		]);
	}
}