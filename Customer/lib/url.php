<?php
use Magento\Customer\Model\Url;
/**                            
 * 2018-09-11    
 * @used-by \Mangoit\MediaclipHub\Controller\Index\RenewMediaclipToken::execute()
 * @return Url
 */
function df_customer_url_h() {return df_o(Url::class);}