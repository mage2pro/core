<?php
namespace Df\Core;
class Sync extends O {
	/**
	 * 2015-12-05
	 * @return bool
	 */
	public function busy() {return file_exists($this->lockFile());}

	/**
	 * 2015-12-05
	 * @return void
	 */
	public function wait() {usleep($this->interval());}
}