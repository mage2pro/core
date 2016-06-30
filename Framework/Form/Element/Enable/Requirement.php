<?php
namespace Df\Framework\Form\Element\Enable;
abstract class Requirement {
	/**
	 * 2016-06-30
	 * @return true|string
	 */
	abstract public function check();
}
