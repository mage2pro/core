<?php
namespace Df\Core\Html;
final class Tag {
	/**
	 * 2022-11-21
	 * @used-by df_tag()
	 * @param array(string => string) $attrs [optional]
	 * @param string|string[] $content [optional]
	 * @param bool|null $multiline [optional]
	 */	
	function __construct(string $tag, array $attrs = [], $content = '', $multiline = null) {
		$this->_tag = strtolower($tag);
		/**
		 * 2023-07-20
		 * 1) «str_replace(): Passing null to parameter #3 ($subject) of type array|string is deprecated
		 * in mage2pro/core/Core/Format/Html/Tag.php on line 57»: https://github.com/mage2pro/core/issues/234
		 * 2) $attrs can contain `null` values e.g.:
		 *	{
		 *		"autocomplete": "new-password",
		 *		"checked": null,
		 *		"class": " df-checkbox",
		 *		"data-action": null,
		 *		"data-form-part": null,
		 *		"data-role": null,
		 *		"data-ui-id": "checkbox-groups-common-fields-test-value",
		 *		"disabled": false,
		 *		"id": "df_amazon_common_test",
		 *		"name": "groups[common][fields][test][value]",
		 *		"onchange": null,
		 *		"onclick": null,
		 *		"style": null,
		 *		"tabindex": null,
		 *		"title": null,
		 *		"type": "checkbox"
		 *	}
		 * 3) I use @see df_clean_r() to remove garbage values like `[null]`.
		 * It allows me to simplify @see self::openTagWithAttributesAsText()
		 */
		$this->_attrs = df_clean_r($attrs, [false]);
		$this->_content = $content;	
		$this->_multiline = !is_null($multiline) ? $multiline : 1 < count($attrs);
	}
	
	/** @used-by df_tag() */
	function render():string {return
		"<{$this->openTagWithAttributesAsText()}"
		. ($this->_multiline ? "\n" : '')
		. (!$this->content() && $this->shortTagAllowed() ? '/>' : ">{$this->content()}</{$this->_tag}>")
	;}

	/** @used-by self::render() */
	private function content():string {return dfc($this, function() {
		$c = df_trim(df_cc_n($this->_content), "\n"); /** @var string $c */
		return $this->tagIs('pre', 'code') || !df_is_multiline($c) ? $c : "\n" . df_tab_multiline($c) . "\n";
	});}
	
	/** @used-by self::render() */
	private function openTagWithAttributesAsText():string {return df_cc_s(
		$this->_tag
		,$this->_multiline ? "\n" : null
		,call_user_func(
			$this->_multiline ? 'df_tab_multiline' : 'df_nop'
			,implode(
				$this->_multiline ? "\n" :  ' '
				/**
				 * 2023-07-20
				 * I removed @see df_clean()
				 * because @see self::$_attrs can not contaon garbage values anymore
				 * because I call `df_clean_r($attrs, [false])` in @see self::__construct()
				 */
				,df_map_k(
					/** 2022-11-21 @param string|string[] $v */
					function(string $k, $v):string {
						df_param_sne($k, 0);
						/**
						 * 2023-07-20
						 * $v can not be a garbage anymore
						 * because I call `df_clean_r($attrs, [false])` in @see self::__construct()
						 */
						df_assert($v);
						/**
						 * 2015-04-16 Передавать в качестве $v массив имеет смысл, например, для атрибута «class».
						 * 2016-11-29
						 * Не использую @see df_e(), чтобы сохранить двойные кавычки (data-mage-init)
						 * и в то же время сконвертировать одинарные
						 * (потому что значения атрибутов мы ниже обрамляем именно одинарными).
						 * 2017-09-11
						 * Today I have noticed that `&apos;` does not work for me
						 * on the Magento 2 backend configuration pages:
						 * @see \Df\Payment\Comment\Description::a()
						 * So I switched to the `&#39;` solution.
						 * «How do I escape a single quote?» https://stackoverflow.com/a/2428595
						 */
						$v = htmlspecialchars(
							str_replace("'", '&#39;', !is_array($v) ? $v : df_cc_s($v)), ENT_NOQUOTES, 'UTF-8', false
						);
						/**
						 * 2023-07-20
						 * The previous code was:
						 * 		return df_es($v) ? $v : "{$k}='{$v}'";
						 * $v can not be an empty string anymore
						 * because I call `df_clean_r($attrs, [false])` in @see self::__construct()
						 */
						return "{$k}='{$v}'";
					}, $this->_attrs
				)
			)
		)
	);}

	/**
	 * 2018-03-11
	 * Self-closing `span` tags sometimes work incorrectly,
	 * I have encountered it today while working on the frugue.com website.
	 * https://stackoverflow.com/questions/2816833
	 * @used-by self::render()
	 */
	private function shortTagAllowed():bool {return !$this->tagIs('div', 'script', 'span');}

	/**
	 * 2016-08-05
	 * @used-by self::content()
	 * @used-by self::shortTagAllowed()
	 */
	private function tagIs(string ...$tags):bool {return in_array($this->_tag, $tags);}
	
	/**
	 * 2022-11-21
	 * @used-by self::__construct()
	 * @used-by self::openTagWithAttributesAsText()
	 * @var array(string => string)
	 */
	private $_attrs;	
	
	/**
	 * 2022-11-21
	 * @used-by self::__construct()
	 * @used-by self::content()
	 * @var string|null|string[]
	 */
	private $_content;	
	
	/**
	 * 2022-11-21
	 * @used-by self::__construct()
	 * @used-by self::openTagWithAttributesAsText()
	 * @used-by self::render()
	 * @var bool|null
	 */
	private $_multiline;		
	
	/**
	 * 2022-11-21
	 * @used-by self::__construct()
	 * @used-by self::render()
	 * @used-by self::openTagWithAttributesAsText()
	 * @used-by self::tagIs()
	 * @var string
	 */
	private $_tag;	
}