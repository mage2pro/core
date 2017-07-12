<?php
namespace Df\Framework\Plugin\App\Router;
use Magento\Framework\App\Router\ActionList as Sb;
// 2017-03-19
class ActionList extends Sb {
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
	 * @param Sb $sb
	 * @param \Closure $f
     * @param string $m
     * @param string $area
     * @param string $ns
     * @param string $action
	 * @return string|null
	 */
	function aroundGet(Sb $sb, \Closure $f, $m, $area, $ns, $action) {return
		$f($m, $area, $ns, $action) ?: (
			'Df' !== substr($m, 0, 2) 
			|| !df_is_virtual($c = df_cc_class_uc(df_module_name_c($m), 'Controller', 'adminhtml' === $area ? $area : null, $ns, $action))
			? null : df_vtr($c)			
		)
	;}
}