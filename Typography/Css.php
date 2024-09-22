<?php
namespace Df\Typography;
# 2015-12-16
final class Css extends \Df\Core\O {
	/**
	 * 2015-12-16
	 * @used-by \Df\Typography\Font::css()
	 * @used-by \Dfe\Frontend\Block\ProductView\Css::customCss()
	 */
	function render():string {return df_cc_n(df_map_k($this->_blocks, function(string $selector, array $rules) {return sprintf(
		"{$selector} {\n%s\n}", df_tab_multiline(df_cc_n($rules))
	);}));}

	/**
	 * 2015-12-16
	 * @used-by \Df\Typography\Font::css()
	 * @used-by \Dfe\Frontend\Block\ProductView\Css::customCss()
	 */
	function rule(string $n, string $v, string $selector = ''):void {
		# 2024-05-16
		# 1) https://3v4l.org/0S7Su
		# 2) https://3v4l.org/ai3sF
		if (!df_es($v)) {
			$this->_blocks[$this[self::$P__PREFIX] . $selector][]= "$n: $v !important;";
		}
	}

	/**
	 * @used-by self::render()
	 * @used-by self::rule()
	 * @var array(string => string[])
	 */
	private $_blocks = [];

	/**
	 * @used-by self::i()
	 * @used-by self::rule()
	 * @var string
	 */
	private static $P__PREFIX = 'selector';

	/**
	 * 2015-12-21
	 * @used-by \Df\Typography\Font::css()
	 * @used-by \Dfe\Frontend\Block\ProductView\Css::customCss()
	 */
	static function i(string $p = ''):self {return new self([self::$P__PREFIX => $p]);}
}