<?php
namespace Df\Core\Text;
# 2021-12-12
final class Marker {
	/**
	 * 2021-12-12
	 * @used-by \Df\Xml\X::marker()
	 * @param string $begin
	 * @param string $end
	 */
	function __construct($begin, $end) {$this->_begin = $begin; $this->_end = $end;}

	/**
	 * 2021-12-12
	 * @used-by \Df\Xml\X::markAsCData()
	 * @param string|null $s
	 * @return string
	 */
	function mark($s) {return $this->_begin . $s . $this->_end;}

	/**
	 * 2021-12-12
	 * @used-by \Df\Xml\X::importString()
	 * @param string|null $s
	 * @return string
	 */
	function marked($s) {return df_starts_with($s, $this->_begin) && df_ends_with($s, $this->_end);}

	/**
	 * 2021-12-12
	 * @used-by \Df\Xml\X::importString()
	 * @param string $s
	 * @return string
	 */
	function unmark($s) {return df_trim_text_left_right($s, $this->_begin, $this->_end);}

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