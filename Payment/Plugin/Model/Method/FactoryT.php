<?php
namespace Df\Payment\Plugin\Model\Method;
use Df\Payment\Method as M;
use Magento\Payment\Model\Method\Factory as Sb;
// 2017-03-30
final class FactoryT {
	/**
	 * 2017-03-30
	 * Цель перекрытия — сделать потомков класса @see M одиночками:
	 * @see \Magento\Payment\Model\Method\Factory::create()
	 * https://github.com/magento/magento2/blob/2.1.5/app/code/Magento/Payment/Model/Method/Factory.php#L30-L47
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param string $c
	 * @param array $d [optional]
	 * @return \Magento\Framework\Option\ArrayInterface|mixed
	 */
	function aroundCreate(Sb $sb, \Closure $f, $c, $d = []) {return
		is_a($c, M::class, true) ? M::singleton($c) : $f($c, $d)
	;}
}