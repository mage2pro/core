<?php
namespace Df\Core;
class Visitor {
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
	private function r($key) {return df_a($this->responseA(), $key);}

	/** @return array(string => mixed) */
	private function responseA() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = json_decode(file_get_contents(
				'https://freegeoip.net/json/' . rm_visitor_ip()
			), true);
		}
		return $this->{__METHOD__};
	}

	/** @return Visitor */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}