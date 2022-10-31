<?php
use Magento\Framework\App\Config\ScopeConfigInterface as IScopeConfig;
use Magento\Framework\App\ScopeInterface as ScopeA;
use Magento\Framework\App\ScopeResolverPool;
use Magento\Store\Model\ScopeInterface as SS;
use Magento\Store\Model\Store;
/**
 * 2017-06-29
 * 2017-10-22
 * Note 1.
 * The @see \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE constant exists in all the Magento 2 versions:
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Store/Model/ScopeInterface.php#L19
 * Note 2.
 * The @see \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES constant exists in all the Magento 2 versions:
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Store/Model/ScopeInterface.php#L15
 * Note 3.
 * The @see \Magento\Store\Model\ScopeInterface::SCOPE_STORE constant exists in all the Magento 2 versions:
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Store/Model/ScopeInterface.php#L17
 * Note 4.
 * The @see \Magento\Store\Model\ScopeInterface::SCOPE_STORES constant exists in all the Magento 2 versions:
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Store/Model/ScopeInterface.php#L13
 * Note 5.
 * The @see \Magento\Store\Model\ScopeInterface::SCOPE_GROUPS constant is absent in Magento <= 2.2.0:
 * https://github.com/magento/magento2/blob/2.1.9/app/code/Magento/Store/Model/ScopeInterface.php#L8-L21
 * https://github.com/magento/magento2/blob/2.2.0/app/code/Magento/Store/Model/ScopeInterface.php#L18
 * @used-by df_scope_stores()
 * @used-by \Df\Config\Comment::sibling()
 * @used-by \Df\Config\Settings::scope()
 * @used-by \Df\Config\Source::sibling()
 * @used-by \Df\OAuth\Settings::authenticatedB()
 * @used-by \Dfe\Dynamics365\Button::onFormInitialized()
 * @return array(string, int)
 */
function df_scope():array {
	$r = null; /** @var array(string, int) $r */
	foreach ([SS::SCOPE_WEBSITE => SS::SCOPE_WEBSITES, SS::SCOPE_STORE => SS::SCOPE_STORES] as $s => $ss) {
		if (!is_null($id = df_request($s))) { /** @var int|null $id */
			$r = [$ss, $id];
			break;
		}
	}
	/**
	 * 2017-08-10
	 * The same constant is also defined in the following places:
	 * 1) @see \Magento\Config\Block\System\Config\Form::SCOPE_DEFAULT:
	 * 		const SCOPE_DEFAULT = 'default';
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/app/code/Magento/Config/Block/System/Config/Form.php#L25
	 * 2) @see \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT
	 * 		const SCOPE_TYPE_DEFAULT = 'default';
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/lib/internal/Magento/Framework/App/Config/ScopeConfigInterface.php#L16-L19
	 * 2017-10-22
	 * Note 1.
	 * The @see \Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT constant is absent in Magento <= 2.2.0:
	 * https://github.com/magento/magento2/blob/2.1.9/lib/internal/Magento/Framework/App/ScopeInterface.php
	 * https://github.com/magento/magento2/blob/2.2.0/lib/internal/Magento/Framework/App/ScopeInterface.php#L13-L16
	 * «Undefined class constant 'SCOPE_DEFAULT' in mage2pro/core/Config/lib/scope.php on line 36»:
	 * https://github.com/mage2pro/core/issues/39
	 * The bug was introduced at 2017-08-10 in the 2.10.12 release:
	 * https://github.com/mage2pro/core/releases/tag/2.10.12
	 * Note 2.
	 * The @see \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT constant
	 * exists in all the Magento 2 versions:
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/App/Config/ScopeConfigInterface.php#L16-L19
	 * It is not deprecated in Magento 2.2.0:
	 * https://github.com/magento/magento2/blob/2.2.0/lib/internal/Magento/Framework/App/Config/ScopeConfigInterface.php#L16-L19
	 * Note 3.
	 * The @see \Magento\Config\Block\System\Config\Form::SCOPE_DEFAULT constant exists in all the Magento 2 versions:
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Block/System/Config/Form.php#L16
	 * It is not deprecated in Magento 2.2.0:
	 * https://github.com/magento/magento2/blob/2.2.0/app/code/Magento/Config/Block/System/Config/Form.php#L26
	 */
	return $r ?: [IScopeConfig::SCOPE_TYPE_DEFAULT, 0];
}

/**
 * 2015-12-26
 * https://mage2.pro/t/359
 * «Propose to make the @see \Magento\Framework\App\Config\ScopePool::_getScopeCode() public
 * because it is useful to calculate cache keys based on a scope
 * (like @see \Magento\Framework\App\Config\ScopePool::getScope() does)».
 * 2015-12-26
 * I use @see \Magento\Store\Model\ScopeInterface::SCOPE_STORE as the default value for $scopeType
 * for compatibility with @see \Df\Config\Settings::v()
 * https://mage2.pro/t/128
 * https://github.com/magento/magento2/issues/2064
 * 2017-10-22
 * Note 1.
 * The @see \Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT constant is absent in Magento <= 2.2.0:
 * https://github.com/magento/magento2/blob/2.1.9/lib/internal/Magento/Framework/App/ScopeInterface.php
 * https://github.com/magento/magento2/blob/2.2.0/lib/internal/Magento/Framework/App/ScopeInterface.php#L13-L16
 * «Undefined class constant 'SCOPE_DEFAULT' in mage2pro/core/Config/lib/scope.php on line 36»:
 * https://github.com/mage2pro/core/issues/39
 * Note 2.
 * The @see \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT constant
 * exists in all the Magento 2 versions:
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/App/Config/ScopeConfigInterface.php#L16-L19
 * It is not deprecated in Magento 2.2.0:
 * https://github.com/magento/magento2/blob/2.2.0/lib/internal/Magento/Framework/App/Config/ScopeConfigInterface.php#L16-L19
 * Note 3.
 * The @see \Magento\Config\Block\System\Config\Form::SCOPE_DEFAULT constant exists in all the Magento 2 versions:
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Block/System/Config/Form.php#L16
 * It is not deprecated in Magento 2.2.0:
 * https://github.com/magento/magento2/blob/2.2.0/app/code/Magento/Config/Block/System/Config/Form.php#L26
 * Note 4.
 * The @see \Magento\Store\Model\ScopeInterface::SCOPE_STORE constant exists in all the Magento 2 versions:
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Store/Model/ScopeInterface.php#L17
 * @used-by df_store_code()
 * @used-by \Df\Config\Settings::_a()
 * @used-by \Df\Config\Settings::_font()
 * @used-by \Df\Config\Settings::_matrix()
 * @param null|string|int|ScopeA|Store $s [optional]
 * @param string $type [optional]
 */
function df_scope_code($s = null, $type = SS::SCOPE_STORE):string {
	if (($s === null || is_numeric($s)) && $type !== IScopeConfig::SCOPE_TYPE_DEFAULT) {
		$s = df_scope_resolver_pool()->get($type)->getScope($s);
	}
	return $s instanceof ScopeA ? $s->getCode() : $s;
}

/**
 * 2016-12-16
 * @used-by df_scope_code()
 */
function df_scope_resolver_pool():ScopeResolverPool {return df_o(ScopeResolverPool::class);}

/**
 * 2017-08-10
 * 2017-10-22
 * Note 1.
 * The @see \Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT constant is absent in Magento <= 2.2.0:
 * https://github.com/magento/magento2/blob/2.1.9/lib/internal/Magento/Framework/App/ScopeInterface.php
 * https://github.com/magento/magento2/blob/2.2.0/lib/internal/Magento/Framework/App/ScopeInterface.php#L13-L16
 * «Undefined class constant 'SCOPE_DEFAULT' in mage2pro/core/Config/lib/scope.php on line 36»:
 * https://github.com/mage2pro/core/issues/39
 * Note 2.
 * The @see \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT constant
 * exists in all the Magento 2 versions:
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/App/Config/ScopeConfigInterface.php#L16-L19
 * It is not deprecated in Magento 2.2.0:
 * https://github.com/magento/magento2/blob/2.2.0/lib/internal/Magento/Framework/App/Config/ScopeConfigInterface.php#L16-L19
 * Note 3.
 * The @see \Magento\Config\Block\System\Config\Form::SCOPE_DEFAULT constant exists in all the Magento 2 versions:
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Block/System/Config/Form.php#L16
 * It is not deprecated in Magento 2.2.0:
 * https://github.com/magento/magento2/blob/2.2.0/app/code/Magento/Config/Block/System/Config/Form.php#L26
 * Note 4.
 * The @see \Magento\Store\Model\ScopeInterface::SCOPE_STORES constant exists in all the Magento 2 versions:
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Store/Model/ScopeInterface.php#L13
 * @return Store[]
 */
function df_scope_stores():array {
	# 2020-03-02, 2022-10-31
	# 1) Symmetric array destructuring requires PHP ≥ 7.1:
	#		[$a, $b] = [1, 2];
	# https://github.com/mage2pro/core/issues/96#issuecomment-593392100
	# We should support PHP 7.0.
	# https://3v4l.org/3O92j
	# https://www.php.net/manual/migration71.new-features.php#migration71.new-features.symmetric-array-destructuring
	# https://stackoverflow.com/a/28233499
	list($t, $id) = df_scope(); /** @var int $id */ /** @var string $t */
	return IScopeConfig::SCOPE_TYPE_DEFAULT === $t ? df_stores() : (
		SS::SCOPE_STORES === $t ? [df_store($id)] : df_store_m()->getWebsite($id)->getStores()
	);
}