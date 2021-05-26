<?php
use Magento\Framework\Exception\NoSuchEntityException as NSE;
use Magento\Quote\Api\CartManagementInterface as IQM;
use Magento\Quote\Api\CartRepositoryInterface as IQR;
use Magento\Quote\Model\CustomerManagement as CustomerM;
use Magento\Quote\Api\Data\CartInterface as IQ;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\QuoteManagement as QM;
use Magento\Quote\Model\QuoteRepository as QR;

/**
 * 2016-07-18
 * @see df_order()
 * @used-by df_quote_id()
 * @used-by mnr_recurring_filter()
 * @used-by mnr_recurring_has()
 * @used-by \BlushMe\Checkout\Observer\ControllerActionPredispatch\CheckoutCartIndex::execute()
 * @used-by \Df\Payment\ConfigProvider::config()
 * @used-by \Df\Payment\Method::getInfoInstance()
 * @used-by \Df\Payment\Settings::applicableForQuoteByCountry()
 * @used-by \Df\Payment\Settings::applicableForQuoteByMinMaxTotal()
 * @used-by \Dfe\Stripe\Block\Multishipping::_toHtml()
 * @used-by \Mineralair\Core\Controller\Modal\Index::execute()
 * @used-by \Yaman\Ordermotion\ItemBuilder\SpecialOffer::p()
 * @param IQ|Q|int|null $q [optional]
 * @return IQ|Q
 * @throws NSE
 */
function df_quote($q = null) {return $q instanceof IQ ? $q : (
	$q ? df_quote_r()->get($q) : df_checkout_session()->getQuote()
);}

/**
 * 2021-05-26
 * @return CustomerM
 */
function df_quote_customer_m() {return df_o(CustomerM::class);}

/**
 * 2020-01-25
 * 2020-01-26 @deprecated It is unused.
 * @return int
 */
function df_quote_id() {return df_quote()->getId();}

/**         
 * 2017-03-20
 * @used-by \Df\Payment\PlaceOrderInternal::_place()
 * @return IQM|QM
 */
function df_quote_m() {return df_o(IQM::class);}

/**
 * 2016-07-18
 * @used-by df_quote()
 * @used-by \Doormall\Shipping\Plugin\Checkout\Api\ShippingInformationManagementInterface::beforeSaveAddressInformation()
 * @return IQR|QR
 */
function df_quote_r() {return df_o(IQR::class);}