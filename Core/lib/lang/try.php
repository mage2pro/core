<?php
use Closure as F;
use Throwable as Th; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
/**
 * 2017-04-15
 * @used-by df_area_code()
 * @used-by df_asset_url()
 * @used-by df_cms_block()
 * @used-by df_contents()
 * @used-by df_currency_convert_safe()
 * @used-by df_customer()
 * @used-by df_customer_group_name()
 * @used-by df_date_from_db()
 * @used-by df_gd()
 * @used-by df_intl_dic_read()
 * @used-by df_layout_update()
 * @used-by df_magento_version_remote()
 * @used-by df_module_file_name()
 * @used-by df_phone()
 * @used-by df_product_att()
 * @used-by df_product_att_val_s()
 * @used-by df_zuri()
 * @used-by dfp_refund()
 * @used-by \Alignet\Paymecheckout\Model\Client\Classic\Order\DataGetter::userCodePayme() (innomuebles.com, https://github.com/innomuebles/m2/issues/17)
 * @used-by \Amasty\Checkout\Controller\Index\Index::isSecureRequest() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/241)
 * @used-by \CanadaSatellite\Core\Plugin\Magento\Sales\Model\Order\Item::aroundGetProductOptions(canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/62)
 * @used-by \Df\Framework\Console\Command::execute()
 * @used-by \Df\Payment\BankCardNetworks::url()
 * @used-by \Df\Qa\Trace\Formatter::p()
 * @used-by \Df\Qa\Trace\Frame::methodR()
 * @used-by \Df\Theme\Controller\Result\MessagePlugin::getCookiesMessages()
 * @used-by \Dfe\CheckoutCom\Method::leh()
 * @used-by \Dfe\GoogleFont\Font\Variant\Preview::box()
 * @used-by \Dfe\GoogleFont\Fonts\Png::url()
 * @used-by \Dfe\Moip\FE\Webhooks::onFormInitialized()
 * @used-by \Dfe\Moip\Facade\Customer::_get()
 * @used-by \Dfe\Square\Facade\Customer::_get()
 * @used-by \Doormall\Shipping\Partner\Entity::locationM()
 * @used-by \Inkifi\Mediaclip\API\Facade\User::projects()
 * @used-by \Sharapov\Cabinetsbay\Block\Category\View::l3p() (https://github.com/cabinetsbay/site/issues/136)
 * @used-by \TFC\GoogleShopping\Controller\Index\Index::execute() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/google-shopping/issues/1)
 * @param F|bool|mixed $onE [optional]
 * @return mixed
 * @throws Th
 */
function df_try(F $try, $onE = null) {
	try {return $try();}
	catch(Th $th) {return $onE instanceof F ? $onE($th) : (true === $onE ? df_error($th) : $onE);}
}