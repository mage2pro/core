<?php
use Df\Security\BlackList as B;
/**
 * 2021-09-14
 * "Implement an ability to temporary ban visitors with a particular IP address": https://github.com/mage2pro/core/issues/159
 * @used-by CanadaSatellite\Bambora\Action::check()  (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/14)
 * @used-by CanadaSatellite\Core\Plugin\Magento\Customer\Api\AccountManagementInterface::aroundIsEmailAvailable() (canadasatellite.ca, https://github.com/canadasatellite-ca/core/issues/1)
 * @used-by CanadaSatellite\Core\Plugin\Magento\Framework\App\Http::aroundLaunch() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/72)
 */
function df_ban(string $ip = ''):void {
	if (!df_is_backend()) {
		B::add($ip);
		df_403();
	}
}