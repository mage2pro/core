<?php
use Magento\Customer\Model\Customer as C;
use Magento\Newsletter\Model\Subscriber as S;
use Magento\Store\Model\Store;
use Magento\Store\Model\Website as W;

/**
 * 2024-06-02
 * 1) "Implement `df_subscriber()`": https://github.com/thehcginstitute-com/m1/issues/627
 * 2) https://3v4l.org/tIHdP
 * 3) "Port `df_subscriber()` from `thehcginstitute-com/m1`": https://github.com/mage2pro/core/issues/399
 * 4) @deprecated It is unused.
 * @param string|C|null $id [optional]
 * @param W|Store|int|string|null|bool $w [optional]
 */
function df_subscriber($id = null, $w = null):S {
	$r = df_new_om(S::class); /** @var S $r */
	$wid = df_website_id($w); /** @var int $wid */
	return !$id ? $r : (
		df_is_email($id) ? $r->loadBySubscriberEmail($id, $wid) : (
			$id instanceof C ? $r->loadByCustomer($id->getId(), $wid) : df_error()
		)
	);
}