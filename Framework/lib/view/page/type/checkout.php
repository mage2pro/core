<?php
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
 * @used-by Df\Payment\ConfigProvider::getConfig()
 * @used-by Df\Payment\ConfigProvider\GlobalT::getConfig()
 * @used-by Df\Shipping\ConfigProvider::getConfig()
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
 * @used-by Df\Payment\Block\Info::_toHtml()
 * @used-by Df\Payment\Observer\Multishipping::execute()
 */
function df_is_checkout_multishipping():bool {return df_action_prefix('multishipping_checkout');}

/**
 * 2017-03-29
 * 2017-08-28
 * @todo May be we should use @see df_action() here?
 * @see  df_is_checkout_multishipping()
 * How to detect the «checkout success» page programmatically in PHP? https://mage2.pro/t/3562
 * @used-by Df\Payment\Block\Info::_toHtml()
 */
function df_is_checkout_success():bool {return df_handle('checkout_onepage_success');}