<?php
namespace Df\Payment\R;
// 2016-08-29
/** @method Method method() */
class BlockInfo extends \Df\Payment\Block\Info {
	/**
	 * 2016-07-18
	 * @param string|null $key [optional]
	 * @return Response|string|null
	 */
	protected function responseF($key = null) {return $this->method()->responseF($key);}

	/**
	 * 2016-07-18
	 * @param string|null $key [optional]
	 * @return Response|string|null
	 */
	protected function responseL($key = null) {return $this->method()->responseL($key);}
}