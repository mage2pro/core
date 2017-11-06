<?php
namespace Df\Payment\Settings;
use Df\Payment\Settings as S;
use Magento\Framework\App\ScopeInterface as IScope;
use Magento\Store\Model\Store;
// 2017-10-20
final class _3DS extends \Df\Config\Settings {
	/**
	 * 2017-10-20
	 * @used-by \Dfe\AlphaCommerceHub\Settings::_3ds()
	 * @used-by \Dfe\CheckoutCom\Settings::_3ds()
	 * @used-by \Dfe\Stripe\Settings::_3ds()
	 * @param S $s
	 */
	function __construct(S $s) {$this->_s = $s;}

	/**
	 * 2016-05-13
	 * 2017-10-20 $countryId is null for orders without shipping.
	 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
	 * @used-by \Dfe\CheckoutCom\Charge::_build()
	 * @used-by \Dfe\Stripe\Init\Action::redirectUrl()
	 * @param string|null $countryId
	 * @param int|null $customerId
	 * @return bool
	 */
	function enable_($countryId, $customerId) {return dfc($this, function($countryId, $customerId) {return
		$this->b('forAll')
		|| $this->b('forNew') && df_customer_is_new($customerId)
		|| $this->nwbn('countries', $countryId, 'forShippingDestinations')
		// 2016-05-31
		// Today it seems that the PHP request to freegeoip.net stopped returning any value,
		// whereas it still returns results when the request is sent from the browser.
		// Apparently, freegeoip.net banned my User-Agent?
		// In all cases, we cannot rely on freegeoip.net and risk getting an empty response.
		|| $this->nwbn('countries', df_visitor()->iso2() ?: $countryId, 'forIPs')
	;}, func_get_args());}

	/**
	 * 2017-10-20
	 * @override
	 * @see \Df\Config\Settings::prefix()
	 * @used-by \Df\Config\Settings::v()
	 * @see \Dfe\Moip\Settings\Boleto::prefix()
	 * @return string
	 */
	protected function prefix() {return "{$this->_s->prefix()}/3ds";}

	/**
	 * 2017-03-27
	 * @override
	 * @see \Df\Config\Settings::scopeDefault()
	 * @used-by \Df\Config\Settings::scope()
	 * @return int|IScope|Store|null|string
	 */
	protected function scopeDefault() {return $this->_s->scopeDefault();}

	/**
	 * 2017-10-20
	 * @used-by __construct()
	 * @used-by prefix()
	 * @var S
	 */
	private $_s;
}