<?php
namespace Df\Framework;
use Magento\Framework\Data\Form\Element\AbstractElement as AE;
use Magento\Framework\Phrase;
interface IValidator {
	/**
	 * 2016-06-30
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetComment()
	 * @used-by \Df\Framework\Validator\Composite::check()
	 * @param AE $e
	 * @return true|Phrase|Phrase[]
	 */
	function check(AE $e);
}
