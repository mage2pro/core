<?php
namespace Df\Payment\R;
abstract class Signer extends \Df\Core\O {
	/**
	 * 2016-07-10
	 * @return string
	 */
	abstract public function sign();

	/**
	 * 2016-07-10
	 * http://php.net/manual/en/function.get-called-class.php#115790
	 * @param array(string => mixed) $params
	 * @param object|null $caller [optional]
	 * @return self
	 */
	public static function i(array $params, $caller = null) {
		return df_create($caller ? df_convention($caller, 'Signer') : static::class, $params);
	}
}