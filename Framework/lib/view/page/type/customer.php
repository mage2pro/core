<?php
/**
 * 2016-12-04
 * 2017-08-28
 * @todo May be we should use @see df_action() here?
 * @see df_is_checkout_multishipping()
 * @used-by Df\Sso\Css::isAccConfirmation()
 */
function df_is_login():bool {return df_handle('customer_account_login');}

/**
 * 2016-12-02
 * 2017-08-28
 * @todo May be we should use @see df_action() here?
 * @see  df_is_checkout_multishipping()
 */
function df_is_reg():bool {return df_handle('customer_account_create');}