<?php
namespace Df\Framework;
interface IValidator {
	/**
	 * 2016-06-30
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetComment()
	 * @used-by \Df\Framework\Validator\Composite::check()
	 * @return true|string|string[]
	 */
	function check();
}
