<?php
namespace Df\Framework\Log\Handler;
# 2021-09-08
final class PayPal extends \Df\Framework\Log\Handler {
	/**
	 * 2021-09-08
	 * @override
	 * @see \Df\Framework\Log\Handler::_p()
	 * @used-by \Df\Framework\Log\Handler::p()
	 */
	protected function _p():bool {return $this->r()->msg([
		/**
		 * 2020-06-24
		 * "Prevent logging the PayPal 10486 error «Exception message: We can't initialize Express Checkout.»
		 * to `system.log`": https://github.com/mage2pro/core/issues/101
		 * @see \Magento\Framework\Message\Manager::addExceptionMessage():
		 *		$message = sprintf(
		 *			'Exception message: %s%sTrace: %s',
		 *			$exception->getMessage(),
		 *			"\n",
		 *			Debug::trace(
		 *				$exception->getTrace(),
		 *				true,
		 *				true,
		 *				(bool)getenv('MAGE_DEBUG_SHOW_ARGS')
		 *			)
		 *		);
		 *		$this->logger->critical($message);
		 * https://github.com/magento/magento2/blob/2.3.5-p1/lib/internal/Magento/Framework/Message/Manager.php#L293-L305
		 */
		"Exception message: We can't initialize Express Checkout."
		/**
		 * 2020-06-24
		 * "Prevent logging the PayPal 10486 error «Please redirect your customer to PayPal» to `system.log`":
		 * https://github.com/mage2pro/core/issues/100
		 * @see \Magento\Paypal\Model\Api\Nvp::_handleCallErrors():
		 *		$exceptionLogMessage = sprintf(
		 *			'PayPal NVP gateway errors: %s Correlation ID: %s. Version: %s.',
		 *			$errorMessages,
		 *			isset($response['CORRELATIONID']) ? $response['CORRELATIONID'] : '',
		 *			isset($response['VERSION']) ? $response['VERSION'] : ''
		 *		);
		 *		$this->_logger->critical($exceptionLogMessage);
		 * https://github.com/magento/magento2/blob/2.3.5-p1/app/code/Magento/Paypal/Model/Api/Nvp.php#L1281-L1287
		 */
		,"PayPal NVP gateway errors: This transaction couldn't be completed. Please redirect your customer to PayPal (#10486: This transaction couldn't be completed)"
	]);}
}