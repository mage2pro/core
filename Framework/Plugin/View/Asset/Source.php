<?php
namespace Df\Framework\Plugin\View\Asset;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Phrase;
use Magento\Framework\View\Asset\File\NotFoundException;
use Magento\Framework\View\Asset\LocalInterface;
use Magento\Framework\View\Asset\Source as Sb;
class Source {
	/**
	 * 2015-11-20
	 * Цель перекрытия — улучшение диагностики отсутствия файлов Less:
	 * https://mage2.pro/t/233
	 * «Magento 2 loses the problem Less file name in a «Compilation from source / Cannot read contents from file» error report»
	 * @see \Magento\Framework\View\Asset\Source::getContent()
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/View/Asset/Source.php#L94-L108
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param LocalInterface $asset
	 * @return bool|string
	 */
	function aroundGetContent(Sb $sb, \Closure $f, LocalInterface $asset) {
		/** @var bool|string $result */
		try {
			$result = $f($asset);
		}
		/**
		 * @see \Magento\Framework\Filesystem\Driver\File::fileGetContents()
		 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Filesystem/Driver/File.php#L148-L153
		 */
		catch (FileSystemException $e) {
			throw new NotFoundException(new Phrase(
				'Unable to resolve the source file for "%1"', [$asset->getFilePath()]
			), 0, $e);
		}
		return $result;
	}
}