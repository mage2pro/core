<?php
namespace Df\Checkout\Block;
use Magento\Framework\View\Element\AbstractBlock;
/**
 * 2016-08-17
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * Цель этого блока — добавить на страницу оформления заказа JavaScript,
 * который донастроит внешний вид и поведение блока способов оплаты.
 * @used-by https://github.com/mage2pro/core/blob/2.3.3/Checkout/view/frontend/layout/checkout_index_index.xml#L14
 */
class Payment extends AbstractBlock {
	/**
	 * 2016-08-17
	 * @override
	 * @see AbstractBlock::_toHtml()
	 * @return string
	 */
	final protected function _toHtml() {return df_x_magento_init(__CLASS__, 'js/payment');}
}


