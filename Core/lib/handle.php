<?php
/**
 * 2016-08-24 
 * @used-by df_is_checkout() 
 * @used-by df_is_checkout_success()
 * @used-by df_is_login() 
 * @used-by df_is_reg()
 * @param string $name
 * @return bool
 */
function df_handle($name) {return in_array($name, df_handles());}

/**
 * 2015-12-21    
 * @used-by df_handle()
 * @return string[]
 */
function df_handles() {return df_layout()->getUpdate()->getHandles();}

/**
 * 2016-08-24
 * @return bool
 */
function df_is_checkout() {return df_handle('checkout_index_index');}

/**
 * 2017-03-29    
 * @used-by \Df\Payment\Block\Info::_toHtml()
 * @return bool
 */
function df_is_checkout_success() {return df_handle('checkout_onepage_success');}

/**
 * 2016-12-04
 * @return bool
 */
function df_is_login() {return df_handle('customer_account_login');}

/**
 * 2016-12-02
 * @return bool
 */
function df_is_reg() {return df_handle('customer_account_create');}