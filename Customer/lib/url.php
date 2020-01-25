<?php
use Magento\Customer\Model\Url;

/**
 * 2016-12-01                             
 * @used-by df_customer_url_h()
 * @used-by \Df\Sso\CustomerReturn::redirectUrl()
 * @return Url
 */
function df_customer_url() {return df_o(Url::class);}

/**                            
 * 2018-09-11    
 * @used-by \Mangoit\MediaclipHub\Controller\Index\RenewMediaclipToken::execute()
 * @return Url
 */
function df_customer_url_h() {return df_customer_url();}