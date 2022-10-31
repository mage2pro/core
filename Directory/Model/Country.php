<?php
namespace Df\Directory\Model;
use Df\Directory\Model\ResourceModel\Country\Collection as C;
# 2016-05-20
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Country extends \Magento\Directory\Model\Country {
	/**
	 * 2016-05-20, 2022-10-31
	 * Не получается сделать этот метод виртуальным,
	 * потому что тогда getIso2Code() будет обращаться к полю `iso_2_code` вместо `iso2_code`.
	 * @used-by df_country_code()
	 * @used-by \Df\Directory\FE\Country::getValue()
	 * @used-by C::mapFrom3To2()
	 * @return string|null
	 */
	final function getIso2Code() {return $this['iso2_code'];}

	/**
	 * 2016-05-20, 2022-10-31
	 * Не получается сделать этот метод виртуальным,
	 * потому что тогда getIso3Code() будет обращаться к полю `iso_3_code` вместо `iso3_code`.
	 * @used-by C::mapFrom3To2()
	 * @return string|null
	 */
	final function getIso3Code() {return $this['iso3_code'];}

	/**
	 * 2016-05-20
	 * Создавать коллекцию надо обязательно через Object Manager,
	 * потому что родительский конструктор используе Dependency Injection.
	 * @used-by df_countries_allowed()
	 */
	final static function c():C {return df_new_om(C::class);}
}