<?php
namespace Df\Framework\Plugin\App\Request;
use Df\Framework\Action as DfA;
use Magento\Framework\App\ActionInterface as IA;
use Magento\Framework\App\Request\CsrfValidator as Sb;
use Magento\Framework\App\Request\Http as R;
use Magento\Framework\App\RequestInterface as IR;
# 2020-02-25
/**
 * 2018-12-17
 * It is needed for Yandex.Checkout: it sends a callback via POST,
 * but Magento 2.3 enforces a CSRF checking for such requests:
 * @see \Magento\Framework\App\Request\CsrfValidator::validateRequest()
 * A similar issue with Adyen: https://github.com/Adyen/adyen-magento2/issues/327
 * 2020-02-25
 * 1) Now I need it for Sift too.
 * "Implement decision webhooks": https://github.com/mage2pro/sift/issues/12
 * 2) My previous solution was a plugin for `Magento\Framework\Data\Form\FormKey\Validator`:
 *	<type name='Magento\Framework\Data\Form\FormKey\Validator'>
 *		<plugin
 *			name='Df\Framework\Plugin\Data\Form\FormKey\Validator'
 *			type='Df\Framework\Plugin\Data\Form\FormKey\Validator'
 *		/>
 *	</type>
 * https://github.com/mage2pro/core/blob/6.2.7/Framework/etc/di.xml#L82-L92
 * 	namespace Df\Framework\Plugin\Data\Form\FormKey;
 * 	use Magento\Framework\App\RequestInterface as IR;
 * 	use Magento\Framework\App\Request\Http as R;
 * 	use Magento\Framework\Data\Form\FormKey\Validator as Sb;
 * 	final class Validator {
 * 		function aroundValidate(Sb $sb, \Closure $f, IR $r) {return
 * 			df_starts_with($r->getModuleName(), ['dfe-', 'df-']) || $f($r)
 * 		;}
 * 	}
 * https://github.com/mage2pro/core/blob/6.2.7/Framework/Plugin/Data/Form/FormKey/Validator.php#L9-L25
 * It worked only with routes started with `df-` or `dfe-`.
 * The Sift's route does not have such prefix (it starts with `sift`), that is why I implemented another solution.
 */
final class CsrfValidator {
	/**
	 * 2020-02-25
	 * @see \Magento\Framework\App\Request\CsrfValidator::validate():
	 *		try {
	 *			$areaCode = $this->appState->getAreaCode();
	 *		}
	 * 		catch (LocalizedException $exception) {
	 *			$areaCode = null;
	 *		}
	 *		if ($request instanceof HttpRequest && in_array($areaCode, [Area::AREA_FRONTEND, Area::AREA_ADMINHTML], true)) {
	 *			$valid = $this->validateRequest($request, $action);
	 *			if (!$valid) {
	 *				throw $this->createException($request, $action);
	 *			}
	 *		}
	 * https://github.com/magento/magento2/blob/2.3.4/lib/internal/Magento/Framework/App/Request/CsrfValidator.php#L111-L135
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param IR|R $r
	 * @param IA $a
	 */
	function aroundValidate(Sb $sb, \Closure $f, IR $r, IA $a):bool {return $a instanceof DfA || $f($r, $a);}
}