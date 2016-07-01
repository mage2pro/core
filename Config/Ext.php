<?php
namespace Df\Config;
use Composer\Package\CompletePackage as CP;
use Composer\Package\Package as P;
use Composer\Package\PackageInterface as IP;
/**
 * 2016-07-01
 * Описываемая этим классом информация отображается в административном разделе модуля
 * прямо под его заголовком.
 * Нельзя называть класс просто Extension, потому что такие имена классов
 * уже зарезервированы для одной из технологий Magento 2.
 */
class Ext {
	/**
	 * 2016-07-01
	 * Идентификатор пакета Composer. Из пакета мы извлекаем информацию.
	 * @param string $name
	 */
	public function __construct($name) {$this->_name = $name;}

	/**
	 * 2016-07-01
	 * @return string
	 */
	public function url() {return $this->package()->getHomepage();}

	/**
	 * 2016-07-01
	 * @return CP|P|IP
	 */
	private function package() {return df_package($this->_name);}

	/**
	 * 2016-07-01
	 * Идентификатор пакета Composer.
	 * Из пакета мы извлекаем информацию.
	 * @var string
	 */
	private $_name;
}