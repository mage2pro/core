<?php
namespace Df\Paypal\Model;
class Config extends \Magento\Paypal\Model\Config {
	/**
	 * 2015-07-15
	 * Цель перекрытия —
	 * добавление России в список «Merchant Country»:
	 * https://github.com/magento/magento2/issues/1489
	 * «PayPal currency list need to be updated»:
	 * https://github.com/magento/magento2/issues/2157
	 * @override
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	 * @param \Magento\Directory\Helper\Data $directoryHelper
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Payment\Model\Source\CctypeFactory $cctypeFactory
	 * @param \Magento\Paypal\Model\CertFactory $certFactory
	 * @param array $params
	 */
	function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Directory\Helper\Data $directoryHelper,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Payment\Model\Source\CctypeFactory $cctypeFactory,
		\Magento\Paypal\Model\CertFactory $certFactory,
		$params = []
	) {
		$this->_supportedCurrencyCodes[]= 'RUB';
		$this->_supportedCountryCodes[]= 'RU';
		parent::__construct(
			$scopeConfig, $directoryHelper, $storeManager, $cctypeFactory, $certFactory, $params
		);
	}
}