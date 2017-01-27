<?php
use Composer\Package\CompletePackage as CP;
use Composer\Package\Package as P;
use Composer\Package\PackageInterface as IP;
use Composer\Repository\ArrayRepository;
use Composer\Repository\BaseRepository;
use Composer\Repository\ComposerRepository;
use Composer\Repository\RepositoryInterface;
use Df\Framework\Composer\ComposerInformation as DCI;
use Magento\Framework\Composer\ComposerInformation as CI;
/**
 * 2016-07-01
 * @return DCI;
 */
function df_composer() {return df_o(DCI::class);}

/**
 * 2017-01-10
 * Эта функция, в отличие от df_package(), считывает информацию
 * не из общего файла с установочной информацией всех пакетов,
 * а из локального файла «composer.json» того модуля, которому принадлежит класс $c.
 * @param string|object $m
 * Функция допускает в качесте $m:
 * 1) Имя модуля. Например: «Df_Core».
 * 2) Имя класса. Например: «Dfe\Stripe\Method».
 * 3) Объект. Сводится к случаю 2 посредством @see get_class()
 * @param string|string[]|null $k [optional]
 * @param mixed|null $v [optional]
 * @return string|array(string => mixed)|null
 */
function df_composer_json($m, $k = null, $v = null) {
	/** @var array(string => array(string => mixed)) $cache */
	static $cache;
	if (!isset($cache[$m = df_module_name($m)])) {
		/** @var string $packagePath */
		$packagePath = df_module_path($m);
		// 2017-01-10
		// У модулей «Df_*» общий файл «composer.json», и он расположен
		// в родительской папке этих модулей.
		if (df_starts_with($m, 'Df_')) {
			$packagePath = dirname($packagePath);
		}
		/** @var string $filePath */
		$filePath = "$packagePath/composer.json";
		$cache[$m] = !file_exists($filePath) ? [] : df_json_decode(file_get_contents($filePath));
	}
	return dfak($cache[$m], $k, $v);
}

/**
 * 2016-06-26
 * @return CI;
 */
function df_composer_m() {return df_o(CI::class);}

/**
 * 2016-07-01
 * @return RepositoryInterface|ArrayRepository|BaseRepository|ComposerRepository
 */
function df_composer_repository_l() {return df_composer()->locker()->getLockedRepository();}

/**
 * 2017-01-25
 * @used-by df_sentry_m()
 * @used-by \Df\Sentry\Client::__construct()
 * @used-by \Df\Sentry\Client::getUserAgent()
 * @used-by \Dfe\Klarna\UserAgent::__construct()
 * @return string
 */
function df_core_version() {return dfcf(function() {return df_package_version('Df_Core');});}

/**
 * 2016-07-01
 * The method returns a package's information from its composer.json file.
 * The method can be used not only for the custom packages,
 * but for the standard Magento packages too.
 * «How is @see \Magento\Framework\Composer\ComposerInformation::getInstalledMagentoPackages()
 * implemented and used?» https://mage2.pro/t/1796
 * 2017-01-10
 * Для чтения информации из локального файла «composer.json» модуля
 * используйте функцию @see df_composer_json().
 * @used-by df_package_version()
 * @used-by \Df\Config\Ext::package()
 * @param string $name
 * @return CP|P|IP|null
 */
function df_package($name) {
	/** @var array(string => P|IP) $packages */
	static $packages;
	if (!isset($packages[$name])) {
		/**
		 * 2016-07-05
		 * Раньше алгоритм был таким: df_composer_repository_l()->findPackage($name, '*')
		 * Однако устаревшие версии Composer
		 * (например, 1.0.0-alpha10, которая используется в Magento 2.0.0)
		 * не поддерживают синтаксис со звёздочкой.
		 * Поэтому нашёл другой путь, причём он даже более простой.
		 */
		/** @var CP|P|IP|null $result */
		$result = null;
		foreach (df_composer_repository_l()->getPackages() as $package) {
			/** @var CP|P|IP $package */
			if ($name === $package->getName()) {
				$result = $package;
				break;
			}
		}
		$packages[$name] = df_n_set($result);
	}
	return df_n_get($packages[$name]);
}

/**
 * 2016-06-26
 * The method can be used not only for the custom packages,
 * but for the standard Magento packages too.
 * «How to programmatically get an extension's version from its composer.json file?»
 * https://mage2.pro/t/1798
 * 2016-12-23
 * Эта функция берёт свой результат не из файла composer.json пакета,
 * а из общего файла с установочной информацией всех пакетов,
 * поэтому простого редактирования файла composer.json пакета недостаточно
 * для обновления значения этой функции, надо ещё переустановить (обновить)
 * посредством Composer.
 * 2017-01-10
 * Отныне эта функция:
 * 1) Допускает в качестве $name не только имя установочного пакета,
 * но и имя модуля, класса, или даже объект.
 * 2) Если в качестве $name указан модуль, класс или объект,
 * то считывает информацию из локального файла «composer.json» модуля.
 * @used-by df_sentry()
 * @used-by df_sentry_m()
 * @used-by \Df\Payment\Method::action()
 * @used-by \Df\Sentry\Client::version()
 * @used-by \Dfe\CheckoutCom\Charge::metaData()
 * @param string|object $name [optional]
 * @return string|null
 */
function df_package_version($name) {
	/** @var string|null $result */
	if (is_object($name)) {
		$name = get_class($name);
	}
	// 2017-01-10
	// Если первая буква — строчная, то $name — имя пакета,
	// иначе — имя класса или модуля.
	if (!ctype_lower($name[0])) {
		$result = df_composer_json($name, 'version');
	}
	else {
		/** @var P|IP|null $package */
		$package = df_package($name);
		/**
		 * 2016-07-01
		 * By analogy with
		 * @see \Magento\Framework\Composer\ComposerInformation::getInstalledMagentoPackages()
		 * https://mage2.pro/t/1796
		 */
		$result = !$package ? null : $package->getPrettyVersion();
	}
	return $result;
}