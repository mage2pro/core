<?php
namespace Df\Payment\Plugin\Model\Checks;
use Magento\Payment\Model\Checks\CanUseForCountry as Sb;
use Magento\Payment\Model\MethodInterface as IM;
use Magento\Quote\Model\Quote as Q;
// 2017-12-13
final class CanUseForCountry {
	/**
	 * 2017-12-13
	 * "Improve @see \Magento\Payment\Model\Checks\CanUseForCountry:
	 * it should give priority to the shipping country over the billing country for my modules":
	 * https://github.com/mage2pro/core/issues/62
	 * @see \Magento\Payment\Model\Checks\CanUseForCountry::isApplicable()
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Payment/Model/Checks/CanUseForCountry.php#L27-L36
	 * https://github.com/magento/magento2/blob/2.2.2/app/code/Magento/Payment/Model/Checks/CanUseForCountry.php#L33-L42
	 * @used-by \Df\Payment\Settings::applicableForQuoteByCountry()
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param IM $m
	 * @param Q $q
	 * @return string
	 */
	function aroundIsApplicable(Sb $sb, \Closure $f, IM $m, Q $q) {return !dfp_my($m) ? $f($m, $q) : (
		$m->canUseForCountry(df_oq_country_sb($q))
	);}
}