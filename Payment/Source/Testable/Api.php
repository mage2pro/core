<?php
namespace Df\Payment\Source\Testable;
/**
 * 2017-02-15
 * @see \Dfe\Spryng\Source\Account
 * @see \Dfe\Square\Source\Location
 */
abstract class Api extends \Df\Payment\Source\Testable {
	/**
	 * 2017-02-15
	 * @used-by map()
	 * @see \Dfe\Spryng\Source\Account::apiKeyTitle()
	 * @see \Dfe\Square\Source\Location::apiKeyTitle()
	 * @return string
	 */
	abstract protected function apiKeyName();

	/**
	 * 2017-02-15
	 * @used-by map()
	 * @see \Dfe\Spryng\Source\Account::apiKeyTitle()
	 * @see \Dfe\Square\Source\Location::apiKeyTitle()
	 * @return string
	 */
	abstract protected function apiKeyTitle();

	/**
	 * 2017-02-15
	 * @used-by map()
	 * @see \Dfe\Spryng\Source\Account::fetch()
	 * @see \Dfe\Square\Source\Location::fetch()
	 * @param string $token
	 * @return array(string => string)
	 */
	abstract protected function fetch($token);

	/**
	 * 2017-02-15
	 * @used-by map()
	 * @see \Dfe\Square\Source\Location::exception()
	 * @param \Exception $e
	 * @return array(string => string)
	 */
	protected function exception(\Exception $e) {return ['error' => $e->getMessage()];}

	/**
	 * 2017-02-15
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	final protected function map() {
		/** @var array(string => string) $result */
		$result = [0 => "Specify {$this->apiKeyTitle()} first, and then save the settings."];
		/** @var string $key */
		if ($key = $this->ss()->p($this->tkey($this->apiKeyName()))) {
			try {$result = $this->fetch($key);}
			// 2016-10-06
			// Я работал с неактивированной учётной записью Square,
			// и в промышленном режиме у меня этот запрос вызывал исключительную ситуацию:
			// [HTTP/1.1 403 Forbidden] {"errors":[{"category":"AUTHENTICATION_ERROR","code":"FORBIDDEN","detail":"You have insufficient permissions to perform that action."}]}
			catch (\Exception $e) {$result = $this->exception($e);}
		}
		return $result;
	}
}