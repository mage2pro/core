<?php
namespace Df\Sales\Plugin\Model\Order;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Sales\Api\Data\CreditmemoInterface as CM;
use Magento\Sales\Model\Order\CreditmemoRepository as Sb;
# 2016-03-18
final class CreditmemoRepository {
	/**
	 * 2016-03-18
	 * Цель плагина — устранения дефекта:
	 * «Bug: the @see \Magento\Sales\Model\Order\CreditmemoRepository::save() method
	 * misses (does not log and does not show) the actual exception message on a credit memo saving falure».
	 * https://mage2.pro/t/973
	 * @see \Magento\Sales\Model\Order\CreditmemoRepository::save()
	 * @throws CouldNotSaveException|LE;
	 */
	function aroundSave(Sb $sb, \Closure $f, CM $element):CM {/** @var CM $r */
		try {$r = $f($element);}
		catch(CouldNotSaveException $e) {throw ($p = $e->getPrevious()) instanceof LE ? $p : $e; /** @var \Exception|null $p */}
		return $r;
	}
}