<?php
use Magento\Customer\Model\Url;

/**
 * 2016-12-01                             
 * @used-by df_customer_url_h()
 * @used-by \Df\Sso\CustomerReturn::redirectUrl()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\RenewMediaclipToken::execute()
 */
function df_customer_url():Url {return df_o(Url::class);}