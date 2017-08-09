<?php
namespace Df\Framework\Module\Dir;
use Df\Framework\Config\FileIterator as dfI;
use Magento\Framework\Config\FileIterator as I;
use Magento\Framework\Module\Dir\Reader as _P;
/**
 * 2017-07-26
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * The purpose of this class is to fix the issue:
 * «bin/magento module:enable --all»: «The file "/composer.json" doesn't exist»
 * https://github.com/mage2pro/stripe/issues/8
 * https://mage2.pro/t/4198
 *
 * @used-by \Df\Framework\Module\PackageInfoFactory::create()
 *
 * Unfortunately, it is impossible to implement this as a plugin because of the recursion:
 * Step 1:
 * @see \Magento\Framework\Config\Reader\Filesystem::read():
 * 		$fileList = $this->_fileResolver->get($this->_fileName, $scope);
 * https://github.com/magento/magento2/blob/2.2.0-RC1.5/lib/internal/Magento/Framework/Config/Reader/Filesystem.php#L124
 * Step 2:
 * @see \Magento\Framework\App\Config\FileResolver::get():
 * 		$iterator = $this->_moduleReader->getConfigurationFiles($filename);
 * https://github.com/magento/magento2/blob/2.2.0-RC1.5/lib/internal/Magento/Framework/App/Config/FileResolver.php#L65
 * Step 3:
 * @see \Magento\Framework\Interception\PluginList\PluginList::getNext():
 * 		$this->_loadScopedData();
 * https://github.com/magento/magento2/blob/2.2.0-RC1.5/lib/internal/Magento/Framework/Interception/PluginList/PluginList.php#L266
 * Step 4:
 * @see \Magento\Framework\Interception\PluginList\PluginList::_loadScopedData():
 * 		$data = $this->_reader->read($scopeCode);
 * https://github.com/magento/magento2/blob/2.2.0-RC1.5/lib/internal/Magento/Framework/Interception/PluginList/PluginList.php#L298
 * Step 5:
 * @see \Magento\Framework\ObjectManager\Config\Reader\Dom\Proxy::read()
 * @see \Magento\Framework\Config\Reader\Filesystem::read()
 * https://github.com/magento/magento2/blob/2.2.0-RC1.5/lib/internal/Magento/Framework/Config/Reader/Filesystem.php#L124
 */
class Reader extends _P {
	/**
	 * 2017-07-26
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see \Magento\Framework\Module\Dir\Reader::getComposerJsonFiles():
	 *		public function getComposerJsonFiles() {
	 *			return $this->getFilesIterator('composer.json');
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/lib/internal/Magento/Framework/Module/Dir/Reader.php#L89-L97 
	 * @used-by \Magento\Framework\Module\PackageInfo::load():
	 * 		$jsonData = $this->reader->getComposerJsonFiles()->toArray();
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/lib/internal/Magento/Framework/Module/PackageInfo.php#L73-L114
	 * @return I
	 */
	function getComposerJsonFiles() {$r = parent::getComposerJsonFiles(); /** @var I $r */return
		dfI::pathsSet($r, array_filter(dfI::pathsGet($r), function($f) {return '/composer.json' !== $f;}))
	;}
}