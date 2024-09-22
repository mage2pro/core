<?php
/**
 * 2016-10-17
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/category/view.phtml (https://github.com/cabinetsbay/catalog/issues/18)
 * @param string|string[] ...$a
 */
function df_c(...$a):string {return implode(dfa_flatten($a));}

/**
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @see df_ccc()
 * @used-by df_js_data()
 * @used-by \Alignet\Paymecheckout\Model\Client\Classic\Order\DataGetter::userCodePayme() (innomuebles.com, https://github.com/innomuebles/m2/issues/17)
 * @used-by \Df\Qa\Trace\Formatter::p()
 * @used-by \Dfe\Sift\API\Facade\GetDecisions::path()
 * @param string|string[] ...$a
 */
function df_cc(string $glue, ...$a):string {return implode($glue, dfa_flatten($a));}

/**
 * 2016-08-13
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @used-by \Df\Payment\Settings::messageFailure()
 * @used-by \Dfe\AllPay\Choice::title()
 * @used-by \Dfe\Square\API\Validator::short()
 * @used-by \Stock2Shop\OrderExport\Observer\OrderSaveAfter::execute()
 * @param string|string[] ...$a
 */
function df_cc_br(...$a):string {return df_ccc("<br>", dfa_flatten($a));}

/**
 * 2016-08-10
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @used-by df_block_output()
 * @used-by df_cli_cmd()
 * @used-by df_log_l()
 * @used-by dfe_modules_info()
 * @used-by \Dfe\Square\Block\Info::prepare()
 * @used-by \Dfe\Stripe\Block\Multishipping::cardholder()
 * @used-by \Frugue\Shipping\Header::_toHtml()
 * @used-by \Hotlink\Brightpearl\Model\Api\Transport::_submit() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/site/issues/122)
 * @used-by \Inkifi\Mediaclip\API\Client::headers()
 * @used-by \KingPalm\B2B\Block\Registration::_toHtml()
 * @used-by \KingPalm\B2B\Block\Registration::e()
 * @used-by \KingPalm\B2B\Block\Registration::region()
 * @used-by \KingPalm\B2B\Observer\RegisterSuccess::execute()
 * @used-by \TFC\GoogleShopping\Att\Price::format() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/google-shopping/issues/6)
 * @used-by app/code/WeltPixel/QuickCart/view/frontend/templates/checkout/cart/item/price/sidebar.phtml (https://github.com/cabinetsbay/site/issues/145)
 * @used-by vendor/cabinetsbay/core/view/frontend/templates/Magento/Tax/item/price/unit.phtml (https://github.com/cabinetsbay/site/issues/143)
 * @used-by \Wolf\Filter\Block\Navigation::hDropdowns()
 * @param string|string[] ...$a
 */
function df_cc_s(...$a):string {return df_ccc(' ', dfa_flatten($a));}

/**
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @see df_cc()
 * @used-by df_asset_name()
 * @used-by df_cc_br()
 * @used-by df_cc_method()
 * @used-by df_cc_n()
 * @used-by df_cc_s()
 * @used-by df_fe_init()
 * @used-by df_log_l()
 * @used-by df_oqi_s()
 * @used-by df_report_prefix()
 * @used-by df_sentry()
 * @used-by df_url_bp()
 * @used-by \Df\Payment\Comment\Description::locations()
 * @used-by \Df\Payment\TID::e2i()
 * @used-by \Df\Payment\W\Handler::log()
 * @used-by \Df\Payment\W\Reader::testData()
 * @used-by \Df\Qa\Trace\Formatter::p()
 * @used-by \Dfe\AllPay\Block\Info\BankCard::custom()
 * @used-by \Dfe\AllPay\Charge::pIgnorePayment()
 * @used-by \Dfe\AllPay\Charge::productUrls()
 * @used-by \Dfe\AmazonLogin\Customer::url()
 * @used-by \Dfe\TwoCheckout\Charge::liDiscount()
 * @param string|string[] ...$a
 */
function df_ccc(string $glue, ...$a):string {return implode($glue, df_clean(dfa_flatten($a)));}