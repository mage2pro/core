<?php
namespace Df\Store\Model;
use Magento\Store\Model\Store;
class StorePlugin {
	/**
	 * 2015-10-01
	 * «Admin» store title is untranslatable on admin attribute edit page
	 * neither by a dictionary nor manually.
	 * https://mage2.pro/t/99
	 * @see AbstractFrontend::getName()
	 * @param Store $subject
	 * @param string $result
	 * @return string
	 */
	public function afterGetName(Store $subject, $result) {
		return 'Admin' === $result ? __($result) : $result;
	}
}