<?php
namespace Df\API;
use Df\API\Document as D;
// 2019-04-05
final class FacadeOptions {
	/**
	 * 2019-04-05
	 * @used-by \Df\API\Facade::p()
	 * @param string|null $v [optional]
	 * @return string|$this
	 */
	function resC($v = null) {return $this->f($v, D::class);}

	/**
	 * 2019-04-05
	 * @used-by \Df\API\Facade::p()
	 * @param bool|null $v [optional]
	 * @return bool|$this
	 */
	function silent($v = null) {return $this->f($v);}

	/**
	 * 2019-04-05
	 * @used-by resC()
	 * @used-by silent()
	 * @param mixed|null $v
	 * @param mixed|null $d [optional]
	 * @return mixed|$this
	 */
	private function f($v = null, $d = null) {
		$k = df_caller_f(); /** @var string $k */
		/** @var string|null $v */
		if (is_null($v)) {
			$r = dfa($this->_d, $k, $d);
		}
		else {
			$this->_d[$k] = $v;
			$r = $this;
		}
		return $r;
	}

	/**
	 * 2019-04-05
	 * @used-by f()
	 * @var array(string => mixed)
	 */
	private $_d = [];

	/**
	 * 2019-04-05
	 * @used-by \Df\API\Facade::p()
	 * @return FacadeOptions
	 */
	static function i() {return new self;}
}