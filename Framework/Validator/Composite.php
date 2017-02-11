<?php
namespace Df\Framework\Validator;
use Df\Framework\IValidator as R;
use Magento\Framework\Data\Form\Element\AbstractElement as AE;
use Magento\Framework\Phrase;
class Composite implements \Df\Framework\IValidator {
	/**
	 * 2016-06-30
	 * @override
	 * @see \Df\Framework\IValidator::check()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetComment()
	 * @used-by check()
	 * @param AE $e
	 * @return true|Phrase|Phrase[]
	 */
	function check(AE $e) {return
		dfa_flatten(array_map(function(R $r) use($e) {
			/** @var true|Phrase|Phrase[] $messages */
			$messages = $r->check($e);
			return true === $messages ? [] : df_array($messages);
		}, $this->_children)) ?: true
	;}

	/**
	 * 2016-06-30
	 * @param R[] $children
	 */
	function __construct(array $children) {$this->_children = $children;}

	/**
	 * 2016-06-30
	 * @var R[]
	 */
	private $_children;
}