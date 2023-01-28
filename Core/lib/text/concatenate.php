<?php
/**
 * 2016-10-17
 * 2022-10-31 @deprecated It is unused.
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax will reject arrays: https://3v4l.org/jFdPm
 * @param string|string[] $a
 */
function df_c(...$a):string {return implode(dfa_flatten($a));}

/**
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax will reject arrays: https://3v4l.org/jFdPm
 * @see df_ccc()
 * @used-by df_js_data()
 * @used-by \Alignet\Paymecheckout\Model\Client\Classic\Order\DataGetter::userCodePayme() (innomuebles.com, https://github.com/innomuebles/m2/issues/17)
 * @used-by \Df\Qa\Trace\Formatter::frame()
 * @used-by \Dfe\Sift\API\Facade\GetDecisions::path()
 * @param string|string[] $a
 */
function df_cc(string $glue, ...$a):string {return implode($glue, dfa_flatten($a));}

/**
 * 2016-08-13
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax will reject arrays: https://3v4l.org/jFdPm
 * @used-by \Df\Payment\Settings::messageFailure()
 * @used-by \Dfe\AllPay\Choice::title()
 * @used-by \Dfe\Square\API\Validator::short()
 * @used-by \Stock2Shop\OrderExport\Observer\OrderSaveAfter::execute()
 * @param string|string[] $a
 */
function df_cc_br(...$a):string {return df_ccc("<br>", dfa_flatten($a));}

/**
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax will reject arrays: https://3v4l.org/jFdPm
 * @used-by df_api_rr_failed()
 * @used-by df_fe_init()
 * @used-by df_kv()
 * @used-by df_log_l()
 * @used-by df_tag_list()
 * @used-by df_tab_multiline()
 * @used-by df_xml_g()
 * @used-by df_xml_prettify()
 * @used-by df_zf_http_last_req()
 * @used-by dfp_error_message()
 * @used-by \Df\Core\Format\Html\Tag::content()
 * @used-by \Df\Core\Text\Regex::getSubjectReportPart()
 * @used-by \Df\Framework\Plugin\View\Asset\Source::aroundGetContent()
 * @used-by \Df\GoogleFont\Exception::message()
 * @used-by \Df\Payment\Comment\Description::getCommentText()
 * @used-by \Df\Qa\Dumper::dumpArrayElements()
 * @used-by \Df\Qa\Method::raiseErrorParam()
 * @used-by \Df\Qa\Method::raiseErrorResult()
 * @used-by \Df\Qa\Method::raiseErrorVariable()
 * @used-by \Df\Qa\Trace\Formatter::frame()
 * @used-by \Df\Typography\Css::render()
 * @used-by \Dfe\AllPay\Block\Info\Barcode::paymentId()
 * @used-by \Dfe\Frontend\Block\ProductView\Css::_toHtml()
 * @used-by \Dfe\Klarna\Button::_toHtml()
 * @used-by \Dfe\Markdown\FormElement::css()
 * @used-by \Dfe\Sift\Test\CaseT\API\Account\Decisions::t01()
 * @used-by \Dfe\Stripe\Block\Multishipping::_toHtml()
 * @used-by \Inkifi\Map\HTML::tiles()
 * @used-by \SayItWithAGift\Options\Frontend::_toHtml()
 * @used-by \TFC\Core\B\Home\Slider::i() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/core/issues/43)
 * @used-by \TFC\Core\B\Home\Slider::p() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/core/issues/43)
 * @used-by \Verdepieno\Core\CustomerAddressForm::f()
 * @used-by \Wyomind\SimpleGoogleShopping\Model\Observer::checkToGenerate(canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/26)
 * @used-by vendor/mage2pro/portal-stripe/view/frontend/templates/page/customers.phtml
 * @used-by vendor/mage2pro/portal/view/frontend/templates/root.phtml
 * @used-by https://github.com/tradefurniturecompany/report/blob/1.0.3/view/frontend/templates/index.phtml#L25
 * @param string|string[] $a
 */
function df_cc_n(...$a):string {return df_ccc("\n", ...$a);}

/**
 * 2015-12-01 Отныне всегда используем / вместо DIRECTORY_SEPARATOR.
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax will reject arrays: https://3v4l.org/jFdPm
 * @used-by df_config_e()
 * @used-by df_db_credentials()
 * @used-by df_fs_etc()
 * @used-by df_img_resize()
 * @used-by df_js()
 * @used-by df_js_x()
 * @used-by df_product_image_path2abs()
 * @used-by df_replace_store_code_in_url()
 * @used-by \Df\API\Client::url()
 * @used-by \Df\API\Facade::path()
 * @used-by \Df\Config\Backend::value()
 * @used-by \Df\Config\Comment::groupPath()
 * @used-by \Df\Config\Source::sibling()
 * @used-by \Df\Intl\Js::_toHtml()
 * @used-by \Dfe\Sift\API\Facade\GetDecisions::path()
 * @used-by \Doormall\Shipping\Partner\Entity::locations()
 * @used-by \Inkifi\Mediaclip\API\Client::urlBase()
 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::writeLocal()
 * @used-by \KingPalm\Core\Plugin\Aitoc\OrdersExportImport\Model\Processor\Config\ExportConfigMapper::aroundToConfig()
 * @used-by \TFC\Image\Command\C1::image()
 * @used-by \Wolf\Filter\Observer\ControllerActionPredispatch::execute()
 * @param string|string[] $a
 */
function df_cc_path(...$a):string {return df_ccc('/', dfa_flatten($a));}

/**
 * 2016-05-31
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax will reject arrays: https://3v4l.org/jFdPm
 * 2023-01-01 @deprecated It is unused.
 * @param string|string[] $a
 */
function df_cc_path_t(...$a):string {return df_append(df_cc_path(dfa_flatten($a)), '/');}

/**
 * 2016-08-10
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax will reject arrays: https://3v4l.org/jFdPm
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
 * @used-by \Wolf\Filter\Block\Navigation::hDropdowns()
 * @param string|string[] $a
 */
function df_cc_s(...$a):string {return df_ccc(' ', dfa_flatten($a));}

/**
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax will reject arrays: https://3v4l.org/jFdPm
 * @see df_cc()
 * @used-by df_asset_name()
 * @used-by df_cc_br()
 * @used-by df_cc_method()
 * @used-by df_cc_n()
 * @used-by df_cc_path()
 * @used-by df_cc_s()
 * @used-by df_fe_init()
 * @used-by df_log_l()
 * @used-by df_oqi_s()
 * @used-by df_report_prefix()
 * @used-by df_url_bp()
 * @used-by \Df\Payment\Comment\Description::locations()
 * @used-by \Df\Payment\TID::e2i()
 * @used-by \Df\Payment\W\Handler::log()
 * @used-by \Df\Payment\W\Reader::testData()
 * @used-by \Dfe\AllPay\Block\Info\BankCard::custom()
 * @used-by \Dfe\AllPay\Charge::pIgnorePayment()
 * @used-by \Dfe\AllPay\Charge::productUrls()
 * @used-by \Dfe\AmazonLogin\Customer::url()
 * @used-by \Dfe\TwoCheckout\Charge::liDiscount()
 * @param string|string[] $a
 */
function df_ccc(string $glue, ...$a):string {return implode($glue, df_clean(dfa_flatten($a)));}