<?php
namespace Df\Framework\Plugin\Css\PreProcessor\File\FileList;
use Magento\Framework\Css\PreProcessor\File\FileList\Collator as Sb;
use Magento\Framework\View\File as F;
# 2020-04-16
final class Collator {
	/**
	 * 2020-04-16
	 * "The `_module.less` files of my modules
	 * should be added to `styles-m.css` and `styles-m.css` (via the `@magento_import` directive)
	 * after all other `_module.less` files (of all other modules)": https://github.com/mage2pro/core/issues/97
	 * @see \Magento\Framework\Css\PreProcessor\File\FileList\Collator::collate():
	 *		public function collate($files, $filesOrigin) {
	 *			foreach ($files as $file) {
	 *				$fileId = substr($file->getFileIdentifier(), strpos($file->getFileIdentifier(), '|'));
	 *				foreach (array_keys($filesOrigin) as $identifier) {
	 *					if (false !== strpos($identifier, $fileId)) {
	 *						unset($filesOrigin[$identifier]);
	 *					}
	 *				}
	 *				$filesOrigin[$file->getFileIdentifier()] = $file;
	 *			}
	 *			return $filesOrigin;
	 *		}
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Css/PreProcessor/File/FileList/Collator.php#L15-L34
	 * https://github.com/magento/magento2/blob/2.3.4/lib/internal/Magento/Framework/Css/PreProcessor/File/FileList/Collator.php#L15-L34
	 * $filesOrigin looks like:
	 * 	{
	 *		"|module:Amazon_Login|file:_module.less": \Magento\Framework\View\File {},
	 * 		...
	 *		"|module:VegAndTheCity_Core|file:_module.less": \Magento\Framework\View\File {}
	 *	}   
	 * $files is a \Magento\Framework\View\File[]
	 * @used-by \Magento\Framework\View\File\FileList::replace():
	 * 		$this->files = $this->collator->collate($files, $this->files);
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/View/File/FileList.php#L72-L81
	 * https://github.com/magento/magento2/blob/2.3.4/lib/internal/Magento/Framework/View/File/FileList.php#L72-L81
	 * @param Sb $sb
	 * @param array(string => F) $r
	 */
	function afterCollate(Sb $sb, array $r):string {
		if ($r) {
			$my = array_flip(df_modules_my()); /** @var array(string => int) $my */
			/**
			 * @param F $f
			 * @return int
			 */
			$f = function(F $f) use($my) {return dfa($my, $f->getModule(), -1);};
			$r = df_sort($r, function(F $a, F $b) use ($f) {return $f($a) - $f($b);});
		}
		return $r;
	}
}