<?php
namespace Df\Sales\Plugin\Model\Order\Address;
use Df\Payment\Method;
use Magento\Customer\Block\Address\Renderer\DefaultRenderer;
use Magento\Customer\Block\Address\Renderer\RendererInterface;
use Magento\Customer\Model\Address\Config as AddressConfig;
use Magento\Framework\DataObject;
use Magento\Payment\Model\MethodInterface;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Address\Renderer as Sb;
use Magento\Sales\Model\Order\Payment as OP;
// 2016-08-17
class Renderer extends Sb {
	/** 2016-04-05 */
	function __construct() {}

	/**
	 * 2016-08-17
	 * Цель плагина — форматирование внешнего вида платёжного адреса в том случае,
	 * когда отключенен запрос этого адреса у покупателей, и данные, вероятно, пусты.
	 *
	 * Раньше я пробовал реализовать эту функциональность обработкой события «customer_address_format»:
	 * https://github.com/mage2pro/core/blob/1.6.16/Customer/Observer/AddressFormat.php?ts=4#L37-L92
	 * Однако такой подход оказался невозможен,
	 * потому что метод @see \Magento\Sales\Model\Order\Address\Renderer::format() выглядит так:
	 *
	 *	$formatType = $this->addressConfig->getFormatByCode($type);
	 *	if (!$formatType || !$formatType->getRenderer()) {
	 *		return null;
	 *	}
	 *	$this->eventManager->dispatch('customer_address_format', [
	 *		'type' => $formatType, 'address' => $address
	 * 	]);
	 *	return $formatType->getRenderer()->renderArray($address->getData());
	 *
	 * Во-первых, надо обратить внимание, что $formatType — это одиночка.
	 * 1) Сначала я наивно пытался её модифицировать, но тогда, раз это одиночка,
	 * то мои изменения применялись ко всем последующим адресам,
	 * в том числе к непустым адресам и адресам доставки.
	 * 2) Второй попыткой было в обработчике события подменять одиночку $formatType на свой объект.
	 * Но ведь метод @see \Magento\Sales\Model\Order\Address\Renderer::format() игнорирует
	 * результат обработчика события, и продолжает использовать одиночку,
	 * так что в таком подходе толку нет.
	 *
	 * Поэтому пришлось делать этот плагин.
	 *
	 * @see \Magento\Sales\Model\Order\Address\Renderer::format()
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param Address $a
	 * @param string $type
	 * @return string
	 */
	function aroundFormat(Sb $sb, \Closure $f, Address $a, $type) {
		/** @var string $result */
		// 2016-08-17
		// Убеждаемся, что firstname и lastname равны null,
		// чтобы не ломать отображение адресов, для которых информация присутствует
		// (например, эти адреса могли быть собраны до отключения опции requireBillingAddress).
		if (df_address_is_billing($a) && !$a->getFirstname() && !$a->getLastname() 
			&& dfp_my($a->getOrder()) 
		) {
			/**
			 * 2016-08-17 
			 * Замечание №1.
			 * Раньше тут было ещё условие !$method->s()->requireBillingAddress(),
			 * но на самом деле оно ошибочно,
			 * потому что если администратор сначала отключил опцию requireBillingAddress,
			 * собраз заказы, а потом снова включил эту опцию,
			 * то адреса заказов, собранных во время отключения опции,
			 * должны обрабатываться корректно.
			 * 
			 * Замечание №2.
			 * Дальнейший код идёт по аналалогии с кодом
			 * @see \Magento\Sales\Model\Order\Address\Renderer::format()
			 * 
			 * 2016-07-27
			 * By analogy with @see \Magento\Sales\Model\Order\Address\Renderer::format()
			 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Model/Order/Address/Renderer.php#L51
			 * @var DataObject $typeO
			 */
			$typeO = $this->addressConfig()->getFormatByCode($type);
			// 2016-07-27
			// Если в будущем мы захотим написать что-либо более объёмное,
			// то можно поставить ещё 'escape_html' => false
			$typeO->addData(['default_format' => __(
				!df_is_backend() ? 'Not used.' : 'The customer was not asked for it.'
			)]);
			/** @var RendererInterface|DefaultRenderer|null $renderer */
			/** @noinspection PhpUndefinedCallbackInspection */
			if (!($renderer = call_user_func([$typeO, 'getRenderer']))) {
				$result = null;
			}
			else {
				df_dispatch('customer_address_format', ['type' => $typeO, 'address' => $a]);
				$result = $renderer->renderArray($a->getData());
			}
		}
		return isset($result) ? $result : $f($a, $type);
	}

	/**
	 * 2016-07-27
	 * Обсерверы по умолчанию являются одиночками: https://github.com/magento/magento2/blob/1.0.0-beta/lib/internal/Magento/Framework/Event/Invoker/InvokerDefault.php#L56-L60
	 * Мы используем свою одиночку, а не общую с ядром,
	 * потому что наша одиночка хранит значения по-умолчанию,
	 * а одиночка ядра загрязняется нашим хаком из метода
	 * @used-by \Df\Sales\Plugin\Model\Order\Address\Renderer::aroundFormat()
	 * Таким образом, мы используем нашу одиночку для того, чтобы очистить одиночку ядра.
	 * @return AddressConfig
	 */
	private function addressConfig() {return dfc($this, function() {return
		df_new_om(AddressConfig::class)
	;});}
}