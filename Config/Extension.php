<?php
namespace Df\Config;
/**
 * 2016-07-01
 * Описываемая этим классом информация отображается в административном разделе модуля
 * прямо под его заголовком.
 */
class Extension {
	/**
	 * 2016-07-01
	 * Идентификатор пакета Composer. Из пакета мы извлекаем информацию.
	 * @param string $packageName
	 */
	public function __construct($packageName) {
		$this->_packageName = $packageName;
	}

	/**
	 * 2016-07-01
	 * @return string
	 */
	public function url() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = '';
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-01
	 * Идентификатор пакета Composer.
	 * Из пакета мы извлекаем информацию.
	 * @var string
	 */
	private $_packageName;
}