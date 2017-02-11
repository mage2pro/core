<?php
namespace Df\Payment\Info;
use Magento\Framework\Phrase;
// 2016-08-09
class Entry extends \Df\Core\O {
	/** @return string */
	function name() {return $this[self::$P__NAME];}
	/** @return string */
	function nameT() {return strval(__($this->name()));}
	/** @return string|Phrase */
	function value() {return $this[self::$P__VALUE];}
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
	 * @return int
	 */
	function weight() {return $this[self::$P__WEIGHT];}

	/**
	 * 2016-08-09
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__NAME, DF_V_STRING_NE)
			->_prop(self::$P__WEIGHT, DF_V_FLOAT, false)
		;
	}
	/** @var string */
	private static $P__NAME = 'name';
	/** @var string */
	private static $P__VALUE = 'value';
	/** @var string */
	private static $P__WEIGHT = 'weight';

	/**
	 * 2016-08-09
	 * @used-by \Df\Payment\Block\Info::add()
	 * @param string $name
	 * @param string|Phrase $value
	 * @param int $weight [optional]
	 * @return self
	 */
	static function i($name, $value, $weight = 0) {return new self([
		self::$P__NAME => $name, self::$P__VALUE => $value, self::$P__WEIGHT => $weight
	]);}
}