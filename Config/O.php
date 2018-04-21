<?php
namespace Df\Config;
use Df\Core\Exception as DFE;
use Df\Framework\Form\Element\Checkbox;
/**
 * 2017-01-24
 * @see \Df\Config\ArrayItem
 * @see \Df\Typography\Font
 * Наследуемся от @see \Df\Core\O,
 * потому что метод @see \Df\Config\O::v()
 * использует метод @see \Df\Core\O::cfg()
 */
class O extends \Df\Core\O {
	/**
	 * 2016-08-02
	 * @used-by \Df\Config\Backend\Serialized::validate()
	 * @see \Df\Typography\Font::validate()
	 * @see \Dfe\AllPay\InstallmentSales\Plan\Entity::validate()
	 * @throws DFE
	 */
	function validate() {}

	/**
	 * 2015-12-30
	 * @used-by \Df\Typography\Font::bold()
	 * @used-by \Df\Typography\Font::enabled()
	 * @used-by \Df\Typography\Font::italic()
	 * @used-by \Df\Typography\Font::underline()
	 * @used-by \Dfe\CurrencyFormat\O::delimitSymbolFromAmount()
	 * @used-by \Dfe\CurrencyFormat\O::showDecimals()
	 * @param bool|callable $d [optional]
	 * @param string|null $k [optional]
	 * @return bool
	 */
	final protected function b($d = false, $k = null) {return $this->filter(
		function($v) use($d) {return Checkbox::b($v, $d);}, $d, $k
	);}

	/**
	 * 2016-08-10
	 * @used-by \Df\Typography\Font::scale_horizontal()
	 * @used-by \Df\Typography\Font::scale_vertical()
	 * @used-by \Dfe\AllPay\InstallmentSales\Plan\Entity::fee()
	 * @used-by \Dfe\AllPay\InstallmentSales\Plan\Entity::rate()
	 * @used-by \Doormall\Shipping\Partner\Entity::fee()
	 * @uses df_float()
	 * @param float|callable $d [optional]
	 * @param string|null $k [optional]
	 * @return float
	 */
	final protected function f($d = 0.0, $k = null) {return $this->filter('df_float', $d, $k);}

	/**
	 * 2016-08-10
	 * @uses df_int()
	 * @param int|callable $d [optional]
	 * @param string|null $k [optional]
	 * @return int
	 */
	final protected function i($d = 0, $k = null) {return $this->filter('df_int', $d, $k);}

	/**
	 * 2016-08-10
	 * @uses df_nat()
	 * @param int|callable $d [optional]
	 * @param string|null $k [optional]
	 * @return int
	 */
	final protected function nat($d = null, $k = null) {return $this->filter('df_nat', $d, $k);}

	/**
	 * 2016-08-10
	 * @uses df_nat0()
	 * @param int|callable $d [optional]
	 * @param string|null $k [optional]
	 * @return int
	 */
	final protected function nat0($d = 0, $k = null) {return $this->filter('df_nat0', $d, $k);}

	/**
	 * 2015-12-30
	 * @used-by \Dfe\CurrencyFormat\O
	 * @used-by \Doormall\Shipping\Partner\Entity::id()
	 * @used-by \Doormall\Shipping\Partner\Entity::title()
	 * @param mixed|callable $d [optional]
	 * @param string|null $k [optional]
	 * @return mixed
	 */
	final protected function v($d = null, $k = null) {
		$k = $k ?: df_caller_f();
		return $this->cfg(df_const($this, $k, $k), $d);
	}

	/**
	 * 2016-08-10
	 * @used-by b()
	 * @used-by f()
	 * @used-by i()
	 * @used-by nat()
	 * @used-by nat0()
	 * @param callable $f
	 * @param mixed|null $d [optional]
	 * @param string|null $k [optional]
	 * @return mixed
	 */
	private function filter(callable $f, $d = null, $k = null) {return 
		dfc($this, function($f, $d, $k) {return
			call_user_func($f, $this->v($d, $k))
		;}, [$f, $d, $k ?: df_caller_f(1)])
	;}
}