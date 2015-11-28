<?php
namespace Df\Core;
class Lib {
	/**
	 * @used-by load()
	 * @param string $moduleLocalPath
	 * @return \Df\Core\Lib
	 */
	final public function __construct($moduleLocalPath) {
		$this->_moduleLocalPath = $moduleLocalPath;
		$this->checkEnvironment();
		/**
		 * PATH_SEPARATOR — это символ «;» для Windows и «:» для Unix,
		 * он разделяет пути к известным интерпретатору PHP папкам со скриптами.
		 * http://stackoverflow.com/questions/9769052/why-is-there-a-path-separator-constant
		 */
		$this->needAddToIncludePath()
			? set_include_path(get_include_path() . PATH_SEPARATOR . $this->getLibDir())
			: $this->includeScripts()
		;
	}

	/**
	 * @used-by Df_Seo_Model_Processor_Image_Exif::process()
	 * @return void
	 */
	public function restoreErrorReporting() {
		if (isset($this->_errorReporting)) {
			error_reporting($this->_errorReporting);
		}
	}

	/**
	 * @used-by Df_Seo_Model_Processor_Image_Exif::process()
	 * @return void
	 */
	public function setCompatibleErrorReporting() {
		$this->_errorReporting = error_reporting();
		/**
		 * Обратите внимание, что ошибочно использовать ^ вместо &~,
		 * потому что ^ — это побитовое XOR,
		 * и если предыдущее значение error_reporting не содержало getIncompatibleErrorLevels(),
		 * то вызов с оператором ^ наоборот добавит в error_reporting getIncompatibleErrorLevels().
		 */
		error_reporting($this->_errorReporting &~ $this->getIncompatibleErrorLevels());
	}

	/**
	 * @used-by __construct()
	 * @see Df_Pel_Lib::checkEnvironment()
	 * @return void
	 * @throws \Exception
	 */
	protected function checkEnvironment() {}

	/** @return int */
	protected function getIncompatibleErrorLevels() {return 0;}

	/**
	 * Если метод вернёт true, то папка «lib» будет добавлена в @see set_include_path(),
	 * а require_once для файлов из папки «lib» вызван не будет.
	 * Этот алгоритм используется для библиотеки Pel: @see Df_Pel_Lib
	 *
	 * Если метод вернёт false, то папка «lib» не будет добавлена в @see set_include_path(),
	 * а вместо этого будет вызван require_once для всех файлов из папки «lib».
	 * Этот алгоритм используется для всех внутренних библиотек Российской сборки Magento.
	 *
	 * @used-by __construct()
	 * @return bool
	 */
	protected function needAddToIncludePath() {return false;}

	/**
	 * @used-by __construct()
	 * @used-by includeScripts()
	 * @return string
	 */
	private function getLibDir() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				is_dir($this->getLibDirCompiled())
				? $this->getLibDirCompiled()
				: $this->getLibDirStandard()
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getLibDirCompiled() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * @see df_concat_path() здесь использовать ещё нельзя,
			 * потому что библиотеки Российской сборки ещё не загружены
			 */
			$this->{__METHOD__} =
				!defined('COMPILER_INCLUDE_PATH')
				? ''
				: COMPILER_INCLUDE_PATH . DIRECTORY_SEPARATOR . $this->getLibDirLocal()
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Возвращает, например, строку «Df/Core/lib»
	 * @return string
	 */
	private function getLibDirLocal() {
		return $this->_moduleLocalPath  . DIRECTORY_SEPARATOR . 'lib';
	}

	/** @return string */
	private function getLibDirStandard() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * @see df_concat_path() здесь использовать ещё нельзя,
			 * потому что библиотеки Российской сборки ещё не загружены
			 */
			$this->{__METHOD__} = implode(DIRECTORY_SEPARATOR, array(
				BP, 'app', 'code', $this->getLibDirLocal()
			));
		}
		return $this->{__METHOD__};
	}

	/**
	 * @return void
	 * @throws \Exception
	 */
	private function includeScripts() {
		$this->setCompatibleErrorReporting();
		try {
			/** @var string $libPath */
			$libPath = $this->getLibDir() . DIRECTORY_SEPARATOR;
			// Нельзя писать df_path()->children(),
			// потому что библиотеки Российской сборки ещё не загружены
			foreach (\Df\Core\Helper\Path::s()->children($this->getLibDir()) as $child) {
				/** @var string $child */
				$fullPath = $libPath . $child;
				if (is_file($fullPath)) {
					require_once $fullPath;
				}
			}
			$this->restoreErrorReporting();
		}
		catch (\Exception $e) {
			$this->restoreErrorReporting();
			throw $e;
		}
	}

	/** @var int */
	private $_errorReporting;

	/**
	 * @used-by __construct()
	 * @used-by getLibDirLocal()
	 * @var string|null
	 */
	private $_moduleLocalPath = null;

	/**
	 * @used-by Df_Core_Boot::run()
	 * @used-by Df_Core_Boot::initCore()
	 * @used-by Df_YandexMarket_Model_Category_Excel_Document::getPhpExcel()
	 * @param string $key
	 * @return \Df\Core\Lib
	 */
	public static function load($key) {
		/** @var array(string => \Df\Core\Lib) */
		static $cache;
		/** @var \Df\Core\Lib $result */
		if (!isset($cache[$key])) {
			/** @var string[] $keyA */
			$keyA = explode('\\', $key);
			/** @var int $count */
			$count = count($keyA);
			/** @var string $moduleLocalPath */
			$moduleLocalPath =
				1 === $count
				? 'Df' . DIRECTORY_SEPARATOR . $key
				: $keyA[0] . DIRECTORY_SEPARATOR . $keyA[1]
			;
			/** @var string $class */
			$class = 2 < $count ? $key : __CLASS__;
			/**
			 * Нам нужно сохранить в кэше не просто флаг загруженности объекта,
			 * а именно сам объект @uses \Df\Core\Lib,
			 * потому что затем у этого объекта могут вызываться методы:
			 * @see setCompatibleErrorReporting()
			 * @see restoreErrorReporting()
			 * @see Df_Seo_Model_Processor_Image_Exif::process()
			 *
			 * Обратите внимание, что выгоднее делать ключом кэша $key, а не $moduleLocalPath,
			 * чтобы не пересчитывать $moduleLocalPath заново при каждом вызове @see load()
			 */
			$cache[$key] = new $class($moduleLocalPath);
		}
		return $cache[$key];
	}
}