<?php
use Magento\Framework\App\ObjectManager as OM;
use Magento\Framework\ObjectManagerInterface as IOM;
use Magento\Framework\ObjectManager\ConfigInterface as IConfig;
use Magento\Framework\ObjectManager\Config\Config;
use Magento\Framework\ObjectManager\Config\Compiled;
use Magento\Framework\Interception\ObjectManager\Config\Developer;

/**
 * 2017-03-20
 * @used-by df_class_exists()
 */
function df_ctr(string $c):string {return df_vtr(df_om_config()->getPreference($c));}

/**
 * 2017-03-20
 * @used-by Df\Framework\Plugin\App\Router\ActionList::aroundGet()
 */
function df_is_virtual(string $c):bool {return !!dfa(df_virtual_types(), $c);}

/**
 * 2015-08-13
 * @used-by df_action_create()
 * @used-by df_address_h()
 * @used-by df_att_set_r()
 * @used-by df_auth()
 * @used-by df_address_rep()
 * @used-by df_cart()
 * @used-by df_cart_h()
 * @used-by df_catalog_h()
 * @used-by df_catalog_layer()
 * @used-by df_catalog_locator()
 * @used-by df_catalog_output()
 * @used-by df_category_h()
 * @used-by df_checkout_h()
 * @used-by df_cms_block_r()
 * @used-by df_cms_filter_provider()
 * @used-by df_component_r()
 * @used-by df_config_field()
 * @used-by df_customer_group_reg()
 * @used-by df_customer_rep()
 * @used-by df_customer_url()
 * @used-by df_default_stock_provider()
 * @used-by df_deployment_cfg()
 * @used-by df_eav_config()
 * @used-by df_eav_setup()
 * @used-by df_file_resp()
 * @used-by df_ie_store_r()
 * @used-by df_logger()
 * @used-by df_mail_tf()
 * @used-by df_message_m()
 * @used-by df_metadata_pool()
 * @used-by df_module_dir_reader()
 * @used-by df_module_list()
 * @used-by df_module_m()
 * @used-by df_msi_website2stockId()
 * @used-by df_no_dispatch()
 * @used-by df_page_config()
 * @used-by df_page_factory()
 * @used-by df_post_h()
 * @used-by df_product_action()
 * @used-by df_product_b()
 * @used-by df_product_gallery_b()
 * @used-by df_product_r()
 * @used-by df_product_res()
 * @used-by df_quote_customer_m()
 * @used-by df_redirect()
 * @used-by df_rest_ipr()
 * @used-by df_session_manager()
 * @used-by df_setup()
 * @used-by df_stock_cfg()
 * @used-by df_stock_h()
 * @used-by df_url_finder()
 * @used-by df_url_h()
 * @used-by dfe_zv_cfg() (https://github.com/mage2pro/zoom-ve/issues/2)
 * @used-by Df\Core\Session::st()
 * @used-by Df\Customer\Plugin\Js\CustomerId::afterGetSectionData()
 * @used-by Df\Payment\Settings::applicableForQuoteByCountry()
 * @used-by Dfe\KlarnaC\OL::fetch()
 * @used-by Frugue\Core\Plugin\Directory\Model\Resource\Country\Collection::aroundLoadByStore()
 * @used-by Hotlink\Framework\Model\Schedule\Cron\Config\Plugin::afterGetJobs() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/site/issues/127)
 * @used-by Interactivated\Quotecheckout\Controller\Index\Updateordermethod::execute() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/116)
 * @used-by Mageplaza\Blog\Controller\Router::match() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/190)
 * @used-by SayItWithAGift\Options\Frontend::_toHtml()
 * @used-by Wolf\Filter\Block\Navigation::selectedPath()
 * @used-by https://github.com/tradefurniturecompany/report/blob/1.0.3/view/frontend/templates/index.phtml#L20
 * @used-by app/code/WeltPixel/QuickCart/view/frontend/templates/checkout/cart/item/price/sidebar.phtml (https://github.com/cabinetsbay/site/issues/145)
 * @return mixed
 */
function df_o(string $t) {return dfcf(function(string $t) {return df_om()->get($t);}, [$t]);}

/**
 * 2015-08-13
 * @used-by df_cms_block_r()
 * @used-by df_ic()
 * @used-by df_new_om()
 * @used-by df_o()
 * @return OM|IOM
 */
function df_om() {return OM::getInstance();}

/**
 * 2016-05-06
 * @used-by df_class_exists()
 * @used-by df_virtual_types()
 * @used-by Df\Framework\Plugin\App\Router\ActionList::aroundGet()
 * @return IConfig|Config|Compiled|Developer
 */
function df_om_config() {return df_o(IConfig::class);}

/**
 * 2017-03-20
 * @used-by df_ctr()
 * @used-by Df\Framework\Plugin\App\Router\ActionList::aroundGet()
 */
function df_vtr(string $c):string {return df_om_config()->getInstanceType($c);}

/**
 * 2017-03-20
 * @used-by df_is_virtual()
 * @return array(string => string)
 */
function df_virtual_types():array {return df_om_config()->getVirtualTypes();}