<?php
// 2016-11-24
namespace Df\Sso\Settings\Button;
class Location extends \Df\Config\Settings\Configurable {
	/**
	 * 2016-11-24
	 * The button's label.
	 * @return string
	 */
	public function label() {return $this->v();}
}