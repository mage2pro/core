<?php
namespace Df\Config\Settings;
/**
 * 2019-01-14
 * @see \Df\Payment\Settings\Proxy
 * @used-by \Df\API\Client::proxy()
 */
interface IProxy {
	/**
	 * 2019-01-14
	 * @used-by \Df\API\Client::setup()
	 * @see \Df\Payment\Settings\Proxy::host()
	 */
	function host():string;

	/**
	 * 2019-01-14
	 * @used-by \Df\API\Client::setup()
	 * @see \Df\Payment\Settings\Proxy::password()
	 */
	function password():string;

	/**
	 * 2019-01-14
	 * @used-by \Df\API\Client::setup()
	 * @see \Df\Payment\Settings\Proxy::port()
	 */
	function port():string;

	/**
	 * 2019-01-14
	 * @used-by \Df\API\Client::setup()
	 * @see \Df\Payment\Settings\Proxy::username()
	 */
	function username():string;
}