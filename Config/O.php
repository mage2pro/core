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
		return $this->cfg(df_const($this, $name, $name), $default);
	}
}