<?php
namespace Df\Framework\Form\Element;
use Df\Framework\Form\Element\Enable\Requirement as R;
class Enabler {
	/**
	 * 2016-06-30
	 * @uses \Df\Framework\Form\Element\Enable\Requirement::check()
	 * @return true|string[]
	 */
	public function check() {return array_filter(df_each($this->_r, 'check'), 'is_string') ?: true;}

	/**
	 * 2016-06-30
	 * @param R[] $requirements
	 */
	public function __construct(array $requirements) {$this->_r = $requirements;}

	/**
	 * 2016-06-30
	 * @var R[]
	 */
	private $_r;
}