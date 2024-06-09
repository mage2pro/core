<?php
use Magento\Framework\Filesystem\Directory\Read as DirectoryRead;
use Magento\Framework\Filesystem\Directory\ReadInterface as IDirectoryRead;

/**
 * 2015-12-06 A @uses \Magento\Framework\Filesystem\Directory\Read::getAbsolutePath() result ends with «/».
 * 2023-07-26
 * 1) "`df_path_absolute` → `df_sys_path_abs`": https://github.com/mage2pro/core/issues/272
 * 2) "`df_sys_reader()` can not be used with an arbitrary path
 * because of `\Magento\Framework\Filesystem\DirectoryList::assertCode()`": https://github.com/mage2pro/core/issues/271
 * 3) "`df_path_absolute()` is wrongly implemented": https://github.com/mage2pro/core/issues/270
 * @see df_path_abs()
 * @used-by df_media_path_abs()
 * @used-by df_product_image_tmp_path2abs()
 * @used-by df_product_images_path()
 * @used-by df_sync()
 */
function df_sys_path_abs(string $p, string $suf = ''):string {return df_prepend(
	df_trim_ds_left($suf), df_sys_reader($p)->getAbsolutePath()
);}

/**
 * 2015-11-30
 * 2023-07-26
 * "`df_sys_reader()` can not be used with an arbitrary path
 *  because of @see \Magento\Framework\Filesystem\DirectoryList::assertCode()": https://github.com/mage2pro/core/issues/271
 * 2024-06-09
 * 		private function assertCode($code) {
 * 			if (!isset($this->directories[$code])) {
 * 				throw new \Magento\Framework\Exception\FileSystemException(
 * 					new \Magento\Framework\Phrase('Unknown directory type: \'%1\'', [$code])
 * 				);
 * 			}
 * 		}
 * https://github.com/magento/magento2/blob/2.4.7/lib/internal/Magento/Framework/Filesystem/DirectoryList.php#L233-L247
 * @used-by df_media_reader()
 * @used-by df_path_rel()
 * @used-by df_sys_path_abs()
 * @return DirectoryRead|IDirectoryRead
 */
function df_sys_reader(string $type) {return df_fs()->getDirectoryRead($type);}