<?php
namespace Df\Payment\Info;
use ArrayIterator as AI;
use Magento\Framework\Phrase;
# 2016-08-09
final class Report implements \IteratorAggregate, \Countable {
	/**
	 * 2016-08-09
	 * @used-by self::addA()
	 * @used-by self::addAfter()
	 * @used-by \Df\Payment\Block\Info::si()
	 * @used-by \Dfe\AllPay\Block\Info::prepareDic()
	 * @param string|Phrase|null $name
	 * @param string|Phrase $value
	 */
	function add(string $name, string $value, int $weight = 0):void {$this->_items[]= new Entry($name, $value, $weight);}

	/**
	 * 2016-08-09
	 * 2022-11-09 @deprecated It is unused.
	 * @param array(string => string|Phrase) $items
	 * @param int $weight [optional]
	 */
	function addA(array $items, $weight = 0):void {
		foreach ($items as $k => $v) {
			$this->add($k, $v, $weight);
			$weight += 10; /** 2016-08-09 Чтобы при вызове @see \Df\Payment\Info\Report::addAfter() не происходило конфликтов. */
		}
	}

	/**
	 * 2016-08-09
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::prepareDic()
	 * @param string $nameToFind
	 * @param string|Phrase|null $name
	 * @param string|Phrase $value
	 */
	function addAfter($nameToFind, $name, $value):void {
		/** @var Entry|null $itemToFind */
		$itemToFind = df_find($this, function(Entry $e) use($nameToFind) {return $e->name() === $nameToFind;});
		$this->add($name, $value, !$itemToFind ? 0 : 1 + $itemToFind->weight());
	}

	/**
	 * 2016-08-09
	 * @override
	 * @see \Countable::count()
	 */
	function count():int {return count($this->_items);}

	/**
	 * 2016-08-09
	 * @override
	 * @see \IteratorAggregate::getIterator()
	 * https://www.php.net/manual/iteratoraggregate.getiterator.php
	 */
	function getIterator():AI {return new AI($this->_items);}

	/**
	 * 2016-08-09
	 * @used-by \Df\Payment\Block\Info::_toHtml()
	 * @uses \Df\Payment\Info\Entry::weight()
	 */
	function sort():void {$this->_items = df_sort($this->_items, 'weight');}

	/**
	 * 2016-08-09
	 * @var array(string => Entry)
	 */
	private $_items = [];
}