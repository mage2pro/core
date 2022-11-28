<?php
namespace Df\Typography;
# 2015-12-16
final class Css extends \Df\Core\O {
	/**
	 * 2015-12-16
	 * @used-by \Df\Typography\Font::css()
	 * @used-by \Dfe\Frontend\Block\ProductView\Css::customCss()
	 */
	function render():string {return df_cc_n(df_map_k($this->_blocks, function($selector, array $rules) {
		/** @var string $selector */ /** @var string[] $rules */
		$rulesS = df_tab_multiline(df_cc_n($rules)); /** @var string $rulesS */
		return "{$selector} {\n{$rulesS}\n}";
	}));}

	/**
	 * 2015-12-16
	 * @used-by \Df\Typography\Font::css()
	 * @used-by \Dfe\Frontend\Block\ProductView\Css::customCss()
	 */
	function rule(string $n, string $v, string $selector = ''):void {
		if (!df_es(df_fts($v))) {
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
	static function i(string $prefix = ''):string {return new self([self::$P__PREFIX => $prefix]);}
}