<?php
use Magento\Framework\Exception\NoSuchEntityException as NSE;
use Magento\Quote\Api\CartManagementInterface as IQM;
use Magento\Quote\Api\CartRepositoryInterface as IQR;
use Magento\Quote\Api\Data\CartInterface as IQ;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\QuoteManagement as QM;
use Magento\Quote\Model\QuoteRepository as QR;

/**
 * 2016-07-18
 * @see df_order()
 * @used-by \Df\Payment\ConfigProvider::config()
 * @used-by \Df\Payment\Method::getInfoInstance()
 * @used-by \Df\Payment\Settings::applicableForQuoteByCountry()
 * @used-by \Df\Payment\Settings::applicableForQuoteByMinMaxTotal()
 * @used-by \Dfe\Stripe\Block\Multishipping::_toHtml()
 * @param IQ|Q|int|null $q [optional]
 * @return IQ|Q
 * @throws NSE
 */
function df_quote($q = null) {return $q instanceof IQ ? $q : (
	$q ? df_quote_r()->get($q) : df_checkout_session()->getQuote()
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