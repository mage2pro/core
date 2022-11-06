<?php
namespace Df\Framework\Plugin\EntityManager;
use Magento\Framework\EntityManager\TypeResolver as Sb;
# 2021-03-26
# 1) «Could not save child: "Unknown entity type: Magento\Bundle\Model\Selection\Interceptor requested"»:
# https://github.com/mage2pro/ipay88/issues/17
# 2) "«Could not save child: "Unknown entity type: Magento\Bundle\Model\Selection\Interceptor requested"»
# on bin/magento product:bundle:resave": https://github.com/canadasatellite-ca/site/issues/44
# 3) "Magento 2.2.0 - fails to add bundle product": https://stackoverflow.com/questions/47588206
# 4) "Unknown entity type: Magento\Bundle\Model\Selection\Interceptor requested": https://magento.stackexchange.com/questions/260419
final class TypeResolver {
	/**
	 * 2021-03-26
	 * @see \Magento\Framework\EntityManager\TypeResolver::resolve()
	 * @param Sb $sb
	 * @param string $r
	 */
	function afterResolve(Sb $sb, $r):string {return df_trim_interceptor($r);}
}