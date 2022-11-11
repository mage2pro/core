<?php
# 2016-11-24
namespace Df\Sso\Settings;
/**
 * 2016-11-25
 * Класс нельзя объявлять финальным, потому что от него наследуется
 * @see \Dfe\FacebookLogin\Settings\Button
 */
class Button extends \Df\Config\Settings\Configurable {
	/**
	 * 2016-11-24 The button's label.
	 * @used-by \Df\Sso\Button::attributes()
	 * @used-by \Df\Sso\Button::loggedOut()
	 * @see \Dfe\AmazonLogin\Settings\Button::label()
	 */
	function label():string {return $this->v();}

	/**
	 * 2016-11-26
	 * @see \Df\Sso\Source\Button\Type\UNL
	 * @used-by \Df\Sso\Button::html()
	 * @used-by \Df\Sso\Button\Js::loggedOut()
	 */
	final function type():string {return $this->v();}
}