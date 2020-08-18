<?php
namespace Df\Core;
// 2015-08-13
final class State {
	/** @return self */
	static function s() {static $r; return $r ? $r : $r = new self;}
}