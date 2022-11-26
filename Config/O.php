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
 * использует метод @see \Df\Core\O::a()
 */
class O extends \Df\Core\O {
	/**
	 * 2016-08-02
	 * @used-by \Df\Config\Backend\Serialized::validate()
	 * @see \Df\Typography\Font::validate()
	 * @see \Dfe\AllPay\InstallmentSales\Plan\Entity::validate()
	 * @throws DFE
	 */
	function validate():void {}

	/**
	 * 2015-12-30
	 * @used-by \Df\Typography\Font::bold()
	 * @used-by \Df\Typography\Font::enabled()
	 * @used-by \Df\Typography\Font::italic()
	 * @used-by \Df\Typography\Font::underline()
	 * @used-by \Dfe\CurrencyFormat\O::delimitSymbolFromAmount()
	 * @used-by \Dfe\CurrencyFormat\O::showDecimals()
	 * @param bool|callable $d [optional]
	 */
	final protected function b($d = false, string $k = ''):bool {return $this->filter(
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
	 */
	final protected function f($d = 0.0, string $k = ''):float {return $this->filter('df_float', $d, $k);}

	/**
	 * 2016-08-10
	 * @uses df_int()
	 * @param int|callable $d [optional]
	 */
	final protected function i($d = 0, string $k = ''):int {return $this->filter('df_int', $d, $k);}

	/**
	 * 2016-08-10
	 * @uses df_nat()
	 * @param int|callable $d [optional]
	 */
	final protected function nat($d = null, string $k = ''):int {return $this->filter('df_nat', $d, $k);}

	/**
	 * 2016-08-10
	 * @uses df_nat0()
	 * @param int|callable $d [optional]
	 */
	final protected function nat0($d = 0, string $k = ''):int {return $this->filter('df_nat0', $d, $k);}

	/**
	 * 2015-12-30
	 * 2022-10-24
	 * `mixed` as a return type is not supported by PHP < 8: https://github.com/mage2pro/core/issues/168#user-content-mixed
	 * @used-by self::v0()
	 * @used-by \Df\Typography\Font::color()
	 * @used-by \Df\Typography\Font::letter_case()
	 * @used-by \Dfe\CurrencyFormat\O::code()
	 * @used-by \Dfe\CurrencyFormat\O::decimalSeparator()
	 * @used-by \Dfe\CurrencyFormat\O::symbolPosition()
	 * @used-by \Dfe\CurrencyFormat\O::thousandsSeparator()
	 * @used-by \Dfe\Sift\PM\Entity::id()
	 * @used-by \Dfe\Sift\PM\Entity::sGateway()
	 * @used-by \Dfe\Sift\PM\Entity::sType()
	 * @used-by \Doormall\Shipping\Partner\Entity::id()
	 * @used-by \Doormall\Shipping\Partner\Entity::title()
	 * @param mixed|callable $d [optional]
	 * @param string|null $k [optional]
	 * @return mixed
	 */
	final protected function v($d = null, $k = null) {
		$k = $k ?: df_caller_f();
		return $this->a(df_const($this, $k, $k), $d);
	}

	/**
	 * 2020-02-05
	 * It returns `null` if a backend user did not chose a value of a dropdown
	 * (in this case the frontend part returns the "0" string
	 * which is associated with a label like "-- select a value --").
	 * @used-by \Dfe\Sift\PM\Entity::sGateway()
	 * @used-by \Dfe\Sift\PM\Entity::sType()
	 * @param mixed|null $d
	 * @param string|null $k
	 * @return string|null
	 */
	final protected function v0($d = null, $k = null) {return '0' !== ($r = $this->v(null, $k ?: df_caller_f())) ? $r : $d;}

	/**
	 * 2016-08-10
	 * 2022-10-24
	 * `mixed` as a return type is not supported by PHP < 8: https://github.com/mage2pro/core/issues/168#user-content-mixed
	 * 2022-11-23 `callable` as an argument type is supported by PHP ≥ 5.4:
	 * https://github.com/mage2pro/core/issues/174#user-content-callable
	 * @used-by self::b()
	 * @used-by self::f()
	 * @used-by self::i()
	 * @used-by self::nat()
	 * @used-by self::nat0()
	 * @param mixed|null $d [optional]
	 * @return mixed
	 */
	private function filter(callable $f, $d = null, string $k = '') {return dfc($this, function($f, $d, string $k) {return
		call_user_func($f, $this->v($d, $k))
	;}, [$f, $d, $k ?: df_caller_f(1)]);}
}