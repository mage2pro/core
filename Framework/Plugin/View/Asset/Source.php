<?php
namespace Df\Framework\Plugin\View\Asset;
use Magento\Framework\Exception\FileSystemException as EFileSystem;
use Magento\Framework\Phrase;
use Magento\Framework\View\Asset\File\NotFoundException as ENotFound;
use Magento\Framework\View\Asset\LocalInterface as ILocalAsset;
use Magento\Framework\View\Asset\Source as Sb;
class Source {
	/**
	 * 2015-11-20
	 * Цель перекрытия — улучшение диагностики отсутствия файлов Less: https://mage2.pro/t/233
	 * «Magento 2 loses the problem Less file name
	 * in a «Compilation from source / Cannot read contents from file» error report»
	 * @see \Magento\Framework\View\Asset\Source::getContent()
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/View/Asset/Source.php#L94-L108
	 * @return bool|string
	 */
	function aroundGetContent(Sb $sb, \Closure $f, ILocalAsset $a) {/** @var bool|string $r */
		try {$r = $f($a);}
		/**
		 * @see \Magento\Framework\Filesystem\Driver\File::fileGetContents()
		 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Filesystem/Driver/File.php#L148-L153
		 */
		catch (EFileSystem $e) {
			# 2019-12-30 Dmitry Fedyuk https://github.com/mage2pro
			# «Unable to resolve the source file for 'frontend/bs_eren/bs_eren3/en_US/quickview/bxslider.js'»
			# https://github.com/royalwholesalecandy/core/issues/70
			df_log_l($this, df_cc_n("Unable to resolve the source file for {$a->getFilePath()}", df_referer()));
			throw new ENotFound(new Phrase('Unable to resolve the source file for "%1"', [$a->getFilePath()]), 0, $e);
		}
		return $r;
	}
}