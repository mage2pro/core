<?php
namespace Df\Payment\Settings;
use Df\Payment\Settings as S;
use Magento\Framework\App\ScopeInterface as IScope;
use Magento\Store\Model\Store;
# 2017-10-20
final class _3DS extends \Df\Config\Settings {
	/**
	 * 2017-10-20
	 * @override
	 * @see \Df\Config\Settings::__construct()
	 * @used-by \Dfe\AlphaCommerceHub\Settings\Card::_3ds()
	 * @used-by \Dfe\CheckoutCom\Settings::_3ds()
	 * @used-by \Dfe\Stripe\Settings::_3ds()
	 */
	function __construct(S $s) {$this->_s = $s;}

	/**
	 * 2017-12-12
	 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
	 * @param string|null $countryId
	 * @param int|null $customerId
	 */
	function disable_($countryId, $customerId):bool {return dfc($this, function($countryId, $customerId) {return
		$this->b('enabled') && (
			$this->b('forAll')
			|| ($this->b('forReturning') && !df_customer_is_new($customerId))
			|| $this->countries($countryId)
		)
	;}, func_get_args());}

	/**
	 * 2016-05-13
	 * 2017-10-20 $countryId is null for orders without shipping.
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\Stripe\Init\Action::need3DS()
	 * @param string|null $countryId
	 * @param int|null $customerId
	 */
	function enable_($countryId, $customerId):bool {return dfc($this, function($countryId, $customerId) {return
		$this->b('forAll')
		|| $this->b('forNew') && df_customer_is_new($customerId)
		|| $this->countries($countryId)
	;}, func_get_args());}

	/**
	 * 2017-10-20
	 * @override
	 * @see \Df\Config\Settings::prefix()
	 * @used-by \Df\Config\Settings::v()
	 */
	protected function prefix():string {return "{$this->_s->prefix()}/3ds";}

	/**
	 * 2017-03-27
	 * 2023-07-17
	 * We should not convert `null` to `0` because @see \Magento\Framework\App\Config\ScopeCodeResolver::resolve()
	 * distinguishes between `null` and `0`:
	 * https://github.com/magento/magento2/blob/2.4.7-beta1/lib/internal/Magento/Framework/App/Config/ScopeCodeResolver.php#L34-L65
	 * @override
	 * @see \Df\Config\Settings::scopeDefault()
	 * @used-by \Df\Config\Settings::scope()
	 * @uses \Df\Payment\Settings::scopeDefault()
	 * @return int|null
	 */
	protected function scopeDefault() {return $this->_s->scopeDefault();}

	/**
	 * 2017-12-12
	 * @used-by self::disable_()
	 * @used-by self::enable_()
	 * @param string|null $countryId
	 */
	private function countries($countryId):bool {return
		$this->nwbn('countries', $countryId, 'forShippingDestinations')
		# 2016-05-31
		# Today it seems that the PHP request to freegeoip.net stopped returning any value,
		# whereas it still returns results when the request is sent from the browser.
		# Apparently, freegeoip.net banned my User-Agent?
		# In all cases, we cannot rely on freegeoip.net and risk getting an empty response.
		|| $this->nwbn('countries', df_visitor()->iso2() ?: $countryId, 'forIPs')
	;}

	/**
	 * 2017-10-20
	 * @used-by self::__construct()
	 * @used-by self::prefix()
	 * @var S
	 */
	private $_s;
}