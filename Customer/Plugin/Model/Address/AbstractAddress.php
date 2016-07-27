<?php
namespace Df\Customer\Plugin\Model\Address;
use Df\Customer\Settings\BillingAddress as S;
use Magento\Customer\Model\Address\AbstractAddress as Sb;
use Magento\Framework\Phrase;
// 2016-07-27
class AbstractAddress {
	/**
	 * 2016-07-27
	 * Цель плагина — добавление возможности отключения необходимости платёжного адреса.
	 * Это будет использоваться моими платёжными модулями.
	 * Помимо этого плагина для данной функциональности нужны ещё 2:
	 * @see \Df\Customer\Plugin\Model\ResourceModel\AddressRepository
	 * @see \Df\Sales\Plugin\Model\Order\Address\Validator
	 *
	 * @see \Magento\Customer\Model\Address\AbstractAddress::validate()
	 * @param Sb $sb
	 * @param \Closure $proceed
	 * @return bool|string[]
	 */
	public function aroundValidate(Sb $sb, \Closure $proceed) {
		return S::disabled() && df_address_is_billing($sb) ? true : $proceed();
	}
}