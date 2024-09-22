<?php
namespace Df\Core\Helper;
final class Text {
	/**
	 * @used-by df_quote_double()
	 * @used-by df_quote_russian()
	 * @used-by df_quote_single()
	 * @param string|string[]|array(string => string) $s
	 * @return string|string[]
	 */
	function quote($s, string $t = self::QUOTE__RUSSIAN) {
		if ('"' === $t) {
			$t = self::QUOTE__DOUBLE;
		}
		elseif ("'" === $t) {
			$t = self::QUOTE__SINGLE;
		}
		static $m = [
			self::QUOTE__DOUBLE => ['"', '"'], self::QUOTE__RUSSIAN => ['«', '»'], self::QUOTE__SINGLE => ["'", "'"]
		]; /** @var array(string => string[]) $m */
		$quotes = dfa($m, $t); /** @var string[] $quotes */
		if (!is_array($quotes)) {
			df_error("An unknown quote: «{$t}».");
		}
		# 2016-11-13 It injects the value $s inside quotes.
		$f = function(string $s) use($quotes):string {return implode($s, $quotes);};
		return !is_array($s) ? $f($s) : array_map($f, $s);
	}

	/**
	 * @used-by df_quote_double()
	 * @used-by self::quote()
	 */
	const QUOTE__DOUBLE = 'double';

	/**
	 * @used-by df_quote_russian()
	 * @used-by self::quote()
	 */
	const QUOTE__RUSSIAN = 'russian';

	/**
	 * @used-by df_quote_single()
	 * @used-by self::quote()
	 */
	const QUOTE__SINGLE = 'single';

	/** @used-by df_t() */
	static function s():self {static $r; return $r ? $r : $r = new self;}
}