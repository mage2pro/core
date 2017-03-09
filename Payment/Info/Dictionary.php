<?php
namespace Df\Payment\Info;
use Magento\Framework\Phrase;
// 2016-08-09
final class Dictionary implements \IteratorAggregate, \Countable {
	/**
	 * 2016-08-09
	 * @param string $name
	 * @param string|Phrase $value
	 * @param int $weight [optional]
	 */
	function add($name, $value, $weight = 0) {
		$this->_items[$name] = Entry::i($name, $value, $weight);
	}

	/**
	 * 2016-08-09
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::prepareDic()
	 * @param string $nameToFind
	 * @param string $name
	 * @param string|Phrase $value
	 * @param int $weight [optional]
	 */
	function addAfter($nameToFind, $name, $value, $weight = 0) {
		/** @var Entry|null $itemToFind */
		$itemToFind = dfa($this->_items, $nameToFind);
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
			/**
			 * 2016-08-09
			 * Чтобы при вызове @see \Df\Payment\Info\Dictionary::addAfter()
			 * не происходило конфликтов.
			 */
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
	 * @used-by \Df\Payment\Block\Info::getSpecificInformation()
	 * @return array(string => string|Phrase)
	 */
	function get() {
		$this->sort();
		/** @var array(string => string|Phrase) $result */
		$result = [];
		foreach ($this as $e) {
			/** @var Entry $e */
			$result[$e->nameT()] = $e->value();
		}
		return $result;
	}

	/**
	 * 2016-08-09
	 * @override
	 * @see \IteratorAggregate::getIterator()
	 * @return \Traversable
	 */
	function getIterator() {return new \ArrayIterator($this->_items);}

	/**
	 * 2016-08-09
	 * @used-by \Df\Payment\Info\Dictionary::get()
	 * @return void
	 */
	private function sort() {
		$this->_items = df_sort($this->_items,
			function(Entry $a, Entry $b) {return $a->weight() - $b->weight();}
		);
	}

	/**
	 * 2016-08-09
	 * @var array(string => Entry)
	 */
	private $_items = [];
}