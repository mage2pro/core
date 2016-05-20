<?php
namespace Df\Core;
class Visitor extends O {
	/**
	 * На английском языке. Например: «Moscow».
	 * @return string
	 */
	public function city() {return $this->r('city');}

	/**
	 * На английском языке. Например: «Russia».
	 * @return string
	 */
	public function countryName() {return $this->r('country_name');}

	/** @return string */
	public function iso2() {return $this->r('country_code');}

	/**
	 * Например: «55.752».
	 * @return string
	 */
	public function latitude() {return $this->r('latitude');}

	/**
	 * Например: «37.616».
	 * @return string
	 */
	public function longitude() {return $this->r('longitude');}

	/**
	 * Например: «101194».
	 * @return string
	 */
	public function postCode() {return $this->r('zip_code');}

	/**
	 * Например: «MOW».
	 * @return string
	 */
	public function regionCode() {return $this->r('region_code');}

	/**
	 * На английском языке. Например: «Moscow».
	 * @return string
	 */
	public function regionName() {return $this->r('region_name');}

	/**
	 * Например: «Europe/Moscow».
	 * @return string
	 */
	public function timeZone() {return $this->r('time_zone');}

	/**
	 * @param string $key
	 * @return string|null
	 */
	private function r($key) {return dfa($this->responseA(), $key);}

	/** @return array(string => mixed) */
	private function responseA() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_json_decode(file_get_contents(
				'https://freegeoip.net/json/' . $this[self::$P__IP]
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-05-20
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__IP, RM_V_STRING_NE);
	}

	/** @var string */
	private static $P__IP = 'ip';

	/**
	 * 2016-05-20
	 * @param string|null $ip [optional]
	 * @return $this
	 */
	public static function s($ip = null) {
		/** @var array(string => $this) $cache */
		static $cache;
		$ip = $ip ?: df_visitor_ip();
		if (!isset($cache[$ip])) {
			$cache[$ip] = new self([self::$P__IP => $ip]);
		}
		return $cache[$ip];
	}
}