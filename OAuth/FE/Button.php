<?php
namespace Df\OAuth\FE;
use Df\Framework\Form\ElementI;
use Df\OAuth\App;
use Df\OAuth\Settings;
use Magento\Backend\Block\Widget\Button as W;
use Magento\Config\Model\Config\CommentInterface as IComment;
use Magento\Framework\Data\Form\Element\AbstractElement as AE;
/**
 * 2017-07-10
 * @see \Dfe\Dynamics365\Button
 * @see \Dfe\Salesforce\Button
 */
abstract class Button extends AE implements ElementI, IComment {
	/**
	 * 2017-06-29
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * «How to implement a dynamically generated comment for a backend configuration field?»
	 * https://mage2.pro/t/4076
	 * @override
	 * @see IComment::getCommentText()
	 * @used-by \Magento\Config\Model\Config\Structure\Element\Field::getComment():
	 *		public function getComment($currentValue = '') {
	 *			$comment = '';
	 *			if (isset($this->_data['comment']) && $this->_data['comment']) {
	 *				if (is_array($this->_data['comment'])) {
	 *					if (isset($this->_data['comment']['model'])) {
	 *						$model = $this->_commentFactory->create($this->_data['comment']['model']);
	 *						$comment = $model->getCommentText($currentValue);
	 *					}
	 *				}
	 *				else {
	 *					$comment = parent::getComment();
	 *				}
	 *			}
	 *			return $comment;
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/app/code/Magento/Config/Model/Config/Structure/Element/Field.php#L106-L126
	 * @param string $v
	 * @return string
	 */
	function getCommentText($v) {return (string)__($this->s()->authenticatedB()
		? "<b>Your Magento instance is <span class='df-ok'>successfully authenticated</span> to your %1 instance.</b>"
		: "<b>You <span class='df-warning'>need to authenticate</span> your Magento instance to your %1 instance.</b>"
	,df_api_name($this->m()));}

	/**
	 * 2017-06-27
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getElementHtml()
	 * @used-by \Magento\Framework\Data\Form\Element\AbstractElement::getDefaultHtml():
	 *		public function getDefaultHtml() {
	 *			$html = $this->getData('default_html');
	 *			if ($html === null) {
	 *				$html = $this->getNoSpan() === true ? '' : '<div class="admin__field">' . "\n";
	 *				$html .= $this->getLabelHtml();
	 *				$html .= $this->getElementHtml();
	 *				$html .= $this->getNoSpan() === true ? '' : '</div>' . "\n";
	 *			}
	 *			return $html;
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/lib/internal/Magento/Framework/Data/Form/Element/AbstractElement.php#L426-L441
	 * @return string
	 */
	function getElementHtml() {return df_block(W::class, [
		'id' => $this->getHtmlId()
		,'label' => __($this->s()->authenticatedB() ? 'Re-authenticate' : 'Authenticate')
	])->toHtml();}

	/**
	 * 2017-07-10
	 * @used-by onFormInitialized()
	 * @see \Dfe\Dynamics365\Button::pExtra()
	 * @see \Dfe\Salesforce\Button::pExtra()
	 * @return array(string => mixed)
	 */
	protected function pExtra() {return [];}

	/**
	 * 2017-06-27
	 * 2017-06-28 Dynamics 365:
	 * «Request an authorization code -
	 * Authorize access to web applications using OAuth 2.0 and Azure Active Directory»
	 * https://docs.microsoft.com/en-us/azure/active-directory/develop/active-directory-protocols-oauth-code#request-an-authorization-code
	 * 2017-07-10 Salesforce:
	 * «Understanding the Web Server OAuth Authentication Flow - Force.com REST API Developer Guide»
	 * https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/intro_understanding_web_server_oauth_flow.htm#d15809e72
	 * @override
	 * @see \Df\Framework\Form\ElementI::onFormInitialized()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 */
	final function onFormInitialized() {
		/**
		 * 2017-06-27
		 * This code removes the «[store view]» sublabel, similar to
		 * @see \Magento\MediaStorage\Block\System\Config\System\Storage\Media\Synchronize::render()
		 */
		$this->_data = dfa_unset($this->_data, 'scope', 'can_use_website_value', 'can_use_default_value');
		// 2017-06-27
		// OpenID Connect protocol: https://docs.microsoft.com/en-us/azure/active-directory/develop/active-directory-protocols-openid-connect-code#send-the-sign-in-request
		// OAuth 2.0 auth code grant: https://docs.microsoft.com/en-us/azure/active-directory/develop/active-directory-protocols-oauth-code#request-an-authorization-code
		// «common» is a special tenant identifier value to request a tenant-independent token:
		$url = "{$this->app()->urlAuth()}?" . http_build_query(df_clean([
			/**
			 * 2017-06-27 Dynamics 365:
			 * «Can be used to pre-fill the username/email address field of the sign-in page for the user,
			 * if you know their username ahead of time.
			 * Often apps use this parameter during reauthentication,
			 * having already extracted the username from a previous sign-in
			 * using the preferred_username claim.»
			 * Optional.
			 * 2017-07-10 Salesforce:
			 * https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/intro_understanding_web_server_oauth_flow.htm#login_hint_parameter_description
			 * «Provides a valid username value to pre-populate the login page with the username.
			 * For example: login_hint=username@company.com.
			 * If a user already has an active session in the browser,
			 * then the login_hint parameter does nothing; the active user session continues.»
			 */
			//'login_hint' => df_backend_user()->getEmail()
			/**
			 * 2017-06-27 Dynamics 365:
			 * OAuth 2.0 auth code grant:
			 * «Indicate the type of user interaction that is required.
			 * Valid values are:
			 * 1) `login`: The user should be prompted to reauthenticate.
			 * 2) `consent`: User consent has been granted, but needs to be updated.
			 * The user should be prompted to consent.
			 * 3) `admin_consent`: An administrator should be prompted to consent
			 * on behalf of all users in their organization.»
			 *
			 * 2017-07-10 Salesforce:
			 * «Specifies how the authorization server prompts the user for reauthentication and reapproval.
			 * This parameter is optional.
			 * The only values Salesforce supports are:
			 * 1) `login`: The authorization server must prompt the user
			 * for reauthentication, forcing the user to log in again.
			 * 2) `consent`: The authorization server must prompt the user
			 * for reapproval before returning information to the client.
			 * It is valid to pass both values, separated by a space,
			 * to require the user to both log in and reauthorize. For example: ?prompt=login%20consent».
			 */
			'prompt' => 'consent'
			// 2017-06-27 Dynamics 365: «Must include `code` for the authorization code flow». Required.
			// 2017-07-10 Salesforce: «Must be `code` for this authentication flow».
			,'response_type' => 'code'
			/**
			 * 2017-06-27 Dynamics 365:
			 * «A value included in the request that is also returned in the token response.
			 * A randomly generated unique value is typically used
			 * for preventing cross-site request forgery attacks.
			 * The state is also used to encode information about the user's state in the app
			 * before the authentication request occurred, such as the page or view they were on.»
			 * Recommended.
			 * 2017-07-10 Salesforce:
			 * «Specifies any additional URL-encoded state data
			 * to be returned in the callback URL after approval.»
			 * https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/intro_understanding_web_server_oauth_flow.htm#state_parameter_description
			 */
            ,'state' => df_json_encode([
				/** 2017-07-11 @used-by \Df\OAuth\ReturnT\GeneralPurpose::_execute() */
            	self::MODULE => $this->m()
				/**
				 * 2017-06-29
				 * We will store the refresh token in this configuration scope.
				 * @used-by \Df\OAuth\App::getAndSaveTheRefreshToken()
				 */
            	,self::SCOPE => df_scope()
				/**
				 * 2017-06-29
				 * The page we need to return to after the authentication.
				 * @used-by \Df\OAuth\ReturnT\GeneralPurpose::redirectUrl()
				 */
            	,self::URL => df_current_url()
			])
		] + $this->app()->pCommon() + $this->pExtra()));
		df_fe_init($this, __CLASS__, [], ['url' => $url]);
	}

	/**
	 * 2017-07-10
	 * @used-by getCommentText()
	 * @used-by getElementHtml()
	 * @used-by \Dfe\Dynamics365\Button::url()
	 * @return Settings
	 */
	final protected function s() {return $this->app()->ss();}

	/**
	 * 2017-07-10
	 * @used-by onFormInitialized()
	 * @used-by s()
	 * @return App
	 */
	private function app() {return df_oauth_app($this->m());}

	/**
	 * 2017-07-11
	 * @used-by app()
	 * @used-by getCommentText()
	 * @used-by onFormInitialized()
	 * @return string
	 */
	private function m() {return dfc($this, function() {return df_module_name_c($this);});}

	/**
	 * 2017-07-11
	 * @used-by onFormInitialized()
	 * @used-by \Df\OAuth\ReturnT\GeneralPurpose::_execute()
	 */
	const MODULE = 'module';
	/**
	 * 2017-06-29
	 * @used-by onFormInitialized()
	 * @used-by \Df\OAuth\App::getAndSaveTheRefreshToken()
	 */
	const SCOPE = 'scope';
	/**
	 * 2017-06-29
	 * @used-by onFormInitialized()
	 * @used-by \Df\OAuth\ReturnT\GeneralPurpose::redirectUrl()
	 */
	const URL = 'url';
}