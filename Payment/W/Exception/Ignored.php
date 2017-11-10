<?php
namespace Df\Payment\W\Exception;
use Df\Payment\Method as M;
use Df\Payment\W\Reader as R;
// 2017-03-11
final class Ignored extends \Df\Payment\W\Exception {
	/**
	 * 2017-03-11
	 * @override
	 * @see \Df\Payment\W\Exception::__construct()
	 * @used-by \Df\Payment\W\Action::execute()
	 * @used-by \Df\Payment\W\F::c()
	 * @param M $m
	 * @param R $r
	 * @param string|null $type
	 */
	function __construct(M $m, R $r, $type) {$this->_type = $type; parent::__construct($m, $r);}

	/**
	 * 2017-03-11
	 * @override
	 * @see \Df\Core\Exception::message()
	 * @used-by \Df\Payment\W\Action::ignoredLog()
	 * @used-by \Df\Payment\W\Responder::ignored()
	 * @return string
	 */
	function message() {return sprintf(
		"The %snotifications are intentionally ignored by the {$this->mTitle()} module.",
			is_null($this->_type) ? '' : "«{$this->_type}» "
	);}

	/**
	 * 2017-03-11
	 * @used-by __construct()
	 * @used-by message()
	 * @var string|null
	 */
	private $_type;
}