<?php
use Df\Core\Exception as DFE;
use Df\Quote\Model\Quote as DfQ;
use Magento\Customer\Model\Customer as C;
use Magento\Quote\Api\Data\CartInterface as IQ;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Address as QA;
use Magento\Sales\Api\Data\OrderInterface as IO;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Address as OA;

/**
 * 2017-12-13
 * "Improve @see \Magento\Payment\Model\Checks\CanUseForCountry:
 * it should give priority to the shipping country over the billing country for my modules":
 * https://github.com/mage2pro/core/issues/62
 * @used-by Df\Payment\Plugin\Model\Checks\CanUseForCountry::aroundIsApplicable()
 * @used-by Df\Payment\Settings::applicableForQuoteByCountry()
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
 * @used-by Df\Payment\Currency::fromOrder()
 * @used-by Df\Payment\Currency::oq()
 * @used-by Dfe\Klarna\Api\Checkout\V2\Charge\Part::amount()
 * @used-by Dfe\Moip\Test\Order::amount()
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
 * @used-by Df\Payment\Operation\Source::customerName()
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
 * 2024-06-02
 * 1.1) @uses \Magento\Quote\Model\Quote::getCustomerEmail()
 * 1.2) @uses \Magento\Sales\Api\Data\OrderInterface::getCustomerEmail()
 * 1.3) @uses \Magento\Sales\Model\Order::getCustomerEmail()
 * 1.4) @see \Magento\Quote\Api\Data\CartInterface does not have a method or a field for the customer's email.
 * 2) The customer's email can be absent in a quote.
 * @used-by df_subscriber()
 * @param O|IO|Q|IQ $oq
 */
function df_oq_email($oq):?string {return $oq->getCustomerEmail();}

/**
 * 2018-11-14
 * @used-by Df\Payment\Metadata::vars()
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
 * @used-by Df\Payment\Operation\Source::addressS()
 * @used-by Dfe\Stripe\Init\Action::need3DS()
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
 * @used-by Dfe\Moip\P\Preorder::pAmount()
 * @param O|Q $oq
 */
function df_oq_shipping_amount($oq):float {return df_is_o($oq) ? $oq->getShippingAmount() : (
	df_is_q($oq) ? $oq->getShippingAddress()->getShippingAmount() : df_error()
);}

/**
 * 2017-04-20
 * @used-by Dfe\Klarna\Api\Checkout\V2\Charge\Shipping::p()
 * @param O|Q $oq
 */
function df_oq_shipping_desc($oq):float {return df_is_o($oq) ? $oq->getShippingDescription() : (
	df_is_q($oq) ? $oq->getShippingAddress()->getShippingDescription() : df_error()
);}