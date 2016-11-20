<?php
namespace Df\Framework\Validator;
use Df\Framework\IValidator as R;
class Composite implements \Df\Framework\IValidator {
	/**
	 * 2016-06-30
	 * @override
	 * @see \Df\Framework\IValidator::check()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetComment()
	 * @used-by check()
	 * @return true|string|string[]
	 */
	public function check() {return
		dfa_flatten(array_map(function(R $r) {
			/** @var true|string|string[] $messages */
			$messages = $r->check();
			return true === $messages ? [] : df_array($messages);
		}, $this->_children)) ?: true
	;}

	/**
	 * 2016-06-30
	 * @param R[] $children
	 */
	public function __construct(array $children) {$this->_children = $children;}

	/**
	 * 2016-06-30
	 * @var R[]
	 */
	private $_children;
}