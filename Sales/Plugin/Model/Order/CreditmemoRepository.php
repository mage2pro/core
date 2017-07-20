<?php
namespace Df\Sales\Plugin\Model\Order;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Sales\Model\Order\CreditmemoRepository as Sb;
use Magento\Sales\Api\Data\CreditmemoInterface as CM;
// 2016-03-18
final class CreditmemoRepository {
	/**
	 * 2016-03-18
	 * Цель плагина — устранения дефекта:
	 * «Bug: the @see \Magento\Sales\Model\Order\CreditmemoRepository::save() method
	 * misses (does not log and does not show) the actual exception message
	 * on a credit memo saving falure».
	 * https://mage2.pro/t/973
	 *
	 * @see \Magento\Sales\Model\Order\CreditmemoRepository::save()
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param CM $element
	 * @return CM
	 * @throws CouldNotSaveException|LE;
	 */
	function aroundSave(Sb $sb, \Closure $f, CM $element) {
		/** @var CM $result */
		try {
			$result = $f($element);
		}
		catch(CouldNotSaveException $e) {
			/** @var \Exception|null $previous */
			$previous = $e->getPrevious();
			throw $previous instanceof LE ? $previous : $e;
		}
		return $result;
	}
}