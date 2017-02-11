<?php
namespace Df\Core;
// 2016-08-21
class A extends O {
	/**
	 * 2016-08-21
	 * https://3v4l.org/2J6sp
	 * @param string|string[]|null $items [optional]
	 * @param mixed|null $default [optional]
	 * @return mixed|array(string => mixed)|null
	 */
	function a($items = null, $default = null) {
		return is_null($items) ? $this->_data : (
			is_array($items) ? array_map([$this, __FUNCTION__], $items) :
				dfa_deep($this->_data, $items, $default)
		);
	}
}