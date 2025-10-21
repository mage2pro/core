<?php
use Magento\Framework\DataObject as _DO;

/**
 * 2017-01-12
 * 1) PHP, к сожалению, не разрешает в выражении с `new` делать выражением имя класса: https://3v4l.org/U6TJR
 * Поэтому и создал эту небольшую функцию.
 * В отличие от @see df_new_om(), она не использует Object Manager.
 * 2) Впервые использую в своём коде возможность argument unpacking, появившуюся в PHP 5.6:
 * https://3v4l.org/eI2vf
 * http://stackoverflow.com/a/25781989
 * https://php.net/manual/functions.arguments.php#example-145
 * 2022-10-31 `object` as a return type is not supported by PHP < 7.2: https://3v4l.org/dAmcs
 * @used-by df_newa()
 * @used-by Df\Payment\Currency::f()
 * @used-by Df\Payment\W\F::__construct()
 * @used-by Df\PaypalClone\Charge::p()
 * @used-by Df\PaypalClone\Signer::_sign()
 * @used-by Df\Sso\Button::s()
 * @used-by Df\Sso\CustomerReturn::c()
 * @used-by Df\StripeClone\Facade\Card::create()
 * @used-by Df\StripeClone\P\Charge::sn()
 * @used-by Df\StripeClone\P\Preorder::request()
 * @used-by Df\StripeClone\P\Reg::request()
 * @used-by Dfe\Zoho\API\Client::i()
 * @used-by Dfe\CheckoutCom\Handler::p()
 * @used-by Dfe\TwoCheckout\Handler::p()
 * @param mixed ...$a
 * @return object
 */
function df_new(string $c, ...$a) {return new $c(...$a);}

/**
 * 2017-01-12
 * PHP, к сожалению, не разрешает в выражении с new делать выражением имя класса.
 * Поэтому и создал эту небольшую функцию.
 * В отличие от @see df_new_om(), она не использует Object Manager.
 * 2022-10-31 `object` as a return type is not supported by PHP < 7.2: https://3v4l.org/dAmcs
 * @used-by dfs_con()
 * @used-by Df\API\Facade::p()
 * @used-by Df\Payment\W\F::aspect()
 * @used-by Df\Zf\Validate\StringT\Parser::getZendValidator()
 * @param mixed ...$a
 * @return object
 */
function df_newa(string $c, string $expected = '', ...$a) {return df_ar(df_new($c, ...$a), $expected);}

/**
 * 2016-01-06
 * 2017-01-12 Use @see df_new() if you do not need Object Manager.
 * 2024-06-03
 * 1) The `object` type requires PHP ≥ 7.2: https://github.com/mage2pro/core/issues/174#user-content-object
 * 2) We need to support PHP ≥ 7.1: https://github.com/mage2pro/core/issues/368
 * @see df_new_omd()
 * @used-by df_category_c()
 * @used-by df_cms_blocks()
 * @used-by df_controller_raw()
 * @used-by df_csv_o()
 * @used-by df_currency()
 * @used-by df_db_transaction()
 * @used-by df_load()
 * @used-by df_mail()
 * @used-by df_mail_shipment()
 * @used-by df_mvars()
 * @used-by df_oi_load()
 * @used-by df_order_c()
 * @used-by df_package_new()
 * @used-by df_pc()
 * @used-by df_region_name()
 * @used-by df_request_i()
 * @used-by df_review_summary()
 * @used-by df_subscriber()
 * @used-by df_url_backend_new()
 * @used-by dfp_refund()
 * @used-by ikf_product_c()
 * @used-by ikf_project()
 * @used-by mc_h()
 * @used-by Df\Customer\AddAttribute\Address::p()
 * @used-by Df\Customer\Plugin\Model\ResourceModel\AddressRepository::aroundSave()
 * @used-by Df\Directory\Model\Country::c()
 * @used-by Df\Framework\Log\Handler\Info::lb()
 * @used-by Df\Framework\Plugin\Data\Form\Element\Fieldset::beforeAddField()
 * @used-by Df\Framework\Upgrade::sEav()
 * @used-by Df\Payment\ConfigProvider::p()
 * @used-by Df\Sales\Model\Order\Payment::getInvoiceForTransactionId()
 * @used-by Df\Sales\Plugin\Model\Order\Address\Renderer::addressConfig()
 * @used-by Df\Sso\CustomerReturn::mc()
 * @used-by Df\Sso\CustomerReturn::register()
 * @used-by Dfe\SalesSequence\Plugin\Model\Manager::aroundGetSequence()
 * @used-by Doormall\Shipping\Method::collectRates()
 * @used-by Frugue\Shipping\Method::collectRates()
 * @used-by Inkifi\Mediaclip\API\Entity\Order\Item::mProduct()
 * @used-by Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::pShipment()
 * @used-by Inkifi\Pwinty\Controller\Index\Index::execute()
 * @used-by Interactivated\Quotecheckout\Controller\Index\Updateordermethod::execute() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/116)
 * @used-by KingPalm\B2B\Setup\V140\MoveDataToAddress::p()
 * @used-by Mangoit\MediaclipHub\Controller\Index\AddToCart::execute()
 * @used-by PPCs\Core\Plugin\Quote\Model\QuoteRepository::aroundGetActiveForCustomer()
 * @param array(string => mixed) $p [optional]
 * @return _DO|object
 */
function df_new_om(string $c, array $p = []) {return df_om()->create($c, $p);}

/**
 * 2017-04-08
 * 2024-06-03
 * 1) The `object` type requires PHP ≥ 7.2: https://github.com/mage2pro/core/issues/174#user-content-object
 * 2) We need to support PHP ≥ 7.1: https://github.com/mage2pro/core/issues/368
 * @used-by df_oq_sa()
 * @used-by CanadaSatellite\Core\Plugin\Magento\Sales\Api\Data\OrderInterface::afterGetPayment() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/29)
 * @used-by Df\Widget\P\Wysiwyg::prepareElementHtml() (https://github.com/mage2pro/core/issues/392)
 * @used-by Dfe\Markdown\Plugin\Ui\Component\Form\Element\Wysiwyg::beforePrepare()
 * @used-by Doormall\Shipping\Method::collectRates()
 * @used-by Frugue\Core\Plugin\Sales\Model\Quote::afterGetAddressesCollection()
 * @used-by Frugue\Shipping\Method::collectRates()
 * @used-by KingPalm\B2B\Block\Registration::e()
 * @used-by KingPalm\B2B\Block\Registration::form()
 * @param array(string => mixed) $d [optional]
 * @return _DO|object
 */
function df_new_omd(string $c, array $d = []) {return df_om()->create($c, ['data' => $d]);}