<?php
// 2016-12-02
namespace Df\Sso;
/** @method static Settings s() */
abstract class Settings extends \Df\Config\Settings {
	/**
	 * 2016-12-02
	 * @used-by \Df\Sso\CustomerReturn::execute()
	 * @return string
	 */
	function regCompletionMessage() {return $this->v();}
}