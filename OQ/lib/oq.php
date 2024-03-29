<?php
use Df\Core\Exception as DFE;
use Df\Quote\Model\Quote as DfQ;
use Magento\Customer\Model\Customer as C;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Api\Data\CartInterface as IQ;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Address as QA;
use Magento\Quote\Model\Quote\Item as QI;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Api\Data\OrderInterface as IO;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Address as OA;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Sales\Model\Order\Payment as OP;
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
 * @used-by df_oq_sa()
 * @used-by df_oqi_leafs()
 * @used-by df_order()
 * @used-by df_quote_id()
 * @used-by df_visitor()
 * @used-by dfp_due()
 * @used-by \Df\Payment\Operation::__construct()
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
 * @used-by \Df\Quote\Model\Quote::runOnFreshAC()
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
 * 2017-12-13
 * "Improve @see \Magento\Payment\Model\Checks\CanUseForCountry:
 * it should give priority to the shipping country over the billing country for my modules":
 * https://github.com/mage2pro/core/issues/62
 * @used-by \Df\Payment\Plugin\Model\Checks\CanUseForCountry::aroundIsApplicable()
 * @used-by \Df\Payment\Settings::applicableForQuoteByCountry()
 * @param O|Q $oq
 */
function df_oq_country_sb($oq):string {return DfQ::runOnFreshAC(function() use($oq) {return
	($a = $oq->getShippingAddress()) && ($r = $a->getCountry()) ? $r : (
		($a = $oq->getBillingAddress()) && ($r = $a->getCountry()) ? $r :
			df_directory()->getDefaultCountry()
	)
;}, $oq);}

/**
 * 2016-11-15
 * @used-by df_oqi_currency_c()
 * @used-by \Df\Payment\Currency::fromOrder()
 * @used-by \Df\Payment\Currency::oq()
 * @used-by \Dfe\Klarna\Api\Checkout\V2\Charge\Part::amount()
 * @used-by \Dfe\Moip\Test\Order::amount()
 * @param O|Q $oq
 * @throws DFE
 */
function df_oq_currency_c($oq):string {return df_is_o($oq) ? $oq->getOrderCurrencyCode() : (
	df_is_q($oq) ? $oq->getQuoteCurrencyCode() : df_error(
		'df_oq_currency_c(): an order or quote is required, but got %s.', df_type($oq)
	)
);}

/**
 * 2016-03-09
 * @used-by \Df\Payment\Operation\Source::customerName()
 * @param O|Q $oq
 */
function df_oq_customer_name($oq):string {return dfcf(function($oq) {
	/** @var O|Q $oq */ /** @var string $r */
	# 2017-04-10
	# До завершения оформления заказа гостем quote не содержит имени покупателя,
	# даже если привязанные к quote адреса billing и shipping это имя содержат.
	$r = df_cc_s(array_filter([$oq->getCustomerFirstname(), $oq->getCustomerMiddlename(), $oq->getCustomerLastname()]));
	if (!$r && ($c = $oq->getCustomer())) { /** @var C $c */
		$r = $c->getName();
	}
	if (!$r && ($ba = $oq->getBillingAddress())) { /** @var OA|QA|null $ba */
		$r = $ba->getName();
	}
	if (!$r && ($sa = $oq->getShippingAddress())) { /** @var OA|QA|null $ba */
		$r = $sa->getName();
	}
	# 2016-08-24
	# Имени в адресах может запросто не быть
	# (например, если покупатель заказывает цифровой товар и requireBillingAddress = false),
	# и вот тогда мы попадаем сюда.
	# В данном случае функция вернёт просто «Guest».
	return $r ?: (df_is_o($oq) ? $oq->getCustomerName() : (string)__('Guest'));
}, [$oq]);}

/**
 * 2018-11-14
 * @used-by \Df\Payment\Metadata::vars()
 * @param O|Q $oq
 */
function df_oq_iid($oq):string {/** @var string $r */
	if (df_is_o($oq)) {
		$r = $oq->getIncrementId();
	}
	else {
		$r = $oq->reserveOrderId()->getReservedOrderId();
		# 2018-12-05
		# We should save the reserved order ID in the quote. It fixes the issue:
		# «The order number pulled into transaction description in the bank
		# doesn't match with our order numbers (it's off by 1)»:
		# https://github.com/mage2pro/tbc-bank/issues/1
		$oq->save();
	}
	return $r
;}

/**
 * 2017-11-02
 * If an order/quote consist of virtual products only, then it does not have a shipping address.
 * In this case:
 * *) @uses \Magento\Sales\Model\Order::getShippingAddress() returns null
 * *) @uses \Magento\Quote\Model\Quote::getShippingAddress() returns an empty object.
 * It is useful for me to return an empty object in the both cases.
 * https://en.wikipedia.org/wiki/Null_object_pattern
 * An empty order address can be detected by a `null`-response on
 * @see \Magento\Sales\Model\Order\Address::getParentId()
 * @used-by \Df\Payment\Operation\Source::addressS()
 * @used-by \Dfe\Stripe\Init\Action::need3DS()
 * @param O|Q $oq
 * @return OA|QA|null
 */
function df_oq_sa($oq, bool $empty = false) {/** @var OA|QA|null $r */
	if (df_is_o($oq)) {
		$r = $oq->getShippingAddress() ?: (!$empty ? null : df_new_omd(OA::class, ['address_type' => OA::TYPE_SHIPPING]));
	}
	elseif (df_is_q($oq)) {
		/**
		 * 2017-11-02
		 * I implemented it by analogy with @see \Magento\Quote\Model\Quote::_getAddressByType()
		 * https://github.com/magento/magento2/blob/2.2.0/app/code/Magento/Quote/Model/Quote.php#L1116-L1133
		 * @see \Magento\Quote\Model\Quote::getShippingAddress()
		 */
		$r = df_find(function(QA $a) use($empty) {return
			!$a->isDeleted() && QA::TYPE_SHIPPING === $a->getAddressType()
		;}, $oq->getAddressesCollection()) ?: (!$empty ? null :
			df_new_omd(QA::class, ['address_type' => QA::TYPE_SHIPPING])
		);
	}
	else {
		df_error();
	}
	return $r;
}

/**
 * 2017-04-20
 * @used-by \Dfe\Moip\P\Preorder::pAmount()
 * @param O|Q $oq
 */
function df_oq_shipping_amount($oq):float {return df_is_o($oq) ? $oq->getShippingAmount() : (
	df_is_q($oq) ? $oq->getShippingAddress()->getShippingAmount() : df_error()
);}

/**
 * 2017-04-20
 * @used-by \Dfe\Klarna\Api\Checkout\V2\Charge\Shipping::p()
 * @param O|Q $oq
 */
function df_oq_shipping_desc($oq):float {return df_is_o($oq) ? $oq->getShippingDescription() : (
	df_is_q($oq) ? $oq->getShippingAddress()->getShippingDescription() : df_error()
);}

/**
 * 2017-04-20
 * @used-by df_oqi_leafs()
 * @used-by \Yaman\Ordermotion\Observer::BuildOrderDetail()
 * @param OI|QI $i
 */
function df_oqi_is_leaf($i):bool {return df_is_oi($i) ? !$i->getChildrenItems() : (
	df_is_qi($i) ? !$i->getChildren() : df_error()
);}

/**
 * 2017-03-19
 * @used-by \Df\Payment\Method::validate()
 * @used-by \Df\Payment\Operation::oq()
 * @param II|OP|QP $p
 * @return O|Q
 * @throws DFE
 */
function dfp_oq(II $p) {return df_assert($p instanceof OP ? $p->getOrder() : ($p instanceof QP ? $p->getQuote() : df_error()));}