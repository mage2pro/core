<?php
namespace Df\Config;
use Df\Core\Exception as DFE;
use Df\Framework\Form\Element\Checkbox;
class O extends \Df\Core\O {
	/**
	 * 2016-08-02
	 * @used-by \Df\Config\Backend\Serialized::validate()
	 * @return void
	 * @throws DFE
	 */
	public function validate() {}

	/**
	 * 2015-12-30
	 * @param bool|callable $d [optional]
	 * @param string|null $k [optional]
	 * @return bool
	 */
	protected function b($d = false, $k = null) {return
		$this->filter(function($v) use($d) {return Checkbox::b($v, $d);}, $d, $k)
	;}

	/**
	 * 2016-08-10
	 * @uses df_float()
	 * @param float|callable $d [optional]
	 * @param string|null $k [optional]
	 * @return float
	 */
	protected function f($d = 0.0, $k = null) {return $this->filter('df_float', $d, $k);}

	/**
	 * 2016-08-10
	 * @uses df_int()
	 * @param int|callable $d [optional]
	 * @param string|null $k [optional]
	 * @return int
	 */
	protected function i($d = 0, $k = null) {return $this->filter('df_int', $d, $k);}

	/**
	 * 2016-08-10
	 * @uses df_nat()
	 * @param int|callable $d [optional]
	 * @param string|null $k [optional]
	 * @return int
	 */
	protected function nat($d = null, $k = null) {return $this->filter('df_nat', $d, $k);}

	/**
	 * 2016-08-10
	 * @uses df_nat0()
	 * @param int|callable $d [optional]
	 * @param string|null $k [optional]
	 * @return int
	 */
	protected function nat0($d = 0, $k = null) {return $this->filter('df_nat0', $d, $k);}

	/**
	 * 2015-12-30
	 * @used-by \Dfe\CurrencyFormat\O
	 * @param mixed|callable $d [optional]
	 * @param string|null $key [optional]
	 * @return mixed
	 */
	protected function v($d = null, $key = null) {
		$key = $key ?: df_caller_f();
		return $this->cfg(df_const($this, $key, $key), $d);
	}

	/**
	 * 2016-08-10
	 * @param callable $f
	 * @param mixed|null $d [optional]
	 * @param string|null $key [optional]
	 * @return mixed
	 */
	private function filter(callable $f, $d = null, $key = null) {return
		dfc($this, function($f, $d, $key) {return
			call_user_func($f, $this->v($d, $key))
		;}, [$f, $d, $key ?: df_caller_f(1)])
	;}
}