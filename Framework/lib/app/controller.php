<?php
use Magento\Framework\App\Action\Action as Controller;
use Magento\Framework\App\ActionFlag as AF;
use Magento\Framework\Authorization as Auth;
use Magento\Framework\AuthorizationInterface as IAuth;

/**
 * 2019-10-23
 * I use the `_f` suffix to distinguish @see \Magento\Framework\Authorization
 * and @see \Magento\Backend\Model\Auth
 * @used-by \PPCs\Core\Plugin\Catalog\Controller\Adminhtml\Product::aroundDispatch()
 * @return IAuth|Auth
 */
function df_auth_f() {return df_o(IAuth::class);}

/**
 * 2019-11-21
 * @used-by \RWCandy\Captcha\Observer\CustomerAccountCreatePost::execute()
 */
function df_no_dispatch():void {
	$af = df_o(AF::class); /** @var AF $af */
	$af->set('', Controller::FLAG_NO_DISPATCH, true);
}