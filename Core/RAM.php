<?php
namespace Df\Core;
// 2017-08-10
final class RAM {
	/**
	 * 2017-08-10
	 * @used-by df_cache_clean_tag()   
	 * @used-by \Df\Payment\Method::sgReset()
	 * @param string $tag
	 */
	function clean($tag) {
		if (isset($this->_tags[$tag])) {
			foreach ($this->_tags[$tag] as $k) { /** @var string $k */
				unset($this->_data[$k]);
			}
			unset($this->_tags[$tag]);
		}
	}

	/**
	 * 2017-08-11
	 * 2016-09-04
	 * The following code will return `1`:
	 * 		$a = ['a' => null];
	 * 		echo intval(array_key_exists('a', $a));
	 * https://3v4l.org/9cQOO
	 * @used-by dfcf()
	 * @used-by get()
	 * @param string $k
	 * @return bool
	 */
	function exists($k) {return array_key_exists($k, $this->_data);}

	/**
	 * 2017-08-11
	 * @used-by dfcf()
	 * @param string $k
	 * @return mixed
	 */
	function get($k) {return $this->exists($k) ? $this->_data[$k] : null;}

	/**
	 * 2017-08-11
	 * @used-by df_cache_clean()
	 */
	function reset() {$this->_data = []; $this->_tags = [];}

	/**
	 * 2017-08-10
	 * @used-by dfcf()
	 * @param string $k
	 * @param mixed $v
	 * @param string[] $tags [optional]
	 * @return mixed
	 */
	function set($k, $v, $tags = []) {
		if ($v instanceof ICached) {
			$tags += $v->tags();
		}
		$this->_data[$k] = $v;
		foreach ($tags as $tag) { /** @var string $tag */
			if (!isset($this->_tags[$tag])) {
				$this->_tags[$tag] = [$k];
			}
			else if (!in_array($k, $this->_tags[$tag])) {
				$this->_tags[$tag][] = $k;
			}
		}
		return $v;
	}

	/**
	 * 2017-08-10
	 * @used-by clean()
	 * @used-by exists()
	 * @used-by get()
	 * @used-by set()
	 * @var array(string => mixed)	«Cache Key => Cached Data»
	 */
	private $_data = [];

	/**
	 * 2017-08-10
	 * @used-by clean()
	 * @used-by set()
	 * @var array(string => string[])  «Tag ID => Cache Keys»
	 */
	private $_tags = [];

	/** 2017-08-10 @return self */
	static function s() {static $r; return $r ? $r : $r = new self;}
}