<?php
use Magento\Framework\Exception\NoSuchEntityException as NSE;
use Magento\Quote\Api\CartManagementInterface as IQM;
use Magento\Quote\Api\CartRepositoryInterface as IQR;
# 2021-05-26 https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Quote/Model/CustomerManagement.php
use Magento\Quote\Model\CustomerManagement as CustomerM;
use Magento\Quote\Api\Data\CartInterface as IQ;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\QuoteManagement as QM;
use Magento\Quote\Model\QuoteRepository as QR;
use Magento\Sales\Api\Data\OrderInterface as IO;

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
 * @used-by app/code/Interactivated/Quotecheckout/view/frontend/templates/dashboard/onepage/billing.phtml (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/128)
 * @param IQ|Q|IO|int|null $q [optional]
 * @return IQ|Q
 * @throws NSE
 */
function df_quote($q = null) {return $q instanceof IQ ? $q : (
	$q ? df_quote_r()->get(df_quote_id($q)) : df_checkout_session()->getQuote()
);}

/**
 * 2021-05-26
 * @used-by \Interactivated\Quotecheckout\Controller\Index\Updateordermethod::execute() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/116)
 * @return CustomerM
 */
function df_quote_customer_m() {return df_o(CustomerM::class);}

/**
 * 2020-01-25
 * @used-by df_quote()
 * @param IQ|Q|IO|null $q [optional]
 * @return int
 */
function df_quote_id($q = null) {return !$q ? df_quote()->getId() : (
	df_is_o($q) ? $q->getQuoteId() : (df_is_q($q) ? $q->getId() : df_error())
);}

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