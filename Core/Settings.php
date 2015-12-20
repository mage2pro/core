<?php
namespace Df\Core;
use Df\Typography\Font;
use Magento\Framework\App\Config;
abstract class Settings {
	/**
	 * @param Config\ScopeConfigInterface|Config $config
	 */
	public function __construct(Config\ScopeConfigInterface $config) {$this->_config = $config;}

	/**
	 * 2015-11-09
	 * @param string $key
	 * @return int
	 */
	public function b($key) {return df_bool($this->v($key));}

	/**
	 * 2015-11-09
	 * @param string $key
	 * @return int
	 */
	public function i($key) {return intval($this->v($key));}

	/**
	 * 2015-12-07
	 * Скорректировал метод таким образом,
	 * чтобы при отсутствии значения опции он возвращал null
	 * (а не делал decrypt для значения null или пустой строки).
	 * @param string $key
	 * @return string|null
	 */
	public function p($key) {
		/** @var string|mixed $result */
		$result = $this->v($key);
		return !$result ? null : df_encryptor()->decrypt($result);
	}

	/**
	 * @uses \Magento\Framework\App\Config\Data::getValue()
	 * https://github.com/magento/magento2/blob/2335247d4ae2dc1e0728ee73022b0a244ccd7f4c/lib/internal/Magento/Framework/App/Config/Data.php#L47-L62
	 * @param string $key
	 * @return array|string|null
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
	 * 2015-12-16
	 * @param string $key
	 * @return Font
	 */
	protected function font($key) {
		if (!isset($this->{__METHOD__}[$key])) {
			$this->{__METHOD__}[$key] = new Font($this->json($key));
		}
		return $this->{__METHOD__}[$key];
	}

	/**
	 * 2015-12-16
	 * @param string $key
	 * @return mixed[]
	 */
	private function json($key) {return df_nta(@df_json_decode($this->v($key)));}

	/**
	 * @used-by \Df\Core\Settings::v()
	 * @return string
	 */
	protected function prefix() {return '';}

	/** @var Config\ScopeConfigInterface|Config  */
	private $_config;
}