<?php
namespace Df\Api\Google;
/**
 * 2015-11-27
 * https://developers.google.com/fonts/docs/developer_api#Example
 */
class Font extends \Df\Core\O {
	/** @return string */
	public function family() {return $this['family'];}
}