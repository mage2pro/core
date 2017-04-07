<?php
namespace Df\Sales\Block\Order;
use Magento\Framework\DataObject as Ob;
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
	 */
	abstract function initTotals();

	/**
	 * 2016-08-14 Add new total to totals array after specific total or before last total by default.
	 * 2017-03-25 В настоящее время никем не используется.
	 * @uses \Magento\Sales\Block\Order\Totals::addTotal()
	 * @param string $code
	 * @param string $label
	 * @param float $value
	 * @param float $valueBase
	 * @param string|null $after [optional]
	 */
	final protected function addAfter($code, $label, $value, $valueBase, $after = null) {$this->add(
		'addTotal', $code, $label, $value, $valueBase, $after
	);}

	/**
	 * 2016-08-14 Add new total to totals array before specific total or after first total by default.
	 * @used-by \Dfe\AllPay\Block\Total::initTotals()
	 * @uses \Magento\Sales\Block\Order\Totals::addTotalBefore()
	 * @param string $code
	 * @param string $label
	 * @param float $value
	 * @param float $valueBase
	 * @param string|null $before [optional]
	 */
	final protected function addBefore($code, $label, $value, $valueBase, $before = null) {$this->add(
		'addTotalBefore', $code, $label, $value, $valueBase, $before
	);}

	/**
	 * 2016-08-14
	 * @used-by \Dfe\AllPay\Block\Total::initTotals()
	 * @return Payment
	 */
	final protected function op() {return dfp($this->getParentBlock()->getOrder());}

	/**
	 * 2016-08-14
	 * @param string $method
	 * @param string $code
	 * @param string $label
	 * @param float $value
	 * @param float $valueBase
	 * @param string|null $position [optional]
	 */
	private function add($method, $code, $label, $value, $valueBase, $position = null) {call_user_func(
		[$this->getParentBlock(), $method]
		,new Ob(['code' => $code, 'label' => __($label), 'value' => $value, 'base_value' => $valueBase])
		,$position
	);}
}