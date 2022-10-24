<?php
namespace Df\Config\Source;
/**
 * 2017-07-02
 * @see \Df\Config\Source\API\Key
 * @see \Df\ZohoBI\Source\Organization
 * @see \Dfe\Dynamics365\Source\PriceList
 */
abstract class API extends \Df\Config\Source {
	/**
	 * 2017-07-02
	 * @used-by self::map()
	 * @see \Df\ZohoBI\Source\Organization::fetch()
	 * @see \Dfe\Dynamics365\Source\PriceList::fetch()
	 * @see \Dfe\Spryng\Source\Account::fetch()
	 * @see \Dfe\Square\Source\Location::fetch()
	 * @return array(string => string)
	 */
	abstract protected function fetch():array;

	/**
	 * 2017-07-02
	 * @used-by self::map()
	 * @see \Df\Config\Source\API\Key::isRequirementMet()
	 * @see \Df\ZohoBI\Source\Organization::isRequirementMet()
	 * @see \Dfe\Dynamics365\Source\PriceList::isRequirementMet()
	 */
	abstract protected function isRequirementMet():bool;

	/**
	 * 2017-07-02
	 * @used-by map()
	 * @see \Df\Config\Source\API\Key::requirement()
	 * @see \Df\ZohoBI\Source\Organization::requirement()
	 * @see \Dfe\Dynamics365\Source\PriceList::requirement()
	 * @return string
	 */
	abstract protected function requirement():string;

	/**
	 * 2017-02-15
	 * @used-by self::map()
	 * @see \Dfe\Square\Source\Location::exception()
	 * @param \Exception $e
	 * @return array(string => string)
	 */
	protected function exception(\Exception $e):array {return ['error' => $e->getMessage()];}

	/**
	 * 2017-07-02
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	final protected function map():array {
		/** @var array(string => string) $r */ /** @var bool $met */
		$r = df_map_0([], ($met = $this->isRequirementMet()) ? null : $this->requirement());
		if ($met) {
			try {$r += $this->fetch();}
			catch (\Exception $e) {$r = $this->exception($e);}
		}
		return $r;
	}
}