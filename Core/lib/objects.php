<?php
use Magento\Framework\ObjectManager\ConfigInterface;
use Magento\Framework\ObjectManager\Config\Config;
use Magento\Framework\ObjectManager\Config\Compiled;

/**
 * 2016-05-06
 * By analogy with https://github.com/magento/magento2/blob/135f967/lib/internal/Magento/Framework/ObjectManager/TMap.php#L97-L99
 * @param string $type
 * @return bool
 */
function df_class_exists($type) {
	/**
	 * 2016-05-23
	 * Намеренно не объединяем строки в единное выражение,
	 * чтобы собака @ не подавляла сбои первой строки.
	 * Такие сбои могут произойти при синтаксических ошибках в проверяемом классе
	 * (похоже, getInstanceType как-то загружает код класса).
	 */
	/** @var string $type */
	$type = df_om_config()->getInstanceType(df_om_config()->getPreference($type));
	return @class_exists($type);
}

/**
 * 2016-01-06
 * @param string $resultClass
 * @param array(string => mixed) $params [optional]
 * @return \Magento\Framework\DataObject|object
 */
function df_create($resultClass, array $params = []) {
	return df_om()->create($resultClass, ['data' => $params]);
}

/**
 * @param string $type
 * @return mixed
 */
function df_o($type) {
	/** @var array(string => mixed) */
	static $cache;
	if (!isset($cache[$type])) {
		$cache[$type] = df_om()->get($type);
	}
	return $cache[$type];
}

/**
 * 2015-08-13
 * @used-by df_o()
 * @used-by df_ic()
 * @return \Magento\Framework\ObjectManagerInterface|\Magento\Framework\App\ObjectManager
 */
function df_om() {return \Magento\Framework\App\ObjectManager::getInstance();}

/**
 * 2016-05-06
 * @return ConfigInterface|Config|Compiled
 */
function df_om_config() {return df_o(ConfigInterface::class);}


