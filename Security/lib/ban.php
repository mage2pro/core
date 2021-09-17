<?php
use Df\Security\BlackList as B;
/**
 * 2021-09-14
 * "Implement an ability to temporary ban visitors with a particular IP address": https://github.com/mage2pro/core/issues/159
 * @used-by \Df\Framework\Plugin\App\Http::aroundLaunch()
 * @param string|null $ip [optional]
 */
function df_ban($ip = null) {
	if (!df_is_backend()) {
		B::add($ip);
		df_403();
	}
}