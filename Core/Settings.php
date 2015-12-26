<?php
namespace Df\Core;
use Df\Typography\Font;
use Magento\Framework\App\Config;
use Magento\Framework\App\ScopeInterface;
abstract class Settings {
	/**
	 * @param Config\ScopeConfigInterface|Config $config
	 */
	public function __construct(Config\ScopeConfigInterface $config) {$this->_config = $config;}

	/**
	 * 2015-11-09
	 * @param string $key
	 * @param null|string|int|ScopeInterface $scope [optional]
	 * @return int
	 */
	public function b($key, $scope = null) {return df_bool($this->v($key, $scope));}

	/**
	 * 2015-11-09
	 * @param string $key
	 * @param null|string|int|ScopeInterface $scope [optional]
	 * @return int
	 */
	public function i($key, $scope = null) {return df_int($this->v($key, $scope));}

	/**
	 * 2015-12-26
	 * @param string $key
	 * @param null|string|int|ScopeInterface $scope [optional]
	 * @return int
	 */
	public function nat($key, $scope = null) {return df_nat($this->v($key, $scope));}

	/**
	 * 2015-12-26
	 * @param string $key
	 * @param null|string|int|ScopeInterface $scope [optional]
	 * @return int
	 */
	public function nat0($key, $scope = null) {return df_nat0($this->v($key, $scope));}

	/**
	 * 2015-12-07
	 * Скорректировал метод таким образом,
	 * чтобы при отсутствии значения опции он возвращал null
	 * (а не делал decrypt для значения null или пустой строки).
	 * @param string $key
	 * @param null|string|int|ScopeInterface $scope [optional]
	 * @return string|null
	 */
	public function p($key, $scope = null) {
		/** @var string|mixed $result */
		$result = $this->v($key, $scope);
		return !$result ? null : df_encryptor()->decrypt($result);
	}

	/**
	 * @uses \Magento\Framework\App\Config\Data::getValue()
	 * https://github.com/magento/magento2/blob/2335247d4ae2dc1e0728ee73022b0a244ccd7f4c/lib/internal/Magento/Framework/App/Config/Data.php#L47-L62
	 *
	 * 2015-12-26
	 * https://mage2.pro/t/357
	 * «The @uses \Magento\Framework\App\Config::getValue() method
	 * has a wrong PHPDoc type for the $scopeCode parameter».
	 *
	 * @param string $key
	 * @param null|string|int|ScopeInterface $scope [optional]
	 * @return array|string|null
	 */
	public function v($key, $scope = null) {
		return $this->_config->getValue(
			$this->prefix() . $key
			/**
			 * 2015-10-09
			 * https://mage2.pro/t/128
			 * https://github.com/magento/magento2/issues/2064
			 */
			, \Magento\Store\Model\ScopeInterface::SCOPE_STORE
			, $scope
		);
	}

	/**
	 * 2015-12-16
	 * @param string $key
	 * @param null|string|int|ScopeInterface $scope [optional]
	 * @return Font
	 */
	protected function _font($key, $scope = null) {
		/** @var string $scopeCode */
		$scopeCode = df_scope_code($scope);
		if (!isset($this->{__METHOD__}[$key][$scopeCode])) {
			$this->{__METHOD__}[$key][$scopeCode] = new Font($this->json($key));
		}
		return $this->{__METHOD__}[$key][$scopeCode];
	}

	/**
	 * 2015-12-16
	 * @param string $key
	 * @param null|string|int|ScopeInterface $scope [optional]
	 * @return mixed[]
	 */
	private function json($key, $scope = null) {
		return df_nta(@df_json_decode($this->v($key, $scope)));
	}

	/**
	 * @used-by \Df\Core\Settings::v()
	 * @return string
	 */
	protected function prefix() {return '';}

	/** @var Config\ScopeConfigInterface|Config  */
	private $_config;
}