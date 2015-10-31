<?php
namespace Df\Core;
use Magento\Framework\App\Config;
abstract class Settings {
	/**
	 * @param Config\ScopeConfigInterface|Config $config
	 */
	public function __construct(Config\ScopeConfigInterface $config) {
		$this->_config = $config;
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function p($key) {return df_encryptor()->decrypt($this->v($key));}

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function v($key) {
		return $this->_config->getValue(
			$this->prefix() . $key
			/**
			 * 2015-10-09
			 * https://mage2.pro/t/128
			 * https://github.com/magento/magento2/issues/2064
			 */
			, \Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}

	/**
	 * @used-by \Df\Core\Settings::v()
	 * @return string
	 */
	protected function prefix() {return '';}

	/** @var Config\ScopeConfigInterface|Config  */
	private $_config;
}