<?php
use Magento\Framework\App\Filesystem\DirectoryList as DL;
/**
 * 2015-12-06
 * 2022-11-23 `callable` as an argument type is supported by PHP ≥ 5.4:
 * https://github.com/mage2pro/core/issues/174#user-content-callable
 * @used-by Dfe\GoogleFont\Controller\Index\Index::execute()
 * @param string|object $id
 * @return mixed
 */
function df_sync($id, callable $job, float $interval = 0.1) { /** @var mixed $r */
	$intervalI = round(1000000 * $interval); /** @var int $intervalI */
	$nameShort = 'df-core-sync-' . md5(is_object($id) ? get_class($id) : $id) . '.lock'; /** @var string $nameShort */
	$name = df_sys_path_abs(DL::TMP, $nameShort); /** @var string $name */
	while(file_exists($name)) {
		usleep($intervalI);
	}
	try {
		df_file_write($name, '');
		$r = $job();
	}
	finally {
		df_sys_path_w(DL::TMP)->delete($nameShort);
	}
	return $r;
}