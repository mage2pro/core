<?php
namespace Df\Shipping;
use Magento\Framework\DataObject as _DO;
use Magento\Quote\Model\Quote\Address\RateResult\Error;
use Magento\Shipping\Model\Carrier\AbstractCarrier as AC; // 2018-04-17 It is used by PHPDoc.
use Magento\Shipping\Model\Carrier\AbstractCarrierInterface as IAC;
use Magento\Shipping\Model\Carrier\CarrierInterface as IC;
// 2018-04-17
/** @see \Doormall\Shipping\Method */
abstract class Method implements IC, IAC {
	/**
	 * 2018-04-17
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see IAC::checkAvailableShipCountries()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrierInterface.php#L79-L84
	 * @see AC::checkAvailableShipCountries():
	 *	public function checkAvailableShipCountries(\Magento\Framework\DataObject $request) {
	 *		$speCountriesAllow = $this->getConfigData('sallowspecific');
	 *		if ($speCountriesAllow && $speCountriesAllow == 1) {
	 *			$showMethod = $this->getConfigData('showmethod');
	 *			$availableCountries = [];
	 *			if ($this->getConfigData('specificcountry')) {
	 *				$availableCountries = explode(',', $this->getConfigData('specificcountry'));
	 *			}
	 *			if ($availableCountries && in_array($request->getDestCountryId(), $availableCountries)) {
	 *				return $this;
	 *			}
	 *			elseif ($showMethod && (!$availableCountries || $availableCountries && !in_array(
	 *				$request->getDestCountryId(),
	 *				$availableCountries
	 *			))) {
	 *				$error = $this->_rateErrorFactory->create();
	 *				$error->setCarrier($this->_code);
	 *				$error->setCarrierTitle($this->getConfigData('title'));
	 *				$errorMsg = $this->getConfigData('specificerrmsg');
	 *				$error->setErrorMessage(
	 *					$errorMsg ? $errorMsg : __(
	 *						'Sorry, but we can\'t deliver to the destination country with this shipping module.'
	 *					)
	 * 				);
	 *				return $error;
	 *			}
	 * 			else {
	 *				return false;
	 *			}
	 *		}
	 *		return $this;
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrier.php#L279-L323
	 * @param _DO $r
	 * @return $this|false|Error
	 */
	function checkAvailableShipCountries(_DO $r) {return $this;}

	/**
	 * 2018-04-17
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see IAC::debugData()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrierInterface.php#L178-L185
	 * @see AC::debugData():
	 *	public function debugData($debugData) {
	 *		$this->_debug($debugData);
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrier.php#L590-L599
	 * @param mixed $d
	 * @return void
	 */
	function debugData($d) {}

	/**
	 * 2018-04-17
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see IAC::getCarrierCode()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrierInterface.php#L187-L193
	 * @see AC::getCarrierCode():
	 *	public function getCarrierCode() {
	 *		return $this->_code;
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrier.php#L601-L609
	 * @used-by getConfigData()
	 * @return string
	 */
	function getCarrierCode() {return self::codeS();}

	/**
	 * 2018-04-17
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see IAC::getConfigData()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrierInterface.php#L15-L22
	 * @see AC::getConfigData():
	 * 	public function getConfigData($field) {
	 *		if (empty($this->_code)) {
	 *			return false;
	 *		}
	 *		$path = 'carriers/' . $this->_code . '/' . $field;
	 *		return $this->_scopeConfig->getValue(
	 *			$path,
	 *			\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
	 *			$this->getStore()
	 *		);
	 * 	}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrier.php#L122-L140
	 * 1) @used-by \Magento\Shipping\Model\Shipping::collectCarrierRates():
 	 *		if ($carrier->getConfigData('shipment_requesttype')) {
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Shipping.php#L270-L306
	 * 2) @used-by \Magento\Shipping\Model\Shipping::collectCarrierRates():
	 *		if ($carrier->getConfigData('showmethod') == 0 && $result->getError()) {
	 *			return $this;
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Shipping.php#L311-L313
	 * @param string $k
	 * @return mixed
	 */
	final function getConfigData($k) {return df_cfg("carriers/{$this->getCarrierCode()}/$k", $this->_storeId);}

	/**
	 * 2018-04-17
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see IAC::getContainerTypes()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrierInterface.php#L53-L60
	 * @see AC::getContainerTypes():
	 *	public function getContainerTypes(\Magento\Framework\DataObject $params = null) {
	 *		return [];
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrier.php#L190-L200
	 * @param _DO $p|null
	 * @return array
	 */
	function getContainerTypes(_DO $p = null) {return [];}

	/**
	 * 2018-04-17
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see IAC::getContentTypes()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrierInterface.php#L195-L202
	 * @see AC::getContentTypes():
	 *	public function getContentTypes(\Magento\Framework\DataObject $params = null) {
	 *		return [];
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrier.php#L611-L621
	 * @param _DO $p|null
	 * @return array
	 */
	function getContentTypes(_DO $p = null) {return [];}

	/**
	 * 2018-04-17
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see IAC::getCustomizableContainerTypes()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrierInterface.php#L62-L68
	 * @see AC::getCustomizableContainerTypes():
	 *	public function getCustomizableContainerTypes() {
	 *		return $this->_customizableContainerTypes;
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrier.php#L257-L265
	 * @return array
	 */
	function getCustomizableContainerTypes() {return [];}

	/**
	 * 2018-04-17
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see IAC::getDeliveryConfirmationTypes()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrierInterface.php#L70-L77
	 * @see AC::getDeliveryConfirmationTypes():
	 *	public function getDeliveryConfirmationTypes(\Magento\Framework\DataObject $params = null) {
	 *		return [];
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrier.php#L267-L277
	 * @param _DO $p|null
	 * @return array
	 */
	function getDeliveryConfirmationTypes(_DO $p = null) {return [];}

	/**
	 * 2018-04-17
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see IAC::getFinalPriceWithHandlingFee()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrierInterface.php#L135-L142
	 * @see AC::getFinalPriceWithHandlingFee():
	 *	public function getFinalPriceWithHandlingFee($cost) {
	 *		$handlingFee = (float)$this->getConfigData('handling_fee');
	 *		$handlingType = $this->getConfigData('handling_type');
	 *		if (!$handlingType) {
	 *			$handlingType = self::HANDLING_TYPE_FIXED;
	 *		}
	 *		$handlingAction = $this->getConfigData('handling_action');
	 *		if (!$handlingAction) {
	 *			$handlingAction = self::HANDLING_ACTION_PERORDER;
	 *		}
	 *		return $handlingAction == self::HANDLING_ACTION_PERPACKAGE ? $this->_getPerpackagePrice(
	 *			$cost,
	 *			$handlingType,
	 *			$handlingFee
 	 *		) : $this->_getPerorderPrice(
	 *			$cost,
	 *			$handlingType,
	 *			$handlingFee
	 *		);
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrier.php#L449-L476
	 * @param float $cost
	 * @return float
	 */
	function getFinalPriceWithHandlingFee($cost) {return $cost;}

	/**
	 * 2018-04-17
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see IAC::getSortOrder()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrierInterface.php#L127-L133
	 * @see AC::getSortOrder():
	 *	public function getSortOrder() {
	 *		return $this->getConfigData('sort_order');
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrier.php#L380-L388
	 * @return array
	 */
	function getSortOrder() {return null;}

	/**
	 * 2018-04-17
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see IAC::getTotalNumOfBoxes()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrierInterface.php#L144-L151
	 * @see AC::getTotalNumOfBoxes():
	 *	public function getTotalNumOfBoxes($weight) {
	 *		$this->_numBoxes = 1;
	 *		$maxPackageWeight = $this->getConfigData('max_package_weight');
	 *		if ($weight > $maxPackageWeight && $maxPackageWeight != 0) {
	 *			$this->_numBoxes = ceil($weight / $maxPackageWeight);
	 *			$weight = $weight / $this->_numBoxes;
	 *		}
	 *		return $weight;
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrier.php#L512-L531
	 * @param float $weight
	 * @return float
	 */
	function getTotalNumOfBoxes($weight) {return $weight;}

	/**
	 * 2018-04-17
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see IAC::isActive()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrierInterface.php#L95-L101
	 * @see AC::isActive():
	 *	public function isActive() {
	 *		$active = $this->getConfigData('active');
	 *		return $active == 1 || $active == 'true';
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrier.php#L338-L348
	 * @return bool
	 */
	function isActive() {
		return true;
	}

	/**
	 * 2018-04-17
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see IAC::isCityRequired()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrierInterface.php#L161-L167
	 * @see AC::isCityRequired():
	 *	public function isCityRequired() {
	 *		return false;
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrier.php#L543-L551
	 * @return bool
	 */
	function isCityRequired() {return false;}

	/**
	 * 2018-04-17
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see IAC::isFixed()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrierInterface.php#L95-L101
	 * @see AC::isFixed():
	 *	public function isFixed() {
	 *		return $this->_isFixed;
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrier.php#L350-L358
	 * @used-by \Magento\Quote\Model\ResourceModel\Quote\Address\Rate\Collection::addItem():
	 *	public function addItem(\Magento\Framework\DataObject $rate) {
	 *		$carrier = $this->_carrierFactory->get($rate->getCarrier());
	 *		if ($this->_allowFixedOnly && (!$carrier || !$carrier->isFixed())) {
	 *			return $this;
	 *		}
	 *		return parent::addItem($rate);
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Quote/Model/ResourceModel/Quote/Address/Rate/Collection.php#L96-L109
	 * @return bool
	 */
	function isFixed() {return true;}

	/**
	 * 2018-04-17
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see IAC::isShippingLabelsAvailable()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrierInterface.php#L119-L125
	 * @see AC::isShippingLabelsAvailable():
	 *	public function isFixed() {
	 *		return false;
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrier.php#L370-L378
	 * @used-by \Magento\Shipping\Block\Adminhtml\Create\Items::canCreateShippingLabel()
	 * @used-by \Magento\Shipping\Block\Adminhtml\View\Form::canCreateShippingLabel()
	 * @used-by \Magento\Shipping\Model\Shipping\LabelGenerator::create()
	 * @return bool
	 */
	function isShippingLabelsAvailable() {return false;}

	/**
	 * 2018-04-17
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see IAC::isStateProvinceRequired()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrierInterface.php#L153-L159
	 * @see AC::isStateProvinceRequired():
	 *	public function isStateProvinceRequired() {
	 *		return false;
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrier.php#L533-L541
	 * @return bool
	 */
	function isStateProvinceRequired() {return false;}

	/**
	 * 2018-04-17
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see IC::isTrackingAvailable()
	 * @used-by \Magento\Shipping\Block\Adminhtml\Order\Tracking::getCarriers():
	 *	foreach ($carrierInstances as $code => $carrier) {
	 *		if ($carrier->isTrackingAvailable()) {
	 *			$carriers[$code] = $carrier->getConfigData('title');
	 *		}
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Block/Adminhtml/Order/Tracking.php#L69-L85
	 * @return bool
	 */
	function isTrackingAvailable() {return false;}

	/**
	 * 2018-04-17
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see IAC::isZipCodeRequired()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrierInterface.php#L169-L176
	 * @see AC::isZipCodeRequired():
	 *	public function isZipCodeRequired() {
	 *		return false;
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrier.php#L553-L563
	 * @param string|null $countryId
	 * @return bool
	 */
	function isZipCodeRequired($countryId = null) {return false;}

	/**
	 * 2018-04-17
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see IAC::proccessAdditionalValidation()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrierInterface.php#L86-L93
	 * @see AC::proccessAdditionalValidation():
	 *	public function proccessAdditionalValidation(\Magento\Framework\DataObject $request) {
	 *		return $this;
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrier.php#L326-L336
	 * @used-by \Magento\Shipping\Model\Shipping::collectCarrierRates()
	 * @param _DO $r
	 * @return $this|false|Error
	 */
	function proccessAdditionalValidation(_DO $r) {return $this;}

	/**
	 * 2018-04-17
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see IAC::requestToShipment()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrierInterface.php#L33-L41
	 * @see AC::requestToShipment():
	 *	public function requestToShipment($request) {
	 *		return new \Magento\Framework\DataObject();
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrier.php#L164-L175
	 * @param _DO $r
	 * @return _DO
	 */
	function requestToShipment($r) {return new _DO;}

	/**
	 * 2018-04-17
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see IAC::returnOfShipment()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrierInterface.php#L42-L51
	 * @see AC::returnOfShipment():
	 *	public function returnOfShipment($request) {
	 *		return new \Magento\Framework\DataObject();
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/Carrier/AbstractCarrier.php#L164-L175
	 * @param _DO $r
	 * @return _DO
	 */
	function returnOfShipment($r) {return new _DO;}
	/**
	 * 2018-04-17
	 * @used-by \Magento\Shipping\Model\Shipping::collectCarrierRates()
	 * @param string $v
	 * @return void
	 */
	final function setActiveFlag($v) {}

	/**
	 * 2018-04-17
	 * @used-by \Magento\Shipping\Model\CarrierFactory::create()
	 * @used-by \Magento\Shipping\Model\CarrierFactory::get()
	 * @param string $carrierCode
	 * @return void
	 */
	final function setId($carrierCode) {}

	/**
	 * 2018-04-17
	 * @used-by \Magento\Shipping\Model\CarrierFactory::create()
	 * @param int $v
	 * @return void
	 */
	final function setStore($v) {$this->_storeId = $v;}

	/**
	 * 2018-04-17
	 * @used-by getCarrierCode()
	 * @uses \Doormall\Shipping\Method::CODE
	 * @see \Df\Payment\Method::codeS()
	 * @return string
	 */
	final static function codeS() {return dfcf(function($c) {return df_const(
		$c, 'CODE', function() use($c) {return df_module_name_lc($c);}
	);}, [static::class]);}

	/**
	 * 2018-04-17
	 * @used-by getConfigData()
	 * @used-by setStore()
	 * @var int|null
	 */
	private $_storeId;
}