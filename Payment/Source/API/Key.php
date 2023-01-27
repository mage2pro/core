<?php
namespace Df\Payment\Source\API;
use Df\Payment\Settings as S;
# 2017-07-02
/** @see \Df\Payment\Source\API\Key\Testable */
abstract class Key extends \Df\Config\Source\API\Key {
	/**
	 * 2017-07-02
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * 2023-01-28
	 * «Declaration of Df\Payment\Source\API\Key::ss(): Df\Payment\Settings
	 * must be compatible with Df\Config\Source\API\Key::ss(): Df\Config\Settings»:
	 * https://github.com/mage2pro/core/issues/181
	 * @override
	 * @see \Df\Config\Source\API\Key::ss()
	 * @used-by \Df\Config\Source\API\Key::isRequirementMet()
	 * @return S
	 */
	final protected function ss():\Df\Config\Settings {return dfps($this);}
}