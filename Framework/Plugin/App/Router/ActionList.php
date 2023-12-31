<?php
namespace Df\Framework\Plugin\App\Router;
use Magento\Framework\App\Router\ActionList as Sb;
# 2017-03-19
# 2023-08-06
# "Prevent interceptors generation for the plugins extended from interceptable classes":
# https://github.com/mage2pro/core/issues/327
# 2023-12-31
# "Declare as `final` the final classes implemented `\Magento\Framework\ObjectManager\NoninterceptableInterface`"
# https://github.com/mage2pro/core/issues/345
final class ActionList extends Sb implements \Magento\Framework\ObjectManager\NoninterceptableInterface {
	/**
	 * 2017-03-19
	 * Позволяет использовать virtualType для контроллеров.
	 * @see \Magento\Framework\App\Router\ActionList::get()
	 * Надо возвращать именно реальный класс, а не виртуальный,
	 * потому что ядро тупо проверяет поддержку классом интерфейа для action:
	 *	if (!$actionClassName || !is_subclass_of($actionClassName, $this->actionInterface)) {
	 *		continue;
	 *	}
	 * @see \Magento\Framework\App\Router\Base::matchAction()
	 * https://github.com/magento/magento2/blob/2.1.5/lib/internal/Magento/Framework/App/Router/Base.php#L296-L298
	 * 2023-01-28
	 * «Argument 4 passed to Df\Framework\Plugin\App\Router\ActionList\Interceptor::aroundGet()
	 * must be of the type string, null given»: https://github.com/mage2pro/core/issues/185
	 * @param string|null $area
	 * @return string|null
	 */
	function aroundGet(Sb $sb, \Closure $f, string $m, $area, string $ns, string $action) {return
		$f($m, $area, $ns, $action) ?: (
			'Df' !== substr($m, 0, 2) || !df_is_virtual(
				$c = df_cc_class_uc(df_module_name_c($m), 'Controller', 'adminhtml' === $area ? $area : null, $ns, $action)
			)
			? null : df_vtr($c)
		)
	;}
}