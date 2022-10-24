<?php
namespace Df\Payment\Settings;
/**
 * 2019-01-14
 * @used-by \Dfe\TBCBank\Settings::proxy()
 * @used-by \Dfe\Vantiv\Settings::proxy()
 */
final class Proxy extends \Df\Payment\Settings implements \Df\Config\Settings\IProxy {
	/**
	 * 2019-01-14
	 * @override
	 * @see \Df\Config\Settings\IProxy::host()
	 * @used-by \Df\API\Client::setup()
	 */
	function host():string {return $this->v();}

	/**
	 * 2019-01-14
	 * @override
	 * @see \Df\Config\Settings\IProxy::password()
	 * @used-by \Df\API\Client::setup()
	 */
	function password():string {return $this->p();}
	
	/**
	 * 2019-01-14
	 * @override
	 * @see \Df\Config\Settings\IProxy::port()
	 * @used-by \Df\API\Client::setup()
	 */
	function port():string {return $this->v();}

	/**
	 * 2019-01-14
	 * @override
	 * @see \Df\Config\Settings\IProxy::username()
	 * @used-by \Df\API\Client::setup()
	 */
	function username():string {return $this->v();}

	/**
	 * 2019-01-14
	 * @override
	 * @see \Df\Payment\Settings::prefix()
	 * @used-by \Df\Config\Settings::v()
	 */
	protected function prefix():string {return dfc($this, function() {return parent::prefix() . '/proxy';});}
}