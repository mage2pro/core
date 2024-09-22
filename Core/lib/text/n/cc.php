<?php
/**
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @used-by df_api_rr_failed()
 * @used-by df_error_create()
 * @used-by df_fe_init()
 * @used-by df_kv()
 * @used-by df_log_l()
 * @used-by df_tag_list()
 * @used-by df_tab()
 * @used-by df_xml_g()
 * @used-by df_xml_prettify()
 * @used-by df_zf_http_last_req()
 * @used-by dfp_error_message()
 * @used-by \Df\Core\Html\Tag::content()
 * @used-by \Df\Core\Text\Regex::getSubjectReportPart()
 * @used-by \Df\Framework\Plugin\View\Asset\Source::aroundGetContent()
 * @used-by \Df\Payment\Comment\Description::getCommentText()
 * @used-by \Df\Qa\Dumper::dumpArrayElements()
 * @used-by \Df\Qa\Method::raiseErrorParam()
 * @used-by \Df\Qa\Method::raiseErrorResult()
 * @used-by \Df\Qa\Method::raiseErrorVariable()
 * @used-by \Df\Typography\Css::render()
 * @used-by \Dfe\AllPay\Block\Info\Barcode::paymentId()
 * @used-by \Dfe\Frontend\Block\ProductView\Css::_toHtml()
 * @used-by \Dfe\GoogleFont\ResponseValidator::short()
 * @used-by \Dfe\Klarna\Button::_toHtml()
 * @used-by \Dfe\Markdown\FormElement::css()
 * @used-by \Dfe\Sift\Test\CaseT\API\Account\Decisions::t01()
 * @used-by \Dfe\Stripe\Block\Multishipping::_toHtml()
 * @used-by \Inkifi\Map\HTML::tiles()
 * @used-by \SayItWithAGift\Options\Frontend::_toHtml()
 * @used-by \CabinetsBay\Catalog\B\Category::title() (https://github.com/cabinetsbay/catalog/issues/5)
 * @used-by \TFC\Core\B\Home\Slider::i() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/core/issues/43)
 * @used-by \TFC\Core\B\Home\Slider::p() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/core/issues/43)
 * @used-by \Verdepieno\Core\CustomerAddressForm::f()
 * @used-by \Wyomind\SimpleGoogleShopping\Model\Observer::checkToGenerate(canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/26)
 * @used-by app/code/WeltPixel/QuickCart/view/frontend/templates/checkout/cart/item/price/sidebar.phtml (https://github.com/cabinetsbay/site/issues/145)
 * @used-by https://github.com/tradefurniturecompany/report/blob/1.0.3/view/frontend/templates/index.phtml#L25
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/category/l2/l3/filters.phtml (https://github.com/cabinetsbay/catalog/issues/18)
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/category/l3/tabs/overview.phtml (https://github.com/cabinetsbay/catalog/issues/22)
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/products/not-empty.phtml (https://github.com/cabinetsbay/catalog/issues/38)
 * @used-by vendor/cabinetsbay/core/view/frontend/templates/Magento/Tax/item/price/unit.phtml (https://github.com/cabinetsbay/site/issues/143)
 * @used-by vendor/cabinetsbay/core/view/frontend/templates/head.phtml (https://github.com/cabinetsbay/catalog/issues/39)
 * @used-by vendor/mage2pro/portal-stripe/view/frontend/templates/page/customers.phtml
 * @used-by vendor/mage2pro/portal/view/frontend/templates/root.phtml
 * @param string|array(string) ...$a
 */
function df_cc_n(...$a):string {return df_ccc("\n", ...$a);}