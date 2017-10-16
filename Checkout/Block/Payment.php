<?php
namespace Df\Checkout\Block;
use Magento\Framework\View\Element\AbstractBlock as _P;
/**
 * 2016-08-17
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * Цель этого блока — добавить на страницу оформления заказа JavaScript,
 * который донастроит внешний вид и поведение блока способов оплаты.
 * @used-by https://github.com/mage2pro/core/blob/2.3.3/Checkout/view/frontend/layout/checkout_index_index.xml#L14
 */
class Payment extends _P {
	/**
	 * 2016-08-17
	 * 2017-04-04
	 * @uses Df_Checkout/payment
	 * https://github.com/mage2pro/core/blob/2.4.26/Checkout/view/frontend/web/payment.js
	 * @override
	 * @see _P::_toHtml()
	 * @used-by _P::toHtml():
	 *		$html = $this->_loadCache();
	 *		if ($html === false) {
	 *			if ($this->hasData('translate_inline')) {
	 *				$this->inlineTranslation->suspend($this->getData('translate_inline'));
	 *			}
	 *			$this->_beforeToHtml();
	 *			$html = $this->_toHtml();
	 *			$this->_saveCache($html);
	 *			if ($this->hasData('translate_inline')) {
	 *				$this->inlineTranslation->resume();
	 *			}
	 *		}
	 *		$html = $this->_afterToHtml($html);
	 * https://github.com/magento/magento2/blob/2.2.0/lib/internal/Magento/Framework/View/Element/AbstractBlock.php#L643-L689
	 * @return string
	 */
	final protected function _toHtml() {return df_js(__CLASS__, 'payment');}
}


