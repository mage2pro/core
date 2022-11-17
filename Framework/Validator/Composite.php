<?php
namespace Df\Framework\Validator;
use Df\Framework\IValidator as IV;
use Magento\Framework\Data\Form\Element\AbstractElement as AE;
use Magento\Framework\Phrase;
# 2016-06-30
/**  2022-11-17 @deprecated It is unused. */
class Composite implements IV {
	/**
	 * 2016-06-30
	 * @override
	 * @see \Df\Framework\IValidator::check()
	 * @used-by self::check()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetComment()
	 * @param AE $e
	 * @return true|Phrase|Phrase[]
	 */
	function check(AE $e) {return dfa_flatten(array_map(function(IV $v) use($e) {/** @var true|Phrase|Phrase[] $m */return
		true === ($m = $v->check($e)) ? [] : df_array($m)
	;}, $this->_children)) ?: true;}

	/**
	 * 2016-06-30
	 * @param IV[] $children
	 */
	function __construct(array $children) {$this->_children = $children;}

	/**
	 * 2016-06-30
	 * @var IV[]
	 */
	private $_children;
}