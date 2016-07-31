<?php
namespace Df\Config;
use Df\Framework\Form\Element\Checkbox;
class O extends \Df\Core\O {
	/**
	 * 2015-12-31
	 * @override
	 * @see \Df\Core\O::getId()
	 * @used-by \Df\Config\A::get()
	 * https://github.com/mage2pro/core/tree/dcc75ea95b8644548d8b2a5c5ffa71c891f97e60/Config/A.php#L26
	 * @return string
	 */
	public function getId() {df_abstract($this);}

	/**
	 * 2015-12-30
	 * @param string $key
	 * @param bool|callable $default [optional]
	 * @return bool
	 */
	protected function b($key, $default = false) {return Checkbox::b($this->ct($key), $default);}

	/**
	 * 2015-12-30
	 * @used-by \Dfe\CurrencyFormat\O
	 * @param string $name
	 * @param mixed|callable $default [optional]
	 * @return mixed
	 */
	protected function ct($name, $default = null) {
		/** @var string $class */
		$class = get_class($this);
		return $this->cfg(constant("$class::$name"), $default);
	}
}