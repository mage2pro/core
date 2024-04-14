<?php
/**
 * @see df_nes()
 * @used-by df_assert_sne()
 * @used-by df_desc()
 * @used-by df_leaf()
 * @used-by df_leaf_sne()
 * @used-by df_n_prepend()
 * @used-by df_param_sne()
 * @used-by df_path_is_internal()
 * @used-by df_report()
 * @used-by df_result_sne()
 * @used-by sift_prefix()
 * @used-by \Df\Config\Backend\Checkbox::dfSaveBefore()
 * @used-by \Df\Framework\Form\Element\Fieldset::color()
 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::aroundGetLabelHtml()
 * @used-by \Df\Payment\Settings::messageFailure()
 * @used-by \Df\Payment\W\Exception\Ignored::message()
 * @used-by \Df\Payment\W\Handler::log()
 * @used-by \Df\Typography\Css::rule()
 * @used-by \Df\Xml\X::importString()
 * @param mixed $v
 */
function df_es($v):bool {return '' === $v;}

/**
 * 2024-04-14
 * @used-by df_tag_if_ne()
 * @param mixed $v
 */
function df_est($v):bool {return df_es(df_trim($v));}

/**
 * 2017-04-26
 * @used-by df_caller_entry()
 * @used-by df_caller_entry_m()
 * @used-by df_ci_get()
 * @used-by df_fe_fc()
 * @used-by df_fetch_one()
 * @used-by df_oi_add()
 * @used-by df_oi_get()
 * @used-by df_package()
 * @used-by df_primary_key()
 * @used-by df_trd()
 * @used-by ikf_oi_pid()
 * @used-by \Amasty\Checkout\Model\Optimization\LayoutJsDiffProcessor::moveArray(canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/120)
 * @used-by \CanadaSatellite\Core\Plugin\Magento\Catalog\Model\Product::afterGetPreconfiguredValues() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/83)
 * @used-by \Df\API\Facade::p()
 * @used-by \Df\Config\Backend\Serialized::valueUnserialize()
 * @used-by \Df\Config\Settings::json()
 * @used-by \Df\Directory\FE\Country::getValues()
 * @used-by \Df\Xml\X::importString()
 * @used-by \Dfe\AllPay\Total\Quote::iiAdd()
 * @used-by \Dfe\AllPay\Total\Quote::iiGet()
 * @used-by \Dfe\CheckoutCom\Method::disableEvent()
 * @used-by \Dfe\Color\Plugin\Swatches\Block\Adminhtml\Attribute\Edit\Options\Visual::afterGetJsonConfig()
 * @used-by \Dfe\Color\Plugin\Swatches\Model\Swatch::beforeBeforeSave()
 * @used-by \Dfe\Markdown\Plugin\Ui\Component\Form\Element\Wysiwyg::beforePrepare()
 * @used-by \Dfe\Square\Facade\Customer::cardsData()
 * @used-by \KingPalm\Core\Plugin\Aitoc\OrdersExportImport\Model\Processor\Config\ExportConfigMapper::aroundToConfig()
 * @used-by \MageSuper\Casat\Observer\ProductSaveBefore::execute() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/73)
 * @used-by \Mangoit\MediaclipHub\Helper\Data::getMediaClipProjects()
 * @used-by \Sharapov\Cabinetsbay\Block\Category\View::images() (https://github.com/cabinetsbay/site/issues/98)
 * @param mixed|null $v
 */
function df_eta($v):array {
	if (!is_array($v)) {
		df_assert(empty($v));
		$v = [];
	}
	return $v;
}

/**
 * 2020-01-29
 * @used-by df_countries_options()
 * @used-by df_currencies_options()
 * @used-by df_customer_session_id()
 * @used-by df_img_resize()
 * @used-by df_slice()
 * @used-by dfa_chop()
 * @used-by \Dfe\Geo\Client::__construct()
 * @used-by \Df\Payment\W\Nav::op()
 * @param mixed $v
 * @return mixed|null
 */
function df_etn($v) {return $v ?: null;}

/**
 * 2023-07-26 "Implement `df_ets()`": https://github.com/mage2pro/core/issues/280
 * @used-by df_log_l()
 * @used-by df_region_name()
 * @param mixed $v
 * @return mixed|string
 */
function df_ets($v) {return $v ?: '';}

/**
 * @used-by df_fetch_one()
 * @used-by df_parent_name()
 * @used-by \Df\Xml\X::descend()
 * @used-by \Dfe\Stripe\Init\Action::need3DS()
 * @param mixed|false $v
 * @return mixed|null
 */
function df_ftn($v) {return false === $v ? null : $v;}

/**
 * 2022-10-15
 * @see df_nts()
 * @used-by df_module_file_name()
 * @used-by \Df\Typography\Css::rule()
 * @used-by \Dfe\Stripe\Init\Action::redirectUrl()
 * @param mixed|false $v
 * @return mixed|string
 */
function df_fts($v) {return false === $v ? '' : $v;}

/**
 * 2016-08-04
 * @see df_es()
 * @used-by df_cache_get_simple()
 * @used-by df_cfg_empty()
 * @used-by df_deployment_cfg()
 * @used-by df_float()
 * @used-by df_json_decode()
 * @used-by df_metadata()
 * @used-by df_oi()
 * @used-by df_request()
 * @used-by dfa()
 * @used-by dfa_deep()
 * @used-by dfa_strict()
 * @used-by dfad()
 * @used-by dftr()
 * @used-by \Df\Framework\Log\Latest::registered()
 * @used-by \Df\Framework\Log\Record::msg()
 * @used-by \Df\Payment\Block\Info::si()
 * @used-by \Df\Payment\Method::s()
 * @used-by \Df\Payment\W\Reader::tl_()
 * @used-by \Df\Shipping\Method::s()
 * @used-by \Dfe\CurrencyFormat\Plugin\Catalog\Controller\Adminhtml\Product\Initialization\Helper\AttributeFilter::beforePrepareProductAttributes()
 * @param mixed $v
 */
function df_nes($v):bool {return is_null($v) || '' === $v;}

/**
 * @see df_fts()
 * @used-by df_starts_with()
 * @used-by df_trim()
 * @used-by sift_prefix()
 * @used-by \Df\API\Facade::p()
 * @used-by \Df\Paypal\Plugin\Model\Api\Nvp::eligible()
 * @used-by \Df\Qa\Trace\Frame::class_()
 * @used-by \Df\Qa\Trace\Frame::function_()
 * @used-by \Df\Typography\Font::variantNumber()
 * @used-by \Dfe\Color\Observer\ProductSaveBefore::execute()
 * @used-by \Dfe\Klarna\Api\Checkout\V2\Charge\ShippingAddress::test()
 * @used-by \Dfe\SalesSequence\Settings::affix()
 * @used-by \Dfe\Sift\API\Validator\Event::long()
 * @used-by \Dfe\Sift\API\Validator\GetDecisions::long()
 * @used-by \Dfe\Sift\API\Validator\GetDecisions::short()
 * @used-by \Dfe\Sift\Payload\Payment\PayPal::p()
 * @used-by \Dfe\Sift\Test\CaseT\PayPal::t01()
 * @used-by \Dfe\TBCBank\API\Validator::long()
 * @used-by \Dfe\TwoCheckout\Address::city()
 * @used-by \Dfe\TwoCheckout\Address::postcode()
 * @used-by \Dfe\TwoCheckout\Address::region()
 * @used-by \Dfe\TwoCheckout\Handler::p()
 * @used-by \Dfe\TwoCheckout\Handler\RefundIssued::pid()
 * @used-by \Dfe\Vantiv\API\Validator::long()
 * @param mixed|null $v
 * @return mixed
 */
function df_nts($v) {return !is_null($v) ? $v : '';}