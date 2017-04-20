<?php
use Magento\Bundle\Model\Product\Type as Bundle;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Customer\Model\Customer as C;
use Magento\Directory\Model\Currency;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Address as QA;
use Magento\Quote\Model\Quote\Item as QI;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Address as OA;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Sales\Model\Order\Payment as OP;

/**
 * 2017-04-10
 * @used-by df_oq_currency
 * @used-by df_oq_customer_name()
 * @used-by df_order()
 * @used-by df_visitor()
 * @used-by dfp_due()
 * @used-by \Df\Payment\Operation\Source::addressMixed()
 * @param mixed $v
 * @return bool
 */
function df_is_o($v) {return $v instanceof O;}

/**
 * 2017-04-20
 * @param mixed $v
 * @return bool
 */
function df_is_oi($v) {return $v instanceof OI;}

/**
 * 2017-04-08        
 * @used-by df_currency_base()
 * @used-by df_oq()  
 * @used-by dfp()
 * @used-by dfpex_args()
 * @used-by dfpm()
 * @param mixed $v
 * @return bool
 */
function df_is_oq($v) {return $v instanceof O || $v instanceof Q;}

/**
 * 2017-04-10
 * @used-by df_oq_currency
 * @used-by dfp_due()
 * @param mixed $v
 * @return bool
 */
function df_is_q($v) {return $v instanceof Q;}

/**
 * 2017-04-20
 * @param mixed $v
 * @return bool
 */
function df_is_qi($v) {return $v instanceof QI;}

/**
 * 2017-03-12
 * @used-by dfpex_args()
 * @param O|Q $oq
 * @return O|Q
 */
function df_oq($oq) {return df_is_oq($oq) ? $oq : df_error();}

/**
 * 2016-11-15
 * @param O|Q $oq
 * @return Currency
 */
function df_oq_currency($oq) {return df_is_o($oq) ? $oq->getOrderCurrency() : (
	df_is_q($oq) ? df_currency($oq->getQuoteCurrencyCode()) : df_error()
);}

/**
 * 2016-03-09
 * @used-by \Df\Payment\Operation\Source::customerName()
 * @param O|Q $oq
 * @return string
 */
function df_oq_customer_name($oq) {return dfcf(function($oq) {
	/** @var O|Q $oq */
	/** @var string $r */
	// 2017-04-10
	// До завершения оформления заказа гостем quote не содержит имени покупателя,
	// даже если привязанные к quote адреса billing и shipping это имя содержат.
	$r = df_cc_s(array_filter([
		$oq->getCustomerFirstname(), $oq->getCustomerMiddlename(), $oq->getCustomerLastname()
	]));
	/** @var C $c */
	if (!$r && ($c = $oq->getCustomer())) {
		$r = $c->getName();
	}
	/** @var OA|QA|null $ba */
	if (!$r && ($ba = $oq->getBillingAddress())) {
		$r = $ba->getName();
	}
	/** @var OA|QA|null $ba */
	if (!$r && ($sa = $oq->getShippingAddress())) {
		$r = $sa->getName();
	}
	// 2016-08-24
	// Имени в адресах может запросто не быть
	// (например, если покупатель заказывает цифровой товар и requireBillingAddress = false),
	// и вот тогда мы попадаем сюда.
	// В данном случае функция вернёт просто «Guest».
	return $r ?: (df_is_o($oq) ? $oq->getCustomerName() : (string)__('Guest'));
}, [$oq]);}

/**
 * 2017-03-19
 * @used-by \Df\Payment\Method::validate()
 * @param II|OP|QP $p
 * @return O|Q
 */
function dfp_oq(II $p) {return $p instanceof OP ? $p->getOrder() : (
	$p instanceof QP ? $p->getQuote() : df_error()
);}

/**
 * 2017-04-20
 * @param O|Q $oq
 * @return float
 */
function df_oq_shipping_amount($oq) {return df_is_o($oq) ? $oq->getShippingAmount() : (
	df_is_q($oq) ? $oq->getShippingAddress()->getShippingAmount() : df_error()
);}

/**
 * 2017-04-20
 * @param O|Q $oq
 * @return float
 */
function df_oq_shipping_desc($oq) {return df_is_o($oq) ? $oq->getShippingDescription() : (
	df_is_q($oq) ? $oq->getShippingAddress()->getShippingDescription() : df_error()
);}

/**
 * 2017-04-20
 * @param OI|QI $i
 * @return bool
 */
function df_oqi_is_leaf($i) {return df_is_oi($i) ? !$i->getChildrenItems() : (
	df_is_qi($i) ? !$i->getChildren() : df_error()
);}