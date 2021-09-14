<?php
/**
 * 2021-09-14
 * "Implement an ability to temporary ban visitors with a particular IP address": https://github.com/mage2pro/core/issues/159
 * @param string|null $ip [optional]
 */
function df_ban($ip = null) {
	$ip = $ip ?: df_visitor_ip();
	#@TODO
}