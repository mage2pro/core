<?php
namespace Df\Api\Settings;
class Google extends \Df\Core\Settings {
	/**
	 * @return string
	 */
	public function clientId() {return $this->v('client_id');}

	/**
	 * @override
	 * @used-by \Df\Core\Settings::v()
	 * @return string
	 */
	protected function prefix() {return 'dfe_api/google/';}

	/** @return \Df\Api\Settings\Google */
	public static function s() {static $r; return $r ? $r : $r = df_o(__CLASS__);}
}