<?php
namespace Df\Framework;
use Df\Framework\Requirement as R;
class Validator {
	/**
	 * 2016-06-30
	 * @uses \Df\Framework\Requirement::check()
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