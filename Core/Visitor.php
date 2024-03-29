<?php
namespace Df\Core;
final class Visitor {
	/**
	 * На английском языке. Например: «Moscow».
	 * @used-by \Dfe\TwoCheckout\Address::city()
	 * @return string|null
	 */
	function city() {return $this->r('city');}

	/**
	 * На английском языке. Например: «Russia».
	 * 2022-10-29 @deprecated It is unused.
	 */
	function countryName():string {return $this->r('country_name');}

	/**
	 * @used-by \Df\Payment\Settings\_3DS::countries()
	 * @used-by \Dfe\TwoCheckout\Address::countryIso3()
	 * @used-by \Frugue\Shipping\Header::_toHtml()
	 * @used-by \Frugue\Store\Plugin\Framework\App\FrontControllerInterface::aroundDispatch()
	 * @return string|null
	 */
	function iso2() {return $this->r('country_code');}

	/**
	 * Например: «55.752».
	 * 2022-10-29 @deprecated It is unused.
	 * @return string|null
	 */
	function latitude() {return $this->r('latitude');}

	/**
	 * Например: «37.616».
	 * 2022-10-29 @deprecated It is unused.
	 * @return string|null
	 */
	function longitude() {return $this->r('longitude');}

	/**
	 * Например: «101194».
	 * @used-by \Dfe\TwoCheckout\Address::postcode()
	 * @return string|null
	 */
	function postCode() {return $this->r('zip_code');}

	/**
	 * Например: «MOW».
	 * 2022-10-29 @deprecated It is unused.
	 * @return string|null
	 */
	function regionCode() {return $this->r('region_code');}

	/**
	 * На английском языке. Например: «Moscow».
	 * @used-by \Dfe\TwoCheckout\Address::region()
	 * @return string|null
	 */
	function regionName() {return $this->r('region_name');}

	/**
	 * Например: «Europe/Moscow».
	 * 2022-10-29 @deprecated It is unused.
	 * @return string|null
	 */
	function timeZone() {return $this->r('time_zone');}

	/**
	 * 2016-05-31
	 * 1) Сегодня заметил, что при запросе из PHP freegeoip.net перестал возвращать мне значение,
	 * а при запросе из браузера — по прежнему возвращает.
	 * Видимо, freegeoip.net забанил мой User-Agent?
	 * В любом случае, нельзя полагаться, что freegeoip.net вернёт непустой ответ.
	 * 2) Стандартное время ожидание ответа сервера задаётся опцией default_socket_timeout:
	 * https://php.net/manual/filesystem.configuration.php#ini.default-socket-timeout
	 * Её значение по-умолчанию равно 60 секундам.
	 * Конечно, при оформлении заказа негоже заставлять покупателя ждать 60 секунд
	 * только ради узнавания его страны вызовом @see df_visitor(),
	 * поэтому уменьшил время ожидания ответа до 5 секунд.
	 * 2018-06-18 "Update `freegeoip.net` to `freegeoip.app`": https://github.com/mage2pro/core/issues/76
	 * 2019-07-11
	 * 1) "Replace the discontinued geolocation service "freegeoip.net" / "freegeoip.app"
	 * with another one": https://github.com/mage2pro/core/issues/87
	 * 2) "How did I install freegeoip to my server?": https://df.tips/t/930
	 * 2020-01-17
	 * 1) Currently, `http://geoip.mage2.pro/json/` works only with an exactly specified IP address, e.g.:
	 * `http://geoip.mage2.pro/json/5.9.188.84`:
	 * https://github.com/mage2pro/core/issues/93#issuecomment-575509642
	 * It is enough for us here.
	 * 2) @see https://github.com/mage2pro/core/blob/5.6.6/Checkout/view/frontend/web/data.js#L93-L98
	 * @used-by self::city()
	 * @used-by self::countryName()
	 * @used-by self::iso2()
	 * @used-by self::latitude()
	 * @used-by self::longitude()
	 * @used-by self::postCode()
	 * @used-by self::regionCode()
	 * @used-by self::regionName()
	 * @used-by self::timeZone()
	 * @return string|null
	 */
	private function r(string $k) {return dfaoc($this, function() {return df_http_json(
		"http://geoip.mage2.pro/json/$this->_ip", [], 5, ''
	);}, $k);}

	/**
	 * 2022-11-24
	 * @used-by self::r()
	 * @used-by self::sp()
	 * @var string
	 */
	private $_ip;

	/**
	 * 2016-05-20
	 * @used-by df_visitor()
	 * @used-by \Dfe\TwoCheckout\Address::visitor()
	 */
	static function sp(string $ip = ''):self {return dfcf(function(string $ip):self {
		$r = new self; $r->_ip = $ip ?: df_visitor_ip(); return $r;
	}, [$ip]);}
}