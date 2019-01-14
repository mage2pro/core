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
	 * @return string
	 */
	function host();

	/**
	 * 2019-01-14
	 * @used-by \Df\API\Client::setup()
	 * @see \Df\Payment\Settings\Proxy::password()
	 * @return string
	 */
	function password();

	/**
	 * 2019-01-14
	 * @used-by \Df\API\Client::setup()
	 * @see \Df\Payment\Settings\Proxy::port()
	 * @return string
	 */
	function port();

	/**
	 * 2019-01-14
	 * @used-by \Df\API\Client::setup()
	 * @see \Df\Payment\Settings\Proxy::username()
	 * @return string
	 */
	function username();
}