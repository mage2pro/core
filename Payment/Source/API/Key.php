<?php
namespace Df\Payment\Source\API;
use Df\Payment\Settings as S;
// 2017-07-02
/** @see \Df\Payment\Source\API\Key\Testable */
abstract class Key extends \Df\Config\Source\API\Key {
	/**
	 * 2017-07-02
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @override
	 * @see \Df\Config\Source\API\Key::ss()
	 * @used-by \Df\Config\Source\API\Key::apiKey()
	 * @return S
	 */
	protected function ss() {return dfps($this);}
}