<?php
namespace Df\OAuth\ReturnT;
use Df\Core\Exception as DFE;
use Df\OAuth\App;
use Df\OAuth\FE\Button as B;
/**   
 * 2017-07-11
 * @final Unable to use the PHP «final» keyword here because the class is used as a base for virtual types:
 * 1) \Dfe\Dynamics365\Controller\Adminhtml\OAuth\Index
 *		<virtualType
 *			name='Dfe\Dynamics365\Controller\Adminhtml\Oauth\Index'
 *			type='Df\OAuth\ReturnT\GeneralPurpose'
 *		/>
 * https://github.com/mage2pro/dynamics365/blob/0.2.6/etc/adminhtml/di.xml#L6-L9
 * 2) \Dfe\Salesforce\Controller\Adminhtml\OAuth\Index
 *		<virtualType
 *			name='Dfe\Salesforce\Controller\Adminhtml\Oauth\Index'
 *			type='Df\OAuth\ReturnT\GeneralPurpose'
 *		/>
 * https://github.com/mage2pro/salesforce/blob/0.0.6/etc/adminhtml/di.xml#L6-L9
 */
class GeneralPurpose extends \Df\OAuth\ReturnT {
	/**
	 * 2017-06-27
	 * @override
	 * @see \Df\OAuth\ReturnT::_execute()
	 * @used-by \Df\OAuth\ReturnT::execute()
	 * @throws DFE
	 */
	final protected function _execute() {df_oauth_app(App::state(B::MODULE))->getAndSaveTheRefreshToken();}

	/**
	 * 2017-06-28
	 * «A value included in the request that is also returned in the token response.»
	 * https://docs.microsoft.com/en-us/azure/active-directory/develop/active-directory-protocols-oauth-code#request-an-authorization-code
	 * @override
	 * @see \Df\OAuth\ReturnT::redirectUrl()
	 * @used-by \Df\OAuth\ReturnT::execute()
	 * @return string
	 */
	final protected function redirectUrl() {return App::state(B::URL);}
}