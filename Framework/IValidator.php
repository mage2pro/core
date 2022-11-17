<?php
namespace Df\Framework;
use Magento\Framework\Data\Form\Element\AbstractElement as AE;
use Magento\Framework\Phrase;
/**
 * 2016-06-30
 * @see \Df\Framework\Validator\Currency
 * @see \Dfe\BlackbaudNetCommunity\Url
 */
interface IValidator {
	/**
	 * 2016-06-30
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetComment()
	 * @see \Dfe\BlackbaudNetCommunity\Url::check()
	 * @see \Df\Framework\Validator\Currency::check()
	 * @return true|Phrase|Phrase[]
	 */
	function check(AE $e);
}
