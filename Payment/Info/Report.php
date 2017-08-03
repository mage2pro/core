<?php
namespace Df\Payment\Info;
use Magento\Framework\Phrase;
// 2016-08-09
final class Report implements \IteratorAggregate, \Countable {
	/**
	 * 2016-08-09
	 * @param string|Phrase|null $name
	 * @param string|Phrase $value
	 * @param int $weight [optional]
	 */
	function add($name, $value, $weight = 0) {$this->_items[]= new Entry($name, $value, $weight);}

	/**
	 * 2016-08-09
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::prepareDic()
	 * @param string $nameToFind
	 * @param string|Phrase|null $name
	 * @param string|Phrase $value
	 * @param int $weight [optional]
	 */
	function addAfter($nameToFind, $name, $value, $weight = 0) {
		/** @var Entry|null $itemToFind */
		$itemToFind = df_find($this, function(Entry $e) use($nameToFind) {return $e->name() === $nameToFind;});
		$this->add($name, $value, !$itemToFind ? 0 : 1 + $itemToFind->weight());
	}

	/**
	 * 2016-08-09
	 * @param array(string => string|Phrase) $items
	 * @param int $weight [optional]
	 */
	function addA(array $items, $weight = 0) {
		foreach ($items as $name => $value) {
			$this->add($name, $value, $weight);
			/** 2016-08-09 Чтобы при вызове @see \Df\Payment\Info\Report::addAfter() не происходило конфликтов. */
			$weight += 10;
		}
	}

	/**
	 * 2016-08-09
	 * @override
	 * @see \Countable::count()
	 * @return int
	 */
	function count() {return count($this->_items);}

	/**
	 * 2016-08-09
	 * @override
	 * @see \IteratorAggregate::getIterator()
	 * @return \Traversable
	 */
	function getIterator() {return new \ArrayIterator($this->_items);}

	/**
	 * 2016-08-09
	 * @used-by \Df\Payment\Info\Report::get()
	 * @used-by \Df\Payment\Block\Info::_toHtml()
	 * @uses \Df\Payment\Info\Entry::weight()
	 */
	function sort() {$this->_items = df_sort($this->_items, 'weight');}

	/**
	 * 2016-08-09
	 * @var array(string => Entry)
	 */
	private $_items = [];
}