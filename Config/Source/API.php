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
	 * @used-by map()
	 * @see \Df\ZohoBI\Source\Organization::fetch()
	 * @see \Dfe\Dynamics365\Source\PriceList::fetch()
	 * @see \Dfe\Spryng\Source\Account::fetch()
	 * @see \Dfe\Square\Source\Location::fetch()
	 * @return array(string => string)
	 */
	abstract protected function fetch();

	/**
	 * 2017-07-02
	 * @used-by map()
	 * @see \Df\Config\Source\API\Key::isRequirementMet()
	 * @see \Dfe\Dynamics365\Source\PriceList::isRequirementMet()
	 * @return bool
	 */
	abstract protected function isRequirementMet();

	/**
	 * 2017-07-02
	 * @used-by map()
	 * @see \Df\Config\Source\API\Key::requirement()
	 * @see \Df\ZohoBI\Source\Organization::requirement()
	 * @see \Dfe\Dynamics365\Source\PriceList::requirement()
	 * @return string
	 */
	abstract protected function requirement();

	/**
	 * 2017-02-15
	 * @used-by map()
	 * @see \Dfe\Square\Source\Location::exception()
	 * @param \Exception $e
	 * @return array(string => string)
	 */
	protected function exception(\Exception $e) {return ['error' => $e->getMessage()];}

	/**
	 * 2017-07-02
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	final protected function map() {
		/** @var array(string => string) $result */ /** @var bool $met */
		$result = df_map_0([], $met = $this->isRequirementMet() ? null : $this->requirement());
		if ($met) {
			try {$result += $this->fetch();}
			catch (\Exception $e) {$result = $this->exception($e);}
		}
		return $result;
	}
}