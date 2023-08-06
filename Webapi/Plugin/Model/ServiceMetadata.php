<?php
namespace Df\Webapi\Plugin\Model;
use Magento\Webapi\Model\ServiceMetadata as Sb;
# 2016-10-06
# 2023-08-06
# "Prevent interceptors generation for the plugins extended from interceptable classes":
# https://github.com/mage2pro/core/issues/327
class ServiceMetadata extends Sb implements \Magento\Framework\ObjectManager\NoninterceptableInterface {
	/** @override */
	function __construct() {}

	/**
	 * 2016-10-06
	 * 1) When browsing the default soap url that should return xml soap services,
	 * instead there is an exception with the following:
	 * «The service interface name "Df\Payment\PlaceOrder" is invalid»
	 * https://code.dmitry-fedyuk.com/m2e/stripe/issues/7
	 * 2) Magento 2 накладывает ограничения на имена классов-вебсервисов:
	 * https://github.com/magento/magento2/blob/2.1.1/app/code/Magento/Webapi/Model/ServiceMetadata.php#L188-L230
	 * Однако, как я понял, моего веб-сервиса @see \Df\Payment\PlaceOrder эти ограничения касаются
	 * только в сценарии генерации документа WSDL /soap/default?wsdl_list=1
	 * Мой веб-сервис предназначен исключительно для моих платёжных модулей, и,
	 * будь моя воля, я бы вообще не включал его в документ WSDL.
	 * Однако, как я понял, избежать включения веб-сервиса в документ WSDL не так-то просто.
	 * Но и менять моё короткое имя Df\Payment\PlaceOrder на имя типа Df\Payment\API\PlaceOrderInterface
	 * мне не хочется: это имя используется каждым моим платёжным модулем,
	 * и мне удобнее иметь для себя свои имена.
	 * Поэтому я и написал этот плагин: чтобы возвращать ядру имя своего сервиса
	 * (и других моих сервисов, если они потом будут), обходя ограничения ядра на имена классов сервисов.
	 * @see \Magento\Webapi\Model\ServiceMetadata::getServiceName()
	 * https://github.com/magento/magento2/blob/2.1.1/app/code/Magento/Webapi/Model/ServiceMetadata.php#L188-L230
	 */
	function aroundGetServiceName(Sb $sb, \Closure $f, string $i, string $v, bool $preserveV = true):string {return
		df_starts_with($i, 'Df\\')
		# 2016-10-06 Df\Payment\PlaceOrder => dfPaymentPlaceOrder
		? lcfirst(implode(df_explode_class($i))) . (!$preserveV ? '' : $v)
		: $f($i, $v, $preserveV)
	;}
}

