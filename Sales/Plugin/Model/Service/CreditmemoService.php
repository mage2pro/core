<?php
namespace Df\Sales\Plugin\Model\Service;
use Magento\Sales\Api\Data\CreditmemoInterface as ICreditmemo;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Service\CreditmemoService as Sb;
# 2016-08-20
final class CreditmemoService {
	/**
	 * 2016-08-20
	 * Цель плагина — установка флага DoTransaction.
	 * Без этого флага возврат работать не будет:
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/Order/Payment.php#L633
	 * @see \Magento\Sales\Model\Order\Payment::refund()
	 * Там стоит код:
	 * if ($gateway->canRefund() && $creditmemo->getDoTransaction()) {
	 *
	 * Флаг DoTransaction устанавливался в прежних версиях Magento, в том числе в Magento 2.1:
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/Service/CreditmemoService.php#L150
	 * Однако в 2.2-dev он почему-то не устанавливается (видимо, баг):
	 * https://github.com/magento/magento2/blob/522fad/app/code/Magento/Sales/Model/Service/CreditmemoService.php#L156-L189
	 *
	 * @see \Magento\Sales\Model\Service\CreditmemoService::refund()
	 */
	function beforeRefund(Sb $sb, ICreditmemo $c, bool $offlineRequested = false):void {
		if ($c instanceof Creditmemo) {
			/** @noinspection PhpUndefinedMethodInspection */
			$c->setDoTransaction(!$offlineRequested);
		}
	}
}