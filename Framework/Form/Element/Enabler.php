<?php
namespace Df\Framework\Form\Element;
use Df\Framework\Form\Element\Enable\Requirement;
class Enabler {
	/**
	 * 2016-06-30
	 * @return true|string[]
	 */
	public function check() {
		/** @var string[] $result */
		$result = [];
		foreach ($this->_requirements as $requirement) {
			/** @var Requirement $requirement */
			$result[]= $requirement->check();
		}
		$result = array_filter($result, 'is_string');
		return !$result ? true : $result;
	}

	/**
	 * 2016-06-30
	 * @param Requirement[] $requirements
	 */
	public function __construct(array $requirements) {
		$this->_requirements = $requirements;
	}

	/**
	 * 2016-06-30
	 * @var Requirement[]
	 */
	private $_requirements;
}