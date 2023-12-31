<?php
namespace Df\Paypal\Plugin\Model\Payflow\Service\Response\Validator;
use Df\Paypal\Plugin\Model\Payflow\Service\Gateway as G;
use Magento\Framework\DataObject as _DO;
use Magento\Paypal\Model\Payflow\Service\Response\Validator\ResponseValidator as Sb;
use Magento\Paypal\Model\Payflow\Transparent as T;
use Magento\Paypal\Model\Payflowpro as P;
# 2023-12-31
final class ResponseValidator extends Sb implements \Magento\Framework\ObjectManager\NoninterceptableInterface {
	/**
	 * 2023-12-31
	 * 1) "Log diagnostic data for «The payment couldn't be processed at this time» error of PayPal Payflow":
	 * https://github.com/mage2pro/core/issues/344
	 * 2)  @see \Magento\Paypal\Model\Payflow\Service\Response\Validator\ResponseValidator::validate():
	 * https://github.com/magento/magento2/blob/2.4.4/app/code/Magento/Paypal/Model/Payflow/Service/Response/Validator/ResponseValidator.php#L35-L61
	 * 3.1) @see \Magento\Paypal\Gateway\Payflowpro\Command\AuthorizationCommand::execute():
	 *		try {
	 *			$this->payflowFacade->getResponceValidator()->validate($response, $this->payflowFacade);
	 *		}
	 * 		catch (LocalizedException $exception) {
	 *			$payment->setParentTransactionId($response->getData(Transparent::PNREF));
	 *			$this->payflowFacade->void($payment);
	 *			throw new LocalizedException(__("The payment couldn't be processed at this time. Please try again later."));
	 *		}
	 * https://github.com/magento/magento2/blob/2.4.4/app/code/Magento/Paypal/Gateway/Payflowpro/Command/AuthorizationCommand.php#L72-L78
	 * 3.2) @see \Magento\Paypal\Model\Payflow\Transparent::authorize():
	 * 		$response = $this->postRequest($request, $this->getConfig());
	 * 		$this->processErrors($response);
	 *		try {
	 * 			$this->responseValidator->validate($response, $this);
	 * 		}
	 * 		catch (ValidatorException $exception) {
	 * 			$payment->setParentTransactionId($response->getData(self::PNREF));
	 * 			$this->void($payment);
	 * 			throw new ValidatorException(__("The payment couldn't be processed at this time. Please try again later."));
	 *		}
	 * https://github.com/magento/magento2/blob/2.4.4/app/code/Magento/Paypal/Model/Payflow/Transparent.php#L220-L229
 	 */
	function beforeValidate(Sb $sb, _DO $r, T $t):void {
		switch ($r->getResult()) {
			case P::RESPONSE_CODE_APPROVED:
			case P::RESPONSE_CODE_FRAUDSERVICE_FILTER:
				foreach ($sb->validators as $validator) {
					if (false === $validator->validate($r, $t)) {
						self::log($r);
					}
				}
				break;
			case P::RESPONSE_CODE_INVALID_AMOUNT:
				break;
			default:
				self::log($r);
		}
	}

	/**
	 * 2023-12-31
	 * @used-by self::beforeValidate()
	 */
	private static function log( _DO $r):void {df_log(
		'PayPal has declined the transaction', Sb::class, ['request' => G::req(), 'response' => $r], 'declined'
	);}
}