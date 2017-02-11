<?php
namespace Df\Core;
use Magento\Framework\App\Filesystem\DirectoryList;
class Sync extends O {
	/**
	 * 2015-12-06
	 * @param callable $job
	 * @return mixed
	 */
	private function _execute(callable $job) {
		/** @var mixed $result */
		while($this->isBusy()) {
			$this->wait();
		}
		try {
			$this->lock();
			$result = $job();
		}
		finally {
			$this->unlock();
		}
		return $result;
	}

	/**
	 * 2015-12-06
	 * @override
	 * @see \Df\Core\O::getId()
	 * @return int
	 */
	function getId() {return $this[self::$P__ID];}

	/**
	 * 2015-12-06
	 * @return string
	 */
	private function file() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_path_absolute(DirectoryList::TMP, $this->fileBaseName());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-06
	 * @return string
	 */
	private function fileBaseName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = 'df-core-sync-' . md5($this->getId()) . '.lock';
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-05
	 * @return bool
	 */
	private function isBusy() {return file_exists($this->file());}

	/**
	 * 2015-12-06
	 * @return void
	 */
	private function lock() {df_file_write(DirectoryList::TMP, $this->fileBaseName(), '');}

	/**
	 * 2015-12-06
	 * @return void
	 */
	private function unlock() {df_fs_w(DirectoryList::TMP)->delete($this->fileBaseName());}

	/**
	 * 2015-12-05
	 * @return void
	 */
	function wait() {usleep($this->intervalI());}

	/** @return float */
	private function interval() {return $this[self::$P__INTERVAL];}

	/**
	 * 2015-12-06
	 * @return int
	 */
	private function intervalI() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = round(1000000 * $this->interval());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-06
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__ID, DF_V_STRING_NE)
			->_prop(self::$P__INTERVAL, DF_V_FLOAT)
		;
	}

	/**
	 * 2015-12-06
	 * @used-by df_sync()
	 * @param string $id
	 * @param callable $job
	 * @param float $interval [optional]
	 * @return mixed
	 */
	public static function execute($id, callable $job, $interval = 0.1) {
		return (new self([self::$P__ID => $id, self::$P__INTERVAL => $interval]))->_execute($job);
	}

	/** @var string */
	private static $P__ID = 'id';
	/** @var string */
	private static $P__INTERVAL = 'interval';
}