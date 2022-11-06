<?php
namespace Df\Framework\Log\Handler;
use Exception as E;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException as NSE;
/**
 * 2020-02-21, 2021-09-08
 * 1) "\Magento\Checkout\Model\Session::getQuote() should not log
 * the «No such entity with customerId = ...» exception because it occurs in an expected normal case":
 * https://github.com/tradefurniturecompany/site/issues/17
 * 2) @see \Magento\Framework\Exception\NoSuchEntityException::singleField():
 *		public static function singleField($fieldName, $fieldValue) {
 *			return new self(
 *				new Phrase('No such entity with %fieldName = %fieldValue', [
 *					'fieldName' => $fieldName,
 *					'fieldValue' => $fieldValue
 *				])
 *			);
 *		}
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Exception/NoSuchEntityException.php#L29-L47
 * https://github.com/magento/magento2/blob/2.3.4/lib/internal/Magento/Framework/Exception/NoSuchEntityException.php#L41-L59
 * 3) The problem is fixed in Magento 2.3.4 by the commit: https://github.com/magento/magento2/commit/5f3b86ab
 * Magento 2.3.4 does not log the exception @see \Magento\Checkout\Model\Session::getQuote():
 * https://github.com/magento/magento2/blob/2.3.4/app/code/Magento/Checkout/Model/Session.php#L280-L282
 * 4) @see \Magento\Checkout\Model\Session::getQuote() in Magento < 2.3.4 logs the
 * @see \Magento\Framework\Exception\NoSuchEntityException exception:
 * https://github.com/magento/magento2/blob/2.3.3/app/code/Magento/Checkout/Model/Session.php#L276-L278
 */
final class NoSuchEntity extends \Df\Framework\Log\Handler {
	/**
	 * 2021-09-08
	 * @override
	 * @see \Df\Framework\Log\Handler::_p()
	 * @used-by \Df\Framework\Log\Handler::p()
	 */
	protected function _p():bool {return ($e = $this->r()->e()) instanceof NSE && df_bt_has(Session::class, 'getQuote', $e);}
}