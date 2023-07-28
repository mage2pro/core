<?php
namespace Df\Framework\Api;
use Magento\Framework\Api\AbstractSimpleObject as _P;
# 2017-05-22
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class AbstractSimpleObject extends _P {
	/**
	 * 2017-05-22
	 * @used-by df_api_o_get()
	 * @return mixed|null
	 */
	final static function get(_P $o, string $k) {return $o->_get($k);}
}