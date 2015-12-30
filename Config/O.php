<?php
namespace Df\Config;
use Df\Framework\Data\Form\Element\Checkbox;
class O extends \Df\Core\O {
	/**
	 * 2015-12-30
	 * @param string $key
	 * @param bool $default [optional]
	 * @return bool
	 */
	protected function b($key, $default = false) {return Checkbox::b($this->ct($key), $default);}

	/**
	 * 2015-12-30
	 * @used-by \Dfe\CurrencyFormat\O
	 * @param string $name
	 * @param mixed $default [optional]
	 * @return mixed
	 */
	protected function ct($name, $default = null) {
		return $this->cfg(constant("self::$name"), $default);
	}
}