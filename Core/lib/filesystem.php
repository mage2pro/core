<?php
/** @return \Df\Core\Helper\Path */
function df_path() {return \Df\Core\Helper\Path::s();}

/**
 * 2015-08-14
 * https://mage2.pro/t/57
 * https://mage2.ru/t/92
 *
 * 2015-09-02
 * Метод @uses \Magento\Framework\Module\Dir\Reader::getModuleDir()
 * в качестве разделителя путей использует не DIRECTORY_SEPARATOR, а /
 *
 * @used-by \Df\Core\O::modulePath()
 * @param string $moduleName
 * @param string $type [optional]
 * @return string
 * @throws \InvalidArgumentException
 */
function df_module_dir($moduleName, $type = '') {
	/** @var \Magento\Framework\ObjectManagerInterface $om */
	$om = \Magento\Framework\App\ObjectManager::getInstance();
	/** @var \Magento\Framework\Module\Dir\Reader $reader */
	$reader = $om->get('Magento\Framework\Module\Dir\Reader');
	return $reader->getModuleDir($type, $moduleName);
}

/**
 * 2015-11-15
 * @used-by \Df\Core\O::modulePath()
 * @param string $moduleName
 * @return string
 * @throws \InvalidArgumentException
 */
function df_module_dir_etc($moduleName) {
	return df_module_dir($moduleName, \Magento\Framework\Module\Dir::MODULE_ETC_DIR);
}

/**
 * 2015-11-15
 * @param string $moduleName
 * @param string $localPath [optional]
 * @return string
 * @throws \InvalidArgumentException
 */
function df_module_path($moduleName, $localPath = '') {
	/** @var array(string => array(string => string)) $cache */
	static $cache;
	if (!isset($cache[$moduleName][$localPath])) {
		/**
		 * 2015-09-02
		 * Метод @uses \Magento\Framework\Module\Dir\Reader::getModuleDir()
		 * и, соответственно, @uses df_module_dir()
		 * в качестве разделителя путей использует не DIRECTORY_SEPARATOR, а /,
		 * поэтому и мы поступаем так же.
		 */
		$cache[$moduleName][$localPath] = df_concat_path(df_module_dir($moduleName), $localPath);
	}
	return $cache[$moduleName][$localPath];
}

/**
 * 2015-11-28
 * http://stackoverflow.com/a/10368236
 * @param string $fileName
 * @return string
 */
function df_file_ext($fileName) {return pathinfo($fileName, PATHINFO_EXTENSION);}

/**
 * Возвращает неиспользуемое имя файла в заданной папке $directory по заданному шаблону $template.
 * Результатом всегда является непустая строка.
 * @used-by Autostyler_Import_Model_Action::getLogFilePath()
 * @used-by Df_1C_Helper_Data::logger()
 * @used-by df_report()
 * @used-by Df_Core_Model_Action::getResponseLogFileName()
 * @used-by Df_Core_Model_SimpleXml_Generator_Document::createLogger()
 * @used-by Df_YandexMarket_Helper_Data::getLogger()
 * @param string $directory
 * @param string $template
 * @param string $datePartsSeparator [optional]
 * @return string
 */
function df_file_name($directory, $template, $datePartsSeparator = '-') {
	return \Df\Core\Model\Fs\GetNotUsedFileName::r($directory, $template, $datePartsSeparator);
}

/**
 * @param string $filePath
 * @param string $contents
 * @return void
 * @throws Exception
 */
function df_file_put_contents($filePath, $contents) {
	df_param_string_not_empty($filePath, 0);
	df_path()->createAndMakeWritable($filePath);
	/** @var int|bool $r */
	$r = file_put_contents($filePath, $contents);
	df_assert(false !== $r);
}

/**
 * 2015-04-01
 * Раньше алгоритм был таким: return preg_replace('#\.[^.]*$#', '', $file)
 * Новый вроде должен работать быстрее?
 * http://stackoverflow.com/a/22537165
 * @used-by Df_Adminhtml_Catalog_Product_GalleryController::uploadActionDf()
 * @used-by
 * @param string $file
 * @return mixed
 */
function df_strip_ext($file) {return pathinfo($file, PATHINFO_FILENAME);}


 