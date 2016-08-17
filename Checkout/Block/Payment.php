<?php
namespace Df\Checkout\Block;
use Magento\Framework\View\Element\AbstractBlock;
/**
 * 2016-08-17
 * Цель этого блока — добавить на страницу оформления заказа JavaScript,
 * который донастроит внешний вид и поведение блока способов оплаты.
 */
class Payment extends AbstractBlock {
	/**
	 * 2016-08-17
	 * @override
	 * @see AbstractBlock::_toHtml()
	 * @return string
	 */
	protected function _toHtml() {return  df_x_magento_init('Df_Checkout/js/payment');}
}


