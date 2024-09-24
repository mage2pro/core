<?php
use Df\Core\Exception as DFE;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Item as QI;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Sales\Model\Order\Payment as OP;

/**
 * 2017-03-12
 * @see df_sales_doc()
 * @used-by df_oqi_currency_c()
 * @used-by df_sales_doc()
 * @used-by dfpex_args()
 * @param O|Q|OI|QI $i
 * @return O|Q
 */
function df_oq($i) {return df_is_oq($i) ? $i : (df_is_oi($i) ? $i->getOrder() : (df_is_qi($i) ? $i->getQuote() : df_error()));}

/**
 * 2017-03-19
 * @used-by Df\Payment\Method::validate()
 * @used-by Df\Payment\Operation::oq()
 * @param II|OP|QP $p
 * @return O|Q
 * @throws DFE
 */
function dfp_oq(II $p) {return df_assert($p instanceof OP ? $p->getOrder() : ($p instanceof QP ? $p->getQuote() : df_error()));}