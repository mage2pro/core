<?php
namespace Df\Sales\Block\Order;
use Magento\Framework\DataObject as _DO;
use Magento\Sales\Block\Order\Totals;
use Magento\Sales\Model\Order\Payment;
/**
 * 2016-08-13
 * Класс-основа, от которого будут наследоваться классы,
 * работающие по аналогии с @see \Magento\Weee\Block\Sales\Order\Totals
 * «How is the Fixed Product Tax information shown on a frontend order's page?» https://mage2.pro/t/1954
 * @method Totals getParentBlock()
 * @see \Dfe\AllPay\Block\Total
 */
abstract class Total extends \Magento\Framework\View\Element\AbstractBlock {
	/**
	 * 2016-08-13
	 * @used-by \Magento\Sales\Block\Order\Totals::_beforeToHtml()
	 * @see \Dfe\AllPay\Block\Total::initTotals()
	 */
	abstract function initTotals():void;

	/**
	 * 2016-08-14 Add new total to totals array after specific total or before last total by default.
	 * 2017-03-25 В настоящее время никем не используется.
	 * @uses \Magento\Sales\Block\Order\Totals::addTotal()
	 */
	final protected function addAfter(string $code, string $l, float $v, float $vBase, string $after = ''):void {$this->add(
		'addTotal', $code, $l, $v, $vBase, $after
	);}

	/**
	 * 2016-08-14 Add new total to totals array before specific total or after first total by default.
	 * @used-by \Dfe\AllPay\Block\Total::initTotals()
	 * @uses \Magento\Sales\Block\Order\Totals::addTotalBefore()
	 */
	final protected function addBefore(string $code, string $l, float $v, float $vBase, string $before = ''):void {$this->add(
		'addTotalBefore', $code, $l, $v, $vBase, $before
	);}

	/**
	 * 2016-08-14
	 * @used-by \Dfe\AllPay\Block\Total::initTotals()
	 */
	final protected function op():Payment {return dfp($this->getParentBlock()->getOrder());}

	/**
	 * 2016-08-14
	 * @used-by self::addAfter()
	 * @used-by self::addBefore()
	 */
	private function add(string $method, string $code, string $l, float $v, float $vBase, string $pos = ''):void {call_user_func(
		[$this->getParentBlock(), $method]
		,new _DO(['code' => $code, 'label' => __($l), 'value' => $v, 'base_value' => $vBase])
		,$pos
	);}
}