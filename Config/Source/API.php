<?php
namespace Df\Config\Source;
/**
 * 2017-07-02
 * @see \Df\Config\Source\API\Key
 */
abstract class API extends \Df\Config\Source {
	/**
	 * 2017-07-02
	 * @used-by map()
	 * @see \Dfe\Spryng\Source\Account::fetch()
	 * @see \Dfe\Square\Source\Location::fetch()
	 * @return array(string => string)
	 */
	abstract protected function fetch();

	/**
	 * 2017-07-02
	 * @used-by map()
	 * @return bool
	 */
	abstract protected function isRequirementMet();

	/**
	 * 2017-07-02
	 * @used-by map()
	 * @see \Df\Config\Source\API\Key::requirementTitle()
	 * @return string
	 */
	abstract protected function requirementTitle();

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
		/** @var array(string => string) $result */
		$result = [0 => "{$this->requirementTitle()} first, and then save the settings."];
		/** @var string $key */
		if ($this->isRequirementMet()) {
			try {$result = $this->fetch();}
			catch (\Exception $e) {$result = $this->exception($e);}
		}
		return $result;
	}
}