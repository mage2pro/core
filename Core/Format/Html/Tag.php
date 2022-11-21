<?php
namespace Df\Core\Format\Html;
final class Tag extends \Df\Core\O {
	/** @used-by self::render() */
	private function _render():string {return
		"<{$this->openTagWithAttributesAsText()}"
		. ($this->shouldAttributesBeMultiline() ? "\n" : '')
		. (!$this->content() && $this->shortTagAllowed() ? '/>' : ">{$this->content()}</{$this->tag()}>")
	;}
	
	/**
	 * @used-by openTagWithAttributesAsText()
	 * @used-by shouldAttributesBeMultiline()
	 */
	private function attributes():array {return $this->a(self::$P__ATTRIBUTES, []);}
	
	/** @used-by self::_render() */
	private function content():string {return dfc($this, function() {
		$c = df_trim(df_cc_n($this[self::$P__CONTENT]), "\n"); /** @var string $c */
		return $this->tagIs('pre', 'code') || !df_contains($c, "\n") ? $c :
			"\n" . df_tab_multiline($c) . "\n"
		;
	});}
	
	/** @used-by self::_render() */
	private function openTagWithAttributesAsText():string {return df_cc_s(
		$this->tag()
		,$this->shouldAttributesBeMultiline() ? "\n" : null
		,call_user_func(
			$this->shouldAttributesBeMultiline() ? 'df_tab_multiline' : 'df_nop'
			,implode(
				$this->shouldAttributesBeMultiline() ? "\n" :  ' '
				,df_clean(df_map_k(
					/** 2022-11-21 @param string|string[] $v */
					function(string $k, $v):string {
						df_param_sne($k, 0);
						/**
						 * 2015-04-16 Передавать в качестве $value массив имеет смысл, например, для атрибута «class».
						 * 2016-11-29
						 * Не использую @see df_e(), чтобы сохранить двойные кавычки (data-mage-init)
						 * и в то же время сконвертировать одинарные
						 * (потому что значения атрибутов мы ниже обрамляем именно одинарными).
						 * 2017-09-11
						 * Today I have notices that `&apos;` does not work for me
						 * on the Magento 2 backend configuration pages:
						 * @see \Df\Payment\Comment\Description::a()
						 * So I switched to the `&#39;` solution.
						 * «How do I escape a single quote?» https://stackoverflow.com/a/2428595
						 */
						$v = htmlspecialchars(
							str_replace("'", '&#39;', !is_array($v) ? $v : df_cc_s($v))
							,ENT_NOQUOTES
							,'UTF-8'
							,false
						);
						return '' === $v ? '' : "{$k}='{$v}'";
					}, $this->attributes()
				))
			)
		)
	);}

	/**
	 * 2018-03-11
	 * Self-closing `span` tags sometimes work incorrectly,
	 * I have encountered it today while working on the frugue.com website.
	 * https://stackoverflow.com/questions/2816833
	 */
	private function shortTagAllowed():bool {return !$this->tagIs('div', 'script', 'span');}

	/**
	 * @used-by self::_render()
	 * @used-by self::openTagWithAttributesAsText()
	 */
	private function shouldAttributesBeMultiline():bool {return dfc($this, function() {/** @var bool|null $r */return
		!is_null($r = $this[self::$P__MULTILINE]) ? $r : 1 < count($this->attributes())
	;});}

	/**
	 * 2016-08-05
	 * @used-by self::_render()
	 * @used-by self::openTagWithAttributesAsText()
	 * @used-by self::tagIs()
	 */
	private function tag():string {return dfc($this, function() {return strtolower($this[self::$P__TAG]);});}

	/**
	 * 2016-08-05
	 * @used-by self::content()
	 * @used-by self::shortTagAllowed()
	 */
	private function tagIs(string ...$tags):bool {return in_array($this->tag(), $tags);}

	/** @var string */
	private static $P__ATTRIBUTES = 'attributes';
	/** @var string */
	private static $P__CONTENT = 'content';
	/** @var string */
	private static $P__MULTILINE = 'multiline';
	/** @var string */
	private static $P__TAG = 'tag';

	/**
	 * @used-by df_tag()
	 * @param string $tag
	 * @param array(string => string) $attrs [optional]
	 * @param string|null|string[] $content [optional]
	 * @param bool|null $multiline [optional]
	 */
	static function render($tag, array $attrs = [], $content = null, $multiline = null):string {return (new self([
		self::$P__ATTRIBUTES => $attrs
		,self::$P__CONTENT => $content
		,self::$P__MULTILINE => $multiline
		,self::$P__TAG => $tag
	]))->_render();}
}