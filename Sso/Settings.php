<?php
// 2016-12-02
namespace Df\Sso;
/** @method static Settings s() */
abstract class Settings extends \Df\Config\Settings {
	/**
	 * 2016-12-02
	 * @return string
	 */
	public function regCompletionMessage() {return $this->v();}
}