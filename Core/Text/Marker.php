<?php
namespace Df\Core\Text;
# 2021-12-12
final class Marker {
	/**
	 * 2021-12-12
	 * @used-by \Df\Xml\G::marker()
	 */
	function __construct(string $begin, string $end) {$this->_begin = $begin; $this->_end = $end;}

	/**
	 * 2021-12-12
	 * @used-by \Df\Xml\G::markAsCData()
	 */
	function mark(string $s):string {return $this->_begin . $s . $this->_end;}

	/**
	 * 2021-12-12
	 * @used-by \Df\Xml\G::importString()
	 */
	function marked(string $s):string {return df_starts_with($s, $this->_begin) && df_ends_with($s, $this->_end);}

	/**
	 * 2021-12-12
	 * @used-by \Df\Xml\G::importString()
	 */
	function unmark(string $s):string {return df_trim_text_left_right($s, $this->_begin, $this->_end);}

	/**
	 * 2021-12-12
	 * @used-by self::__construct()
	 * @used-by self::mark()
	 * @used-by self::marked()
	 * @used-by self::unmark()
	 * @var string
	 */
	private $_begin;

	/**
	 * 2021-12-12
	 * @used-by self::__construct()
	 * @used-by self::mark()
	 * @used-by self::marked()
	 * @used-by self::unmark()
	 * @var string
	 */
	private $_end;
}