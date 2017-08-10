<?php
namespace Df\Core;
// 2017-08-10
final class RAM {
	/**
	 * 2017-08-10
	 * @param string $tag
	 */
	function clean($tag) {
		foreach (dfa($this->_tags, $tag, []) as $k) { /** @var string $k */
			unset($this->_data[$k]);
		}
	}

	/**
	 * 2017-08-10
	 * @param string $k
	 * @param mixed $v
	 * @param string[] $tags [optional]
	 */
	function set($k, $v, $tags = []) {
		$this->_data[$k] = $v;
		foreach ($tags as $tag) { /** @var string $tag */
			$this->_tags[$tag][] = $k;
		}
	}

	/**
	 * 2017-08-10
	 * @var array(string => mixed)
	 */
	private $_data;

	/**
	 * 2017-08-10
	 * @var array(string => string)
	 */
	private $_tags;

	/** 2017-08-10 @return self */
	static function s() {static $r; return $r ? $r : $r = new self;}
}