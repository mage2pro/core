<?php
namespace Df\Checkout\Block;
use Magento\Framework\View\Element\AbstractBlock;
// 2016-07-14
class Messages extends AbstractBlock {
	/**
	 * 2016-07-14
	 * @override
	 * @see AbstractBlock::_toHtml()
	 * @return string
	 */
	protected function _toHtml() {
		/** @var array(array(string => bool|Phrase)) $m */
		$m = df_checkout_session()->getMessagesDf();
		return !$m ? '' : df_x_magento_init('Df_Checkout/js/messages', ['messages' => $m]);
	}
}


