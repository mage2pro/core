<?php
use Magento\Directory\Model\Currency;
use Magento\Sales\Model\Order as O;
use Magento\Quote\Model\Quote as Q;
/**
 * 2016-11-15
 * @param O|Q $oq
 * @return Currency
 */
function df_oq_currency($oq) {return
	$oq instanceof O ? $oq->getOrderCurrency() : (
		$oq instanceof Q ? df_currency($oq->getQuoteCurrencyCode()) : df_error()
	)
;}