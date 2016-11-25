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
	 * @return string
	 */
	public function label() {return $this->v();}
}