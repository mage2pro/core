<?php
namespace Df\Directory\Model;
use Df\Directory\Model\ResourceModel\Country\Collection;
class Country extends \Magento\Directory\Model\Country {
	/**
	 * 2016-05-19
	 * 2016-05-20
	 * Создавать коллекцию надо обязательно через Object Manager,
	 * потому что родительский конструктор используе Dependency Injection.
	 * @return Collection
	 */
	public static function c() {return df_create(Collection::class);}
	/** @return Collection */
	public static function cs() {return Collection::s();}
}

