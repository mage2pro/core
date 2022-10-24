<?php
namespace Df\Config\Source\API;
use Df\Config\Settings as S;
# 2017-07-02
/** @see \Df\Payment\Source\API\Key */
abstract class Key extends \Df\Config\Source\API {
	/**
	 * 2017-07-02
	 * @used-by self::apiKey()
	 * @see \Dfe\Spryng\Source\Account::apiKeyName()
	 * @see \Dfe\Square\Source\Location::apiKeyName()
	 * @return string
	 */
	abstract protected function apiKeyName();

	/**
	 * 2017-07-02
	 * @used-by self::requirement()
	 * @see \Dfe\Spryng\Source\Account::apiKeyTitle()
	 * @see \Dfe\Square\Source\Location::apiKeyTitle()
	 * @return string
	 */
	abstract protected function apiKeyTitle();

	/**
	 * 2017-07-02
	 * @used-by self::apiKey()
	 * @see \Df\Payment\Source\API\Key::ss()
	 * @return S
	 */
	abstract protected function ss();

	/**
	 * 2017-07-02
	 * @override
	 * @see \Df\Config\Source\API::isRequirementMet()
	 * @used-by \Df\Config\Source\API::map()
	 */
	final protected function isRequirementMet():bool {return !!$this->ss()->p($this->apiKeyName());}

	/**
	 * 2017-07-02
	 * @used-by \Df\Config\Source\API::map()
	 */
	final protected function requirement():string {return "Specify {$this->apiKeyTitle()} first, and then save the settings.";}
}