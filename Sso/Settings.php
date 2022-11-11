<?php
# 2016-12-02
namespace Df\Sso;
/**
 * 2016-12-02
 * @see \Dfe\AmazonLogin\Settings
 * @see \Dfe\BlackbaudNetCommunity\Settings
 * @see \Dfe\FacebookLogin\Settings
 * @method static Settings s()
 */
abstract class Settings extends \Df\Config\Settings {
	/**
	 * 2016-12-02
	 * @used-by \Df\Sso\CustomerReturn::execute()
	 */
	final function regCompletionMessage():string {return $this->v();}
}