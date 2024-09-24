<?php
use Df\Core\Exception as DFE;
use Magento\Quote\Api\Data\CartInterface as IQ;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Item as QI;
use Magento\Sales\Api\Data\OrderInterface as IO;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;

/**
 * 2021-05-31
 * 2021-06-03 @deprecated It is unused.
 * @param mixed $v
 * @return O|Q
 * @throws DFE
 */
function df_assert_oq($v) {return df_is_oq($v) ? $v : df_error('Expected an order or a quote.');}

/**
 * 2017-04-10
 * @used-by df_is_oq()
 * @used-by df_oq_currency_c ()
 * @used-by df_oq_customer_name()
 * @used-by df_oq_iid()
 * @used-by df_oq_sa()
 * @used-by df_oq_shipping_amount()
 * @used-by df_oq_shipping_desc()
 * @used-by df_oqi_leafs()
 * @used-by df_order()
 * @used-by df_quote_id()
 * @used-by df_store()
 * @used-by df_visitor()
 * @used-by df_website()
 * @used-by dfp_due()
 * @used-by Df\Payment\Operation::__construct()
 * @param mixed $v
 */
function df_is_o($v):bool {return $v instanceof IO;}

/**
 * 2017-04-20
 * @used-by df_is_oqi()
 * @used-by df_oi()
 * @used-by df_oq()
 * @param mixed $v
 */
function df_is_oi($v):bool {return $v instanceof OI;}

/**
 * 2017-04-08
 * @see df_is_sales_doc()
 * @used-by df_assert_oq()
 * @used-by df_currency_base()
 * @used-by df_is_sales_doc()
 * @used-by df_oq()
 * @used-by df_subscriber()
 * @used-by dfp()
 * @used-by dfpex_args()
 * @used-by dfpm()
 * @param mixed $v
 */
function df_is_oq($v):bool {return df_is_o($v) || df_is_q($v);}

/**
 * 2020-02-05
 * @used-by df_product()
 * @used-by df_sales_doc()
 * @param mixed $v
 */
function df_is_oqi($v):bool {return df_is_oi($v) || df_is_qi($v);}

/**
 * 2017-04-10
 * @used-by df_is_oq()
 * @used-by df_oq_currency_c()
 * @used-by df_oq_sa()
 * @used-by df_quote_id()
 * @used-by dfp_due()
 * @used-by Df\Quote\Model\Quote::runOnFreshAC()
 * @param mixed $v
 */
function df_is_q($v):bool {return $v instanceof IQ;}

/**
 * 2017-04-20
 * @used-by df_is_oqi()
 * @used-by df_oq()
 * @used-by df_oqi_is_leaf()
 * @param mixed $v
 */
function df_is_qi($v):bool {return $v instanceof QI;}