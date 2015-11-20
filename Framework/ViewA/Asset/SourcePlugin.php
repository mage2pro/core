<?php
namespace Df\Framework\ViewA\Asset;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Phrase;
use Magento\Framework\View\Asset\File\NotFoundException;
use Magento\Framework\View\Asset\Source;
use Magento\Framework\View\Asset\LocalInterface;
class SourcePlugin {
	/**
	 * 2015-11-20
	 * Цель перекрытия — улучшение диагностики отсутствия файлов Less:
	 * https://mage2.pro/t/233
	 * «Magento 2 loses the problem Less file name in a «Compilation from source / Cannot read contents from file» error report»
	 * @see \Magento\Framework\View\Asset\Source::getContent()
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/View/Asset/Source.php#L94-L108
	 * @param Source $subject
	 * @param \Closure $proceed
	 * @param LocalInterface $asset
	 * @return bool|string
	 */
	public function aroundGetContent(Source $subject, \Closure $proceed, LocalInterface $asset) {
		/** @var bool|string $result */
		try {
			$result = $proceed($asset);
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