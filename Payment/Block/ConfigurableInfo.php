<?php
namespace Df\Payment\Block;
use Df\Payment\Method;
use Magento\Framework\DataObject;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
use Magento\Framework\Phrase;
/**
 * 2016-05-06
 * По аналогии с @see \Magento\Braintree\Block\Info
 * https://github.com/magento/magento2/blob/135f967/app/code/Magento/Braintree/Block/Info.php
 * https://mage2.pro/t/898/3
 */
class ConfigurableInfo extends \Magento\Payment\Block\ConfigurableInfo {
	/**
	 * 2016-05-21
	 * @override
	 * @see \Magento\Framework\View\Element\AbstractBlock::escapeHtml()
	 * @param array|string $data
	 * @param null $allowedTags
	 * @return array|string
	 */
	public function escapeHtml($data, $allowedTags = null) {return $data;}

	/**
	 * 2016-05-23
	 * @used-by https://code.dmitry-fedyuk.com/m2e/2checkout/blob/1.0.4/view/frontend/templates/info.phtml#L5
	 * @used-by \Dfe\TwoCheckout\Block\Info::_prepareSpecificInformation()
	 * @return bool
	 */
	public function isTest() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->iia(Method::II__TEST);
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-13
	 * @return string
	 */
	public function title() {
		/** @var string $result */
		$result = $this->escapeHtml($this->getMethod()->getTitle());
		if ($this->isTest()) {
			$result .= " ({$this->testModeLabel()} Mode)";
		}
		return $result;
	}

	/**
	 * 2016-05-06
	 * @override
	 * @see \Magento\Payment\Block\ConfigurableInfo::getLabel()
	 * @used-by \Magento\Payment\Block\ConfigurableInfo::setDataToTransfer()
	 * @param string $field
	 * @return Phrase
	 */
	protected function getLabel($field) {return __($field);}

	/**
	 * 2016-05-21
	 * @param string|null $key [optional]
	 * @return II|I|OP|mixed
	 */
	protected function ii($key = null) {
		/** @var II|I|OP $result */
		$result = $this->getInfo();
		return is_null($key) ? $result : $result[$key];
	}

	/**
	 * 2016-05-21
	 * @param ...
	 * @return mixed|array(string => mixed)
	 */
	protected function iia() {
		/** @var string[] $keys */
		$keys = func_get_args();
		return !$keys ? $this->ii()->getAdditionalInformation() : (
			1 === count($keys)
			? $this->ii()->getAdditionalInformation(df_first($keys))
			: dfa_select_ordered($this->ii()->getAdditionalInformation(), $keys)
		);
	}

	/**
	 * 2016-07-13
	 * @param DataObject $result
	 */
	protected function markTestMode(DataObject $result) {
		if (!$this->getIsSecureMode() && $this->isTest()) {
			$result->setData('Mode', __($this->testModeLabel()));
		}
	}

	/**
	 * 2016-07-13
	 * @return string
	 */
	protected function testModeLabel() {return 'Test';}

	/**
	 * 2016-07-13
	 * @return T[]
	 */
	protected function transC() {return $this->transP()->getChildTransactions();}

	/**
	 * 2016-07-13
	 * @return T
	 */
	protected function transP() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_trans_by_payment($this->ii());
		}
		return $this->{__METHOD__};
	}


}