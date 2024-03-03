<?php
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * 2017-02-07
 * @used-by df_lang_zh()
 * @used-by df_lang_ru()
 * @used-by \Df\Payment\Block\Info::extended()
 * @used-by \Df\Payment\Method::test()
 * @used-by \Df\Payment\Source\API\Key\Testable::test()
 * @used-by \Dfe\Moip\FE\Webhooks::onFormInitialized()
 * @used-by \Dfe\Paymill\Settings::test3DS()
 * $args — массив либо пустой, либо из 2 элементов с целочисленными индексами 0 и 1.
 * Если массив $args пуст, то функция возвращает $r.
 * Если массив $args непуст, то функция возвращает:
 * 		$args[0] при истинности $r
 *		$args[1] при ложности $r
 * @return mixed
 */
function df_b(array $args, bool $r) {return !$args ? $r : $args[intval(!$r)];}

/**
 * @used-by \Df\Core\Html\Tag::openTagWithAttributesAsText()
 * @param mixed $v
 * @return mixed
 */
function df_nop($v) {return $v;}

/**
 * 2015-12-06
 * 2022-11-23 `callable` as an argument type is supported by PHP ≥ 5.4:
 * https://github.com/mage2pro/core/issues/174#user-content-callable
 * @param string|object $id
 * @return mixed
 */
function df_sync($id, callable $job, float $interval = 0.1) { /** @var mixed $r */
	$intervalI = round(1000000 * $interval); /** @var int $intervalI */
	$nameShort = 'df-core-sync-' . md5(is_object($id) ? get_class($id) : $id) . '.lock'; /** @var string $nameShort */
	$name = df_sys_path_abs(DirectoryList::TMP, $nameShort); /** @var string $name */
	while(file_exists($name)) {
		usleep($intervalI);
	}
	try {
		df_file_write($name, '');
		$r = $job();
	}
	finally {
		df_fs_w(DirectoryList::TMP)->delete($nameShort);
	}
	return $r;
}