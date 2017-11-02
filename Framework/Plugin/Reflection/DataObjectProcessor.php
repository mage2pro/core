<?php
namespace Df\Framework\Plugin\Reflection;
use Df\Customer\Setup\UpgradeSchema as Schema;
use Magento\Customer\Model\Data\Customer as DC;
use Magento\Framework\Reflection\DataObjectProcessor as Sb;
// 2017-05-22
class DataObjectProcessor {
	/**
	 * 2017-05-22
	 * @see \Magento\Framework\Reflection\DataObjectProcessor::buildOutputDataArray()
	 * @param Sb $sb
	 * @param \Closure $f
     * @param object|DC $object
     * @param string $type
	 * @return array(string => mixed)
	 */
	function aroundBuildOutputDataArray(Sb $sb, \Closure $f, $object, $type) {
		$result = $f($object, $type); /** @var array(string => mixed) $result */
		if ($object instanceof DC) {
			$result += df_clean([Schema::F__DF => df_api_object_get($object, Schema::F__DF)]);
		}
		return $result;
	}
}


