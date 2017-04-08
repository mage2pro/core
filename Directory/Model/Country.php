<?php
namespace Df\Directory\Model;
use Df\Directory\Model\ResourceModel\Country\Collection;
class Country extends \Magento\Directory\Model\Country {
	/**
	 * 2016-05-20
	 * Не получается сделать этот метод виртуальным,
	 * потому что тогда getIso2Code() будет обращаться к полю iso_2_code.
	 * @return string|null
	 */
	function getIso2Code() {return $this['iso2_code'];}

	/**
	 * 2016-05-20
	 * Не получается сделать этот метод виртуальным,
	 * потому что тогда getIso3Code() будет обращаться к полю iso_3_code.
	 * @return string|null
	 */
	function getIso3Code() {return $this['iso3_code'];}

	/**
	 * 2016-05-19
	 * 2016-05-20
	 * Создавать коллекцию надо обязательно через Object Manager,
	 * потому что родительский конструктор используе Dependency Injection.
	 * @return Collection
	 */
	static function c() {return df_new_om(Collection::class);}
	/** @return Collection */
	static function cs() {return Collection::s();}
}

