<?php
namespace Df\Shipping\Plugin\Model;
use Df\Shipping\Method as M;
use Magento\Shipping\Model\Carrier\AbstractCarrierInterface as IM;
use Magento\Shipping\Model\CarrierFactory as Sb;
// 2018-04-21
final class CarrierFactoryT {
	/**
	 * 2018-04-21
	 * It forces the @see \Df\Shipping\Method descendants to be singletons:
	 * @see \Magento\Shipping\Model\CarrierFactory::create()
	 * https://github.com/magento/magento2/blob/2.2.3/app/code/Magento/Shipping/Model/CarrierFactory.php#L57-L80
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param string $c
	 * @param int|null $sid [optional]
	 * @return M|IM
	 */
	function aroundCreate(Sb $sb, \Closure $f, $c, $sid = null) {
		/** @var $r */
		if (!is_a($c, M::class, true)) {
			$r = $f($c, $sid);
		}
		else {
			$r = M::sg($c);
			$r->setId($c);
			$r->setStore($sid);
		}
		return $r;
	}
}