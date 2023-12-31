<?php
namespace Df\Paypal\Plugin\Model\Payflow\Service;
use Magento\Framework\DataObject as _DO;
use Magento\Paypal\Model\Payflow\Service\Gateway as Sb;
# 2023-12-31
final class Gateway {
	/**
	 * 2023-12-31
	 * "Log diagnostic data for «The payment couldn't be processed at this time» error of PayPal Payflow":
	 * https://github.com/mage2pro/core/issues/344
 	 */
	function beforePostRequest(Sb $sb, _DO $r):void {self::req($r);}

	/**
	 * 2023-12-31
	 * @used-by self::beforePostRequest()
	 * @used-by \Df\Paypal\Plugin\Model\Payflow\Service\Response\Validator\ResponseValidator::log()
	 * @param _DO|null|string $v [optional]
	 * @return self|_DO
	 */
	static function req($v = DF_N) {return df_prop(null, $v);}
}