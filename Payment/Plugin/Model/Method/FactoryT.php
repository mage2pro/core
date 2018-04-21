<?php
namespace Df\Payment\Plugin\Model\Method;
use Df\Payment\Method as M;
use Magento\Payment\Model\Method\Factory as Sb;
use Magento\Payment\Model\MethodInterface as IM;
// 2017-03-30
final class FactoryT {
	/**
	 * 2017-03-30
	 * Цель перекрытия — сделать потомков класса @see M одиночками:
	 * @see \Magento\Payment\Model\Method\Factory::create()
	 * https://github.com/magento/magento2/blob/2.1.5/app/code/Magento/Payment/Model/Method/Factory.php#L30-L47
	 * 2017-08-28   
	 * 1) @used-by \Magento\Payment\Model\Config::getActiveMethods():
	 *		public function getActiveMethods() {
	 *			$methods = [];
	 *			foreach ($this->_scopeConfig->getValue('payment', ScopeInterface::SCOPE_STORE, null)
	 *				as $code => $data
	 *			) {
	 *				if (isset($data['active'], $data['model']) && (bool)$data['active']) {
	 *					$methodModel = $this->_paymentMethodFactory->create($data['model']);
	 *					$methodModel->setStore(null);
	 *					if ($methodModel->getConfigData('active', null)) {
	 *						$methods[$code] = $methodModel;
	 *					}
	 *				}
	 *			}
	 *			return $methods;
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.0-rc2.2/app/code/Magento/Payment/Model/Config.php#L88-L108
	 * 2) @used-by \Magento\Payment\Helper\Data::getMethodInstance():
	 *		public function getMethodInstance($code) {
	 *			$class = $this->scopeConfig->getValue(
	 *				$this->getMethodModelConfigName($code),
	 *				\Magento\Store\Model\ScopeInterface::SCOPE_STORE
	 *			);
	 *			if (!$class) {
	 *				throw new \UnexpectedValueException('Payment model name is not provided in config!');
	 *			}
	 *			return $this->_methodFactory->create($class);
	 *		}  
	 * https://github.com/magento/magento2/blob/2.2.0-rc2.2/app/code/Magento/Payment/Helper/Data.php#L95-L115
	 * 3) @used-by \Magento\Payment\Helper\Data::getStoreMethods() (deprecated):
	 *		$methodInstance = $this->_methodFactory->create($model);
	 *		$methodInstance->setStore($store);
	 *		if (!$methodInstance->isAvailable($quote)) {
	 *			continue;
	 *		}
	 *		$res[] = $methodInstance;
	 * https://github.com/magento/magento2/blob/2.2.0-rc2.2/app/code/Magento/Payment/Helper/Data.php#L117-L167
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param string $c
	 * @param array $d [optional]
	 * @return M|IM
	 */
	function aroundCreate(Sb $sb, \Closure $f, $c, $d = []) {return 
		is_a($c, M::class, true) ? M::sg($c) : $f($c, $d)
	;}
}