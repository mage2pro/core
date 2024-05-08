<?php
use Df\Core\Exception as DFE;
use Throwable as Th; # 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2019-12-14
 * If you do not want the exception to be logged via @see df_bt_log(),
 * then you can pass an empty string (instead of `null`) as the second argument:
 * @see \Df\Core\Exception::__construct():
 *		if (is_null($m)) {
 *			$m = __($prev ? df_xts($prev) : 'No message');
 *			# 2017-02-20 To facilite the «No message» diagnostics.
 *			if (!$prev) {
 *				df_bt_log();
 *			}
 *		}
 * https://github.com/mage2pro/core/blob/5.5.7/Core/Exception.php#L61-L67
 * @used-by df_assert_qty_supported()
 * @used-by df_call_parent()
 * @used-by df_caller_m()
 * @used-by df_caller_mf()
 * @used-by df_catalog_locator()
 * @used-by df_config_field()
 * @used-by df_configurable_children()
 * @used-by df_date_from_timestamp_14()
 * @used-by df_dtss()
 * @used-by df_eta()
 * @used-by df_fe_fs()
 * @used-by df_float()
 * @used-by df_id()
 * @used-by df_layout_update()
 * @used-by df_module_dir()
 * @used-by df_module_file_name()
 * @used-by df_oqi_amount()
 * @used-by df_oqi_amount()
 * @used-by df_xml_child()
 * @used-by dfaf()
 * @used-by dfp_oq()
 * @used-by dfr_prop()
 * @used-by \Df\Config\A::i()
 * @used-by \Df\Core\Html\Tag::openTagWithAttributesAsText()
 * @used-by \Df\Framework\Form\Element\Fieldset::select()
 * @used-by \Df\Payment\Operation\Source\Quote::id()
 * @used-by \Df\Qa\Trace\Frame::methodParameter()
 * @used-by \Df\Qa\Trace\Frame::url()
 * @used-by \Df\StripeClone\Payer::newCard()
 * @used-by \Df\Typography\Font::validate()
 * @used-by \Dfe\AllPay\InstallmentSales\Plan\Entity::validate()
 * @used-by \Dfe\AllPay\Total\Quote::collect()
 * @used-by \Dfe\AllPay\Total\Quote::iiAdd()
 * @used-by \Dfe\AlphaCommerceHub\Method::charge()
 * @used-by \Dfe\CheckoutCom\Response::getCaptureCharge()
 * @used-by \Dfe\Color\Observer\ProductImportBunchSaveAfter::execute()
 * @used-by \Dfe\GoogleFont\Font::variant()
 * @used-by \Dfe\GoogleFont\Font\Variant\Preview::draw()
 * @used-by \Dfe\GoogleFont\Fonts::get()
 * @used-by \Dfe\GoogleFont\Fonts\Png::image()
 * @used-by \Dfe\Logo\Observer\OptionSaveAfter::execute()
 * @used-by \Dfe\TwoCheckout\Method::_refund()
 * @used-by \Dfe\TwoCheckout\Method::charge()
 * @used-by \Dfe\YandexKassa\W\Responder::error()
 * @used-by \Inkifi\Mediaclip\API\Entity\Order\Item::mProduct()
 * @used-by \Inkifi\Mediaclip\Event::oi()
 * @used-by \RWCandy\Captcha\Assert::email()
 * @used-by \RWCandy\Captcha\Assert::name()
 * @used-by \RWCandy\Captcha\Observer\CustomerAccountCreatePost::execute()
 * @used-by \RWCandy\Captcha\Observer\CustomerSaveBefore::execute()
 * @param mixed $cond
 * @param string|Th|null $m [optional]
 * @return mixed
 * @throws DFE
 */
function df_assert($cond, $m = null) {return $cond ?: df_error($m);}