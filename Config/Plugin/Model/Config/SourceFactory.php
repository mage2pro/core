<?php
namespace Df\Config\Plugin\Model\Config;
use Magento\Config\Model\Config\SourceFactory as Sb;
use Magento\Framework\Option\ArrayInterface as IOptionsArray;
# 2015-11-14
final class SourceFactory {
	/**
	 * 2015-11-14
	 * Magento treats `<source_model>` classes as singletons: @see \Magento\Config\Model\Config\SourceFactory::create()
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Model/Config/SourceFactory.php#L33
	 * The puprose of my plugin to create independent instances of my `<source_model>` classes
	 * for each `<source_model>` occurence.
	 * 2016-01-01 We got there during the `<source_model>` tag handling by the Magento core.
	 * @see \Magento\Config\Model\Config\SourceFactory::create()
	 */
	function aroundCreate(Sb $sb, \Closure $f, string $c):IOptionsArray {return df_class_my($c) ? new $c : $f($c);}
}