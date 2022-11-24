<?php
namespace Df\OAuth;
use Magento\Framework\App\ScopeInterface as S;
use Magento\Store\Model\Store;
/**
 * 2017-07-10
 * @see \Dfe\Dynamics365\Settings\General\OAuth
 * @see \Dfe\Salesforce\Settings\General\OAuth
 * @method static Settings s()
 */
abstract class Settings extends \Df\Config\Settings {
	/**
	 * 2017-06-29
	 * @used-by \Df\OAuth\FE\Button::getCommentText()
	 * @used-by \Df\OAuth\FE\Button::getElementHtml()
	 */
	final function authenticatedB():bool {return dfc($this, function() {return !!$this->refreshToken(df_scope());});}

	/**
	 * 2017-04-23
	 * @used-by \Df\OAuth\App::pCommon()
	 */
	final function clientId():string {return $this->v();}

	/**
	 * 2017-04-23
	 * @used-by \Df\OAuth\App::requestToken()
	 */
	final function clientPassword():string {return $this->p();}

	/**
	 * 2017-06-29
	 * 2017-07-02
	 * We do not encrypt the refresh token in the database,
	 * because it is used only with the @see clientPassword(),
	 * which is encrypted in the database.
	 * @see \Df\OAuth\App::requestToken()
	 * @used-by self::authenticatedB()
	 * @used-by \Df\OAuth\App::token()
	 * @param null|string|int|S|Store|array(string, int) $s [optional]
	 * @return string|null
	 */
	final function refreshToken($s = null) {return $this->v(null, $s);}

	/**
	 * 2017-06-29
	 * $scope could be: «default», «websites», or «stores».
	 * $scopeId could be «0».
	 * @used-by \Df\OAuth\App::getAndSaveTheRefreshToken()
	 * @see \Magento\Store\Model\ScopeInterface::SCOPE_STORES
	 * @see \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES
	 */
	final function refreshTokenSave(string $v, string $scope, int $scopeId):void {df_cfg_save(
		"{$this->prefix()}/refreshToken", $v, $scope, $scopeId
	);}
}