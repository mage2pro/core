<?php
namespace Df\Payment\Info;
use Magento\Framework\Phrase;
// 2016-08-09
final class Entry {
	/**
	 * 2016-08-09
	 * @used-by \Df\Payment\Info\Dictionary::add()
	 * @param string $name
	 * @param string|Phrase $value
	 * @param int $weight [optional]
	 */
	function __construct($name, $value, $weight = 0) {
		$this->_name = strval(__($name)); $this->_value = $value; $this->_weight = $weight;
	}

	/**
	 * 2016-08-09
	 * @used-by \Df\Payment\Info\Dictionary::get()
	 * @return string
	 */
	function name() {return strval(__($this->_name));}

	/**
	 * 2016-08-09
	 * @used-by \Df\Payment\Info\Dictionary::get()
	 * @return string|Phrase
	 */
	function value() {return $this->_value;}

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
	 * @used-by \Df\Payment\Info\Dictionary::addAfter()
	 * @used-by \Df\Payment\Info\Dictionary::sort()
	 * @return int
	 */
	function weight() {return $this->_weight;}

	/**
	 * 2017-03-12
	 * @used-by __construct()
	 * @used-by nameT()
	 * @var string
	 */
	private $_name;

	/**
	 * 2017-03-12
	 * @used-by __construct()
	 * @used-by value()
	 * @var string|Phrase
	 */
	private $_value;

	/**
	 * 2017-03-12
	 * @used-by __construct()
	 * @used-by weight()
	 * @var int
	 */
	private $_weight;
}