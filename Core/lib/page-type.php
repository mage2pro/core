<?php
/**
 * 2016-08-24
 * 2017-08-28
 * @todo May be we should use @see df_action() here?
 * @see  df_is_checkout_multishipping()
 * @return bool
 */
function df_is_checkout() {return df_handle('checkout_index_index');}

/**
 * 2017-08-24
 * 2017-08-28
 * df_handle_prefix('multishipping_checkout') is wrong here
 * because it does not work before the layout initialization.
 * @used-by \Df\Payment\Block\Info::_toHtml()
 * @used-by \Df\Payment\ConfigProvider::getConfig()
 * @used-by \Df\Payment\Observer\Multishipping::execute()
 * @return bool
 */
function df_is_checkout_multishipping() {return df_action_prefix('multishipping_checkout');}

/**
 * 2017-03-29
 * 2017-08-28
 * @todo May be we should use @see df_action() here?
 * @see  df_is_checkout_multishipping()
 * How to detect the «checkout success» page programmatically in PHP? https://mage2.pro/t/3562
 * @used-by \Df\Payment\Block\Info::_toHtml()
 * @return bool
 */
function df_is_checkout_success() {return df_handle('checkout_onepage_success');}

/**
 * 2016-12-04
 * 2017-08-28
 * @todo May be we should use @see df_action() here?
 * @see  df_is_checkout_multishipping()
 * @return bool
 */
function df_is_login() {return df_handle('customer_account_login');}

/**
 * 2016-12-02
 * 2017-08-28
 * @todo May be we should use @see df_action() here?
 * @see  df_is_checkout_multishipping()
 * @return bool
 */
function df_is_reg() {return df_handle('customer_account_create');}

/**
 * 2017-10-15   
 * @used-by \Df\Config\Settings::scope()
 * @return bool
 */
function df_is_system_config() {return df_handle('adminhtml_system_config_edit');}