<?php
namespace Df\Framework\Plugin\Data\Form\FormKey;
use Magento\Framework\App\RequestInterface as IR;
use Magento\Framework\App\Request\Http as R;
// 2018-12-17
// https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/Form/FormKey/Validator.php
use Magento\Framework\Data\Form\FormKey\Validator as Sb;
// 2018-12-17
final class Validator {
	/**
	 * 2018-12-17
	 * It is needed for Yandex.Checkout: it sends a callback via POST,
	 * but Magento 2.3 enforces a CSRF checking for such requests:
	 * @see \Magento\Framework\App\Request\CsrfValidator::validateRequest()
	 * A similar issue with Adyen: https://github.com/Adyen/adyen-magento2/issues/327
	 * @see \Magento\Framework\Data\Form\FormKey\Validator::validate()
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param IR $r
	 * @return bool
	 */
	function aroundValidate(Sb $sb, \Closure $f, IR $r) {return
		df_starts_with($r->getModuleName(), ['dfe-', 'df-']) || $f($r)
	;}
}