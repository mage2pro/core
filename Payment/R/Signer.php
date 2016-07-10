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
	 * @return self
	 */
	public static function i(array $params) {return df_create(static::class, $params);}
}


