<?php
/**
 * 2019-03-27
 * @used-by \CabinetsBay\Catalog\Observer\LayoutLoadBefore::execute() (https://github.com/cabinetsbay/core/issues/5)
 * @used-by \Frugue\Core\Plugin\Swatches\Helper\Media::afterGetImageConfig()
 */
function df_is_catalog_product_list():bool {return df_handle('catalog_category_view');}

/**
 * 2019-03-27
 * @used-by \Dfe\Frontend\Block\ProductView\Css::_toHtml()
 * @used-by \Frugue\Core\Plugin\Swatches\Helper\Media::afterGetImageConfig()
 * @used-by \TFC\Core\Plugin\Theme\Block\Html\Breadcrumbs::aroundAddCrumb()
 */
function df_is_catalog_product_view():bool {return df_handle('catalog_product_view');}

/**
 * 2016-08-24
 * 2017-08-28 May be we should use @see df_action_is() here?
 * 2017-12-04
 * The previous code was:
 * 		df_handle('checkout_index_index')
 * https://github.com/mage2pro/core/blob/3.4.0/Core/lib/page-type.php#L2-L9
 * It is not compatible with MagePlaza's One Step Checkout
 * (and presumably with some other third-party one-step-checkout extensions),
 * because MagePlaza's One Step Checkout uses `/onestepcheckout` frontend URL,
 * which is translated to the `onestepcheckout_index_index` handle by M2 core.
 * "The MagePlaza's One Step Checkout does not show the Stripe module's payment form"
 * https://github.com/mage2pro/stripe/issues/57
 * @see df_is_checkout_multishipping()
 * @used-by mnr_recurring()
 * @used-by \Df\Payment\ConfigProvider::getConfig()
 * @used-by \Df\Payment\ConfigProvider\GlobalT::getConfig()
 * @used-by \Df\Shipping\ConfigProvider::getConfig()
 */
function df_is_checkout():bool {return
	df_is_checkout_multishipping()
	|| df_action_prefix('onestepcheckout') # 2018-09-22 Aheadworks OneStepCheckout, MagePlaza OneStepCheckout
	|| df_action_prefix('checkout_index') # 2017-12-04 It eliminates the `checkout_cart` and `checkout_success` cases.
	# 2018-09-22
	# "The Swissup's «Fire Checkout» module does not show Mage2.PRO payment methods
	# on the frontend checkout screen": https://github.com/mage2pro/core/issues/79
	|| df_action_prefix('firecheckout_index')
;}

/**
 * 2017-08-24
 * 2017-08-28
 * df_handle_prefix('multishipping_checkout') is wrong here
 * because it does not work before the layout initialization.
 * @used-by df_is_checkout()
 * @used-by \Df\Payment\Block\Info::_toHtml()
 * @used-by \Df\Payment\Observer\Multishipping::execute()
 */
function df_is_checkout_multishipping():bool {return df_action_prefix('multishipping_checkout');}

/**
 * 2017-03-29
 * 2017-08-28
 * @todo May be we should use @see df_action() here?
 * @see  df_is_checkout_multishipping()
 * How to detect the «checkout success» page programmatically in PHP? https://mage2.pro/t/3562
 * @used-by \Df\Payment\Block\Info::_toHtml()
 */
function df_is_checkout_success():bool {return df_handle('checkout_onepage_success');}

/**
 * 2018-11-23
 * @used-by \Frugue\Core\Plugin\Framework\App\PageCache\Kernel()
 * @used-by \Magento\RequireJs\Model\FileManager::createBundleJsPool() (Frugue)
 */
function df_is_home():bool {return df_handle('cms_index_index');}

/**
 * 2016-12-04
 * 2017-08-28
 * @todo May be we should use @see df_action() here?
 * @see df_is_checkout_multishipping()
 * @used-by \Df\Sso\Css::isAccConfirmation()
 */
function df_is_login():bool {return df_handle('customer_account_login');}

/**
 * 2016-12-02
 * 2017-08-28
 * @todo May be we should use @see df_action() here?
 * @see  df_is_checkout_multishipping()
 */
function df_is_reg():bool {return df_handle('customer_account_create');}

/**
 * 2017-10-15
 * 2017-12-04
 * The previous code was:
 * 		df_handle('adminhtml_system_config_edit')
 * It is incorrect, because:
 * 1) It does not take into account the `admin/system_config/save` action.
 * 2) We do not have any layout handles yet in a @see \Df\Config\Backend::dfSaveAfter() handler:
 * e.g., in the @see \Dfe\Moip\Backend\Enable::dfSaveAfter() handler.
 * So the following code will not help us:
 * 		df_handle_prefix('adminhtml_system_config_')
 * It can be related to the following Moip issue:
 * "«Please set your Moip private key in the Magento backend» even if the Moip private key is set"
 * https://github.com/mage2pro/moip/issues/22
 * @used-by \Df\Config\Settings::scope()
 */
function df_is_system_config():bool {return df_action_prefix('adminhtml_system_config');}