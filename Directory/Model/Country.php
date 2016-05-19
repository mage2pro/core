<?php
namespace Df\Directory\Model;
use Df\Directory\Model\ResourceModel\Country\Collection;
class Country extends \Magento\Directory\Model\Country {
	/** @return Collection */
	public static function c() {return new Collection;}
	/** @return Collection */
	public static function cs() {return Collection::s();}
}

