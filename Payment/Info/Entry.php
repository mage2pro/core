<?php
namespace Df\Payment\Info;
# 2016-08-09
final class Entry {
	/**
	 * 2016-08-09
	 * @used-by \Df\Payment\Info\Report::add()
	 */
	function __construct(string $name, string $value, int $weight = 0) {
		$this->_name = (string)__($name); $this->_value = $value; $this->_weight = $weight;
	}

	/**
	 * 2016-08-09
	 * @used-by \Df\Payment\Info\Report::get()
	 */
	function name():string {return $this->_name;}

	/**
	 * 2016-08-09
	 * @used-by \Df\Payment\Info\Report::get()
	 */
	function value():string {return $this->_value;}

	/**
	 * 2016-08-09
	 * К сожалению, мы не можем делать нецелые веса:
	 * ttp://php.net/manual/function.usort.php
	 * «Returning non-integer values from the comparison function, such as float,
	 * will result in an internal cast to integer of the callback's return value.
	 * So values such as 0.99 and 0.1 will both be cast to an integer value of 0,
	 * which will compare such values as equal.»
	 * Нецелые веса позволили бы нам гарантированно запихнуть
	 * безвесовые записи между весовыми, но увы...
	 * @used-by \Df\Payment\Info\Report::addAfter()
	 * @used-by \Df\Payment\Info\Report::sort()
	 */
	function weight():int {return $this->_weight;}

	/**
	 * 2017-03-12
	 * @used-by self::__construct()
	 * @used-by self::name()
	 * @var string
	 */
	private $_name;

	/**
	 * 2017-03-12
	 * @used-by self::__construct()
	 * @used-by self::value()
	 * @var string
	 */
	private $_value;

	/**
	 * 2017-03-12
	 * @used-by self::__construct()
	 * @used-by self::weight()
	 * @var int
	 */
	private $_weight;
}