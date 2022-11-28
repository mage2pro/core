<?php
namespace Df\Paypal\Model;
# 2015-07-15
class Config extends \Magento\Paypal\Model\Config {
	/**
	 * 2015-07-15
	 * 1) It adds Russia to the «Merchant Country» list: https://github.com/magento/magento2/issues/1489
	 * 2) «PayPal currency list needs to be updated»: https://github.com/magento/magento2/issues/2157
	 * @override
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
		parent::__construct($scopeConfig, $directoryHelper, $storeManager, $cctypeFactory, $certFactory, $params);
	}
}