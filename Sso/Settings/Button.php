<?php
// 2016-11-24
namespace Df\Sso\Settings;
/**
 * 2016-11-25
 * Класс нельзя объявлять финальным, потому что от него наследуется
 * @see \Dfe\FacebookLogin\Settings\Button
 */
class Button extends \Df\Config\Settings\Configurable {
	/**
	 * 2016-11-24
	 * The button's label.
	 * @see \Dfe\AmazonLogin\Settings\Button::label()
	 * @return string
	 */
	function label() {return $this->v();}

	/**
	 * 2016-11-26
	 * @see \Df\Sso\Source\Button\Type\UNL
	 * @used-by \Df\Sso\Button::html()
	 * @used-by \Df\Sso\Button\Js::loggedOut()
	 * @return string
	 */
	final function type() {return $this->v();}
}