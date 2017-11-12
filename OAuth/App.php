<?php
namespace Df\OAuth;
use Df\Core\Exception as DFE;
use Df\OAuth\FE\Button as B;
use Zend_Http_Client as C;
/**
 * 2017-07-10
 * @see \Dfe\Dynamics365\OAuth\App
 * @see \Dfe\Salesforce\OAuth\App
 */
abstract class App {
	/**
	 * 2017-07-10
	 * @used-by getAndSaveTheRefreshToken()
	 * @used-by pCommon()
	 * @used-by requestToken()
	 * @used-by token()
	 * @used-by \Df\OAuth\FE\Button::s()
	 * @see \Dfe\Dynamics365\OAuth\App::ss()
	 * @see \Dfe\Salesforce\OAuth\App::ss()
	 * @return Settings
	 */
	abstract function ss();

	/**
	 * 2017-07-10
	 * @used-by \Df\OAuth\FE\Button::onFormInitialized()
	 * @see \Dfe\Dynamics365\OAuth\App::urlAuth()
	 * @see \Dfe\Salesforce\OAuth\App::urlAuth()
	 * @return string
	 */
	abstract function urlAuth();

	/**
	 * 2017-06-30
	 * «OAuth authorization endpoints» https://msdn.microsoft.com/en-us/library/dn531009.aspx#bkmk_oauthurl
	 * @used-by requestToken()
	 * @see \Dfe\Dynamics365\OAuth\App::urlToken()
	 * @see \Dfe\Salesforce\OAuth\App::urlToken()
	 * @return string
	 */
	abstract protected function urlToken();

	/**
	 * 2017-06-29 Dynamics 365
	 * Note 1.
	 * «An OAuth 2.0 refresh token.
	 * The app can use this token to acquire additional access tokens
	 * after the current access token expires.
	 * Refresh tokens are long-lived,
	 * and can be used to retain access to resources for extended periods of time.»
	 * https://docs.microsoft.com/en-us/azure/active-directory/develop/active-directory-protocols-oauth-code#successful-response-1
	 * Note 2.
	 * «Refresh tokens do not have specified lifetimes.
	 * Typically, the lifetimes of refresh tokens are relatively long.
	 * However, in some cases, refresh tokens expire, are revoked,
	 * or lack sufficient privileges for the desired action.
	 * Your application needs to expect and handle errors
	 * returned by the token issuance endpoint correctly.»
	 * https://docs.microsoft.com/en-us/azure/active-directory/develop/active-directory-protocols-oauth-code#refreshing-the-access-tokens
	 * Note 3 (mine). It is string of 824 caracters.
	 * @used-by \Df\OAuth\ReturnT\GeneralPurpose::_execute()
	 * @var string $refreshToken
	 * @throws DFE
	 */
	final function getAndSaveTheRefreshToken() {
		/** @var array(string => string) $r */
		$this->validateResponse($r = df_request());
		/**
		 * 2017-06-28 Dynamics 365
		 * «The authorization code that the application requested.
		 * The application can use the authorization code
		 * to request an access token for the target resource.»
		 * «Successful response»: https://docs.microsoft.com/en-us/azure/active-directory/develop/active-directory-protocols-oauth-code#successful-response
		 * Required.
		 * My note: a string of 611 caracters.
		 * 2017-07-11 Salesforce
		 * «Authorization code the consumer must use to obtain the access and refresh tokens».
		 * https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/intro_understanding_web_server_oauth_flow.htm#code_parameter_description
		 * @var string $code
		 */
		$code = $r['code'];
		$this->ss()->refreshTokenSave($this->requestToken([
			/**
			 * 2017-06-28 Dynamics 365
			 * «The `authorization_code` that you acquired in the previous section».
			 * «Use the authorization code to request an access token»: https://docs.microsoft.com/en-us/azure/active-directory/develop/active-directory-protocols-oauth-code#use-the-authorization-code-to-request-an-access-token
			 * Required.
			 * 2017-07-11 Salesforce:
			 * «Authorization code the consumer must use to obtain the access and refresh tokens».
			 * https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/intro_understanding_web_server_oauth_flow.htm#d15809e462
			 */
			'code' => $code
			// 2017-06-28 Dynamics 365: «Must be `authorization_code` for the authorization code flow».
			// Required.
			// 2017-07-11 Salesforce: «Value must be `authorization_code` for this flow».
			// https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/intro_understanding_web_server_oauth_flow.htm#d15809e462
			,'grant_type' => 'authorization_code'
		])['refresh_token'], ...(self::state(B::SCOPE)));
		/**
		 * 2017-07-12 Salesforce:
		 * A successfull response looks like:
		 *	{
		 *		"access_token": "<a string of 112 characters>",
		 *		"id": "https://login.salesforce.com/id/00D0Y000000ZB09UAG/0050Y000000Ik1DQAS",
		 *		"instance_url": "https://mage2pro-dev-ed.my.salesforce.com",
		 *		"issued_at": "1499829975543",
		 *		"refresh_token": "<a string of 87 characters>",
		 *		"scope": "refresh_token web api",
		 *		"signature": "z8U5tqehCzuiQfmH8JNfn+7pn4lzP2VaeOfBYBxDDkg=",
		 *		"token_type": "Bearer"
		 *	}
		 */
		// 2017-06-30 It is required, because the backend settings are cached.
		df_cache_clean();
	}

	/**
	 * 2017-06-28 Dynamics 365:
	 * «Request an authorization code -
	 * Authorize access to web applications using OAuth 2.0 and Azure Active Directory»
	 * https://docs.microsoft.com/en-us/azure/active-directory/develop/active-directory-protocols-oauth-code#request-an-authorization-code
	 * 2017-07-10 Salesforce:
	 * «Understanding the Web Server OAuth Authentication Flow - Force.com REST API Developer Guide»
	 * https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/intro_understanding_web_server_oauth_flow.htm#d15809e72
	 * @used-by requestToken()
	 * @used-by \Df\OAuth\FE\Button::onFormInitialized()
	 * @see \Dfe\Dynamics365\OAuth\App::pCommon()
	 * @return array(string => string)
	 */
	function pCommon() {return df_clean([
		/**
		 * 2017-06-27 Dynamics 365:
		 * «The Application Id assigned to your app when you registered it with Azure AD.
		 * You can find this in the Azure Portal.
		 * Click `Active Directory`, click the directory, choose the application, and click `Configure`.»
		 * Required.
		 * «How to grant Magento 2 the permissions to access the Dynamics 365 web API?»
		 * https://mage2.pro/t/3825
		 * 2017-07-10 Salesforce: «The `Consumer Key` from the connected app definition».
		 * https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/intro_understanding_web_server_oauth_flow.htm#client_id_parameter_description
		 * https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/intro_understanding_web_server_oauth_flow.htm#d15809e462
		 */
		'client_id' => $this->ss()->clientId()
		/**
		 * 2017-06-27 Dynamics 365:
		 * Note 1.
		 * «The `redirect_uri` of your app,
		 * where authentication responses can be sent and received by your app.
		 * It must exactly match one of the `redirect_uris` you registered in the portal,
		 * except it must be url encoded.»
		 * Recommended.
		 * Note 2.
		 * It uses the same algorithm as in @see \Df\Sso\FE\CustomerReturn::url()
		 * https://github.com/mage2pro/dynamics365/blob/0.0.5/etc/adminhtml/system.xml#L102
		 * https://github.com/mage2pro/dynamics365/blob/0.0.5/etc/adminhtml/system.xml#L105
		 * https://github.com/mage2pro/core/blob/2.7.23/Sso/FE/CustomerReturn.php#L28-L30
		 * 2017-07-10 Salesforce: «The `Callback URL` from the connected app definition».
		 * https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/intro_understanding_web_server_oauth_flow.htm#redirect_url_parameter_description
		 * https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/intro_understanding_web_server_oauth_flow.htm#d15809e462
		 */
		,'redirect_uri' => df_url_backend_ns(df_route($this, 'oauth', true))
	]);}

	/**
	 * 2017-06-29
	 * Note 1.
	 * «The requested access token.
	 * The app can use this token to authenticate to the secured resource, such as a web API.»
	 * https://docs.microsoft.com/en-us/azure/active-directory/develop/active-directory-protocols-oauth-code#successful-response-1
	 * Note 2.
	 * «Access Tokens are short-lived
	 * and must be refreshed after they expire to continue accessing resources.
	 * You can refresh the access_token by submitting another POST request to the `/token` endpoint,
	 * but this time providing the `refresh_token` instead of the `code`.»
	 * https://docs.microsoft.com/en-us/azure/active-directory/develop/active-directory-protocols-oauth-code#refreshing-the-access-tokens
	 * Note 3 (mine). It is string of 446 caracters.
	 * Note 4.
	 * «Azure AD returns an access token upon a successful response.
	 * To minimize network calls from the client application and their associated latency,
	 * the client application should cache access tokens for the token lifetime
	 * that is specified in the OAuth 2.0 response.
	 * To determine the token lifetime, use either the `expires_in` or `expires_on` parameter values.
	 * If a web API resource returns an `invalid_token` error code,
	 * this might indicate that the resource has determined that the token is expired.
	 * If the client and resource clock times are different (known as a "time skew"),
	 * the resource might consider the token to be expired
	 * before the token is cleared from the client cache.
	 * If this occurs, clear the token from the cache, even if it is still within its calculated lifetime.»
	 * https://docs.microsoft.com/en-us/azure/active-directory/develop/active-directory-protocols-oauth-code#successful-response-1
	 *
	 * 2017-07-11
	 * Salesforce
	 * «Understanding the OAuth Refresh Token Process» - «Force.com REST API Developer Guide»
	 * https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/intro_understanding_refresh_token_oauth.htm#topic-title
	 *
	 * @used-by \Dfe\Dynamics365\API\Client::headers()
	 * @used-by \Dfe\Salesforce\API\Client::headers()
	 * @return string
	 * @throws DFE
	 */
	final function token() {
		// 2017-07-11 Each descendant class has its own set of the static variables: https://3v4l.org/3GihR
		/** @var string|null $r */
		static $r;
		/** @var int $expiration */
		static $expiration;
		if ($r && time() > $expiration) {
			$r = null;
		}
		if (!$r) {
			/** @var array(string => mixed) $a */
			$a = $this->requestToken([
				/**
				 * 2017-07-11
				 * Dynamics 365: «Value must be `refresh_token`».
				 * https://docs.microsoft.com/en-us/azure/active-directory/develop/active-directory-protocols-oauth-code#refreshing-the-access-tokens
				 * Salesforce: «Value must be `refresh_token`».
				 * https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/intro_understanding_refresh_token_oauth.htm#d13867e63
				 */
				'grant_type' => 'refresh_token'
				// 2017-07-11 Salesforce: «The refresh token the client application already received».
				// https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/intro_understanding_refresh_token_oauth.htm#d13867e63
				,'refresh_token' => $this->ss()->refreshToken()
			]);
			$r = $a['access_token'];
			if ($k = $this->kExpiration()) {
				$expiration = time() + round(0.8 * $a[$k]);
			}
		}
		return $r;
	}

	/**
	 * 2017-07-11
	 * @used-by token()
	 * @see \Dfe\Dynamics365\Button::kExpiration()
	 * @return string|null
	 */
	protected function kExpiration() {return null;}

	/**
	 * 2017-06-30
	 * @used-by getAndSaveTheRefreshToken()
	 * @used-by token()
	 * @param array(string => string) $key
	 * @return array(string => mixed)
	 * @throws DFE
	 */
	private function requestToken(array $key) {
		$s = $this->ss();
		// 2017-06-28
		// «Now that you've acquired an authorization code and have been granted permission by the user,
		// you can redeem the code for an access token to the desired resource,
		// by sending a POST request to the `/token` endpoint.»
		// «Use the authorization code to request an access token»:
		// https://docs.microsoft.com/en-us/azure/active-directory/develop/active-directory-protocols-oauth-code#use-the-authorization-code-to-request-an-access-token
		$c = df_zf_http($this->urlToken())
			->setHeaders(['accept' => 'application/json'])
			->setMethod(C::POST)
			->setParameterPost($this->pCommon() + $key + ['client_secret' => $s->clientPassword()])
		; /** @var C $c */
		/** @var array(string => mixed) $r */
		$this->validateResponse($r = df_json_decode($c->request()->getBody()));
		return $r;
	}

	/**
	 * 2017-06-28 Dynamics 365
	 * A successful response looks like:
	 *	{
	 *		"token_type": "Bearer",
	 *		"scope": "user_impersonation",
	 *		"expires_in": "3600",
	 *		"ext_expires_in": "0",
	 *		"expires_on": "1498666110",
	 *		"not_before": "1498662210",
	 *		"resource": "https://mage2.crm.dynamics.com",
	 *		"access_token": <a string of 446 caracters>,
	 *		"refresh_token": <a string of 824 caracters>,
	 *		"id_token": <a string of 697 caracters>
	 *	}
	 * An error response looks like:
	 *	{
	 *		"error": "invalid_resource",
	 *		"error_description": "AADSTS50001: Resource identifier is not provided.\r\nTrace ID: b4546deb-82b7-410a-b79d-191380244200\r\nCorrelation ID: 0dcc3112-7b8d-4010-9e06-60f046132fea\r\nTimestamp: 2017-06-28 14:35:58Z",
	 *		"error_codes": [
	 *			50001
	 *		],
	 *		"timestamp": "2017-06-28 14:35:58Z",
	 *		"trace_id": "b4546deb-82b7-410a-b79d-191380244200",
	 *		"correlation_id": "0dcc3112-7b8d-4010-9e06-60f046132fea"
	 *	}
	 *
	 * 2017-07-12 Salesforce:
	 * A successfull response looks like:
	 *	{
	 *		"access_token": "<a string of 112 characters>",
	 *		"id": "https://login.salesforce.com/id/00D0Y000000ZB09UAG/0050Y000000Ik1DQAS",
	 *		"instance_url": "https://mage2pro-dev-ed.my.salesforce.com",
	 *		"issued_at": "1499829975543",
	 *		"refresh_token": "<a string of 87 characters>",
	 *		"scope": "refresh_token web api",
	 *		"signature": "z8U5tqehCzuiQfmH8JNfn+7pn4lzP2VaeOfBYBxDDkg=",
	 *		"token_type": "Bearer"
	 *	}
	 * An error response looks like:
	 *	{
	 *		"error": "invalid_grant",
	 *		"error_description": "invalid authorization code"
	 *	}
	 *
	 * @override
	 * @used-by getAndSaveTheRefreshToken()
	 * @used-by requestToken()
	 * @param array(string => mixed) $r
	 * @throws DFE
	 */
	private function validateResponse(array $r) {
		if ($error = dfa($r, 'error')) {
			df_error_html("<b>%s: $error</b> (%s)", df_api_name($this), nl2br(dfa($r, 'error_description')));
		}
	}

	/**
	 * 2017-06-29
	 * 2017-07-11
	 * Dynamics 365
	 * «If a state parameter is included in the request, the same value should appear in the response.
	 * It's a good practice for the application
	 * to verify that the state values in the request and response are identical before using the response.
	 * This helps to detect Cross-Site Request Forgery (CSRF) attacks against the client.»
	 * https://docs.microsoft.com/en-us/azure/active-directory/develop/active-directory-protocols-oauth-code#successful-response
	 * Salesforce «The state value that was passed in as part of the initial request, if applicable».
	 * https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/intro_understanding_web_server_oauth_flow.htm#state_return_parameter_description
	 * @used-by getAndSaveTheRefreshToken()
	 * @used-by \Df\OAuth\ReturnT\GeneralPurpose::_execute()
	 * @used-by \Df\OAuth\ReturnT\GeneralPurpose::redirectUrl()
	 * @param string $k
	 * @return string|mixed
	 */
	final static function state($k) {return dfa(
		dfcf(function() {return df_json_decode(df_request('state'));}, [], [], true, 1), $k
	);}
}