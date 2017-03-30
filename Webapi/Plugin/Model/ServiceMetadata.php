<?php
// 2016-10-06
namespace Df\Webapi\Plugin\Model;
use Magento\Webapi\Model\ServiceMetadata as Sb;
class ServiceMetadata extends Sb {
	/** @override */
	function __construct() {}

	/**
	 * 2016-10-06
	 * When browsing the default soap url that should return xml soap services,
	 * instead there is an exception with the following:
	 * «The service interface name "Df\Payment\PlaceOrder" is invalid»
	 * https://code.dmitry-fedyuk.com/m2e/stripe/issues/7
	 *
	 * Magento 2 накладывает ограничения на имена классов-вебсервисов:
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
	 *
	 * @see \Magento\Webapi\Model\ServiceMetadata::getServiceName()
	 * https://github.com/magento/magento2/blob/2.1.1/app/code/Magento/Webapi/Model/ServiceMetadata.php#L188-L230
	 *
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param string $interface
	 * @param string $version
	 * @param bool $preserveVersion Should version be preserved during interface name conversion into service name
	 * @return string
	 */
	function aroundGetServiceName(Sb $sb, \Closure $f, $interface, $version, $preserveVersion = true) {return
		df_starts_with($interface, 'Df\\')
		// 2016-10-06
		// Df\Payment\PlaceOrder => dfPaymentPlaceOrder
		? lcfirst(implode(df_explode_class($interface))) . (!$preserveVersion ? '' : $version)
		: $f($interface, $version, $preserveVersion)
	;}
}

