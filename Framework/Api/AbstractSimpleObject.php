<?php
namespace Df\Framework\Api;
use Magento\Framework\Api\AbstractSimpleObject as _P;
class AbstractSimpleObject extends _P {
	/**
	 * 2017-05-22
	 * @used-by df_api_object_get()
	 * @param _P $o
	 * @param string $k
	 * @return mixed|null
	 */
	static function get(_P $o, $k) {return $o->_get($k);}
}