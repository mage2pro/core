<?php
namespace Df\Sales\Block\Order;
use Magento\Framework\DataObject;
use Magento\Sales\Block\Order\Totals;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
/**
 * 2016-08-13
 * Класс-основа, от которого будут наследоваться классы,
 * работающие по аналогии с @see \Magento\Weee\Block\Sales\Order\Totals
 * «How is the Fixed Product Tax information shown on a frontend order's page?» https://mage2.pro/t/1954
 * @method Totals getParentBlock()
 */
abstract class Total extends \Magento\Framework\View\Element\AbstractBlock {
	/**
	 * 2016-08-13
	 * @used-by \Magento\Sales\Block\Order\Totals::_beforeToHtml()
	 * @return void
	 */
	abstract public function initTotals();

	/**
	 * 2016-08-14
	 * Add new total to totals array after specific total or before last total by default
	 * @param string $code
	 * @param string $label
	 * @param float $value
	 * @param float $valueBase
	 * @param string|null $after [optional]
	 * @return void
	 */
	protected function addAfter($code, $label, $value, $valueBase, $after = null) {
		/** @uses \Magento\Sales\Block\Order\Totals::addTotal() */
		$this->add('addTotal', $code, $label, $value, $valueBase, $after);
	}

	/**
	 * 2016-08-14
	 * Add new total to totals array before specific total or after first total by default
	 * @param string $code
	 * @param string $label
	 * @param float $value
	 * @param float $valueBase
	 * @param string|null $before [optional]
	 * @return void
	 */
	protected function addBefore($code, $label, $value, $valueBase, $before = null) {
		/** @uses \Magento\Sales\Block\Order\Totals::addTotalBefore() */
		$this->add('addTotalBefore', $code, $label, $value, $valueBase, $before);
	}

	/**
	 * 2015-08-14
	 * @return Order
	 */
	protected function order() {return $this->getParentBlock()->getOrder();}

	/**
	 * 2015-08-14
	 * @return Payment
	 */
	protected function payment() {return $this->order()->getPayment();}

	/**
	 * 2016-08-14
	 * @param string $method
	 * @param string $code
	 * @param string $label
	 * @param float $value
	 * @param float $valueBase
	 * @param string|null $position [optional]
	 * @return void
	 */
	private function add($method, $code, $label, $value, $valueBase, $position = null) {
		call_user_func(
			[$this->getParentBlock(), $method]
			,new DataObject([
				'code' => $code,'label' => __($label),'value' => $value,'base_value' => $valueBase
			])
			,$position
		);
	}
}


