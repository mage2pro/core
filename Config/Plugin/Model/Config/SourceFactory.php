<?php
namespace Df\Config\Plugin\Model\Config;
use Magento\Config\Model\Config\SourceFactory as Sb;
# 2022-11-21
# It exists since Magento 2.0.0:
# https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Data/OptionSourceInterface.php
use Magento\Framework\Data\OptionSourceInterface as IOptionSource;
# 2015-11-14
final class SourceFactory {
	/**
	 * 2015-11-14
	 * Magento treats `<source_model>` classes as singletons: @see \Magento\Config\Model\Config\SourceFactory::create()
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Model/Config/SourceFactory.php#L33
	 * The puprose of my plugin to create independent instances of my `<source_model>` classes
	 * for each `<source_model>` occurence.
	 * 2016-01-01 We got there during the `<source_model>` tag handling by the Magento core.
	 * 2023-07-21
	 * 1) «Return value of Df\Config\Plugin\Model\Config\SourceFactory::aroundCreate()
	 * must implement interface Magento\Framework\Data\OptionSourceInterface,
	 * instance of Df\Paypal\Model\Config\Interceptor returned»: https://github.com/mage2pro/core/issues/244
	 * 2) `<source_model>` is not required to implement @see \Magento\Framework\Data\OptionSourceInterface
	 * 2.1) E.g., `Magento_Paypal` uses the syntax:
	 * 		<source_model>Magento\Paypal\Model\Config::getApiAuthenticationMethods</source_model>
	 * https://github.com/magento/magento2/blob/2.3.7-p4/app/code/Magento/Paypal/etc/adminhtml/system/express_checkout.xml#L39-L39
	 * 2.2) This syntax is handled by @see \Magento\Config\Model\Config\Structure\Element\Field::_getOptionsFromSourceModel()
	 * https://github.com/magento/magento2/blob/2.3.7-p4/app/code/Magento/Config/Model/Config/Structure/Element/Field.php#L447-L460
	 * 2.3) With the syntax, `<source_model>` can be any class.
	 * 2.4) `object` as a return type is not supported by PHP < 7.2: https://3v4l.org/dAmcs
	 * @see \Magento\Config\Model\Config\SourceFactory::create()
	 * 2024-06-03
	 * 1) The `object` type requires PHP ≥ 7.2: https://github.com/mage2pro/core/issues/174#user-content-object
	 * 2) We need to support PHP ≥ 7.1: https://github.com/mage2pro/core/issues/368
	 * @return object
	 */
	function aroundCreate(Sb $sb, \Closure $f, string $c) {return df_class_my($c) ? new $c : $f($c);}
}