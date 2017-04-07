<?php
use Magento\Directory\Model\Currency;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;

/**
 * 2017-03-12
 * @param O|Q $oq
 * @return O|Q
 */
function df_oq($oq) {return $oq instanceof O || $oq instanceof Q ? $oq : df_error();}

/**
 * 2016-11-15
 * @param O|Q $oq
 * @return Currency
 */
function df_oq_currency($oq) {return $oq instanceof O ? $oq->getOrderCurrency() : (
	$oq instanceof Q ? df_currency($oq->getQuoteCurrencyCode()) : df_error()
);}

/**
 * 2017-03-19
 * @used-by \Df\Payment\Method::validate()
 * @param II|OP|QP $p
 * @return O|Q
 */
function dfp_oq(II $p) {return $p instanceof OP ? $p->getOrder() : (
	$p instanceof QP ? $p->getQuote() : df_error()
);}