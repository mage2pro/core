<?php
use Magento\Customer\Model\Customer as C;
use Magento\Newsletter\Model\Subscriber as S;
use Magento\Quote\Api\Data\CartInterface as IQ;
use Magento\Quote\Model\Quote as Q;
use Magento\Sales\Api\Data\OrderInterface as IO;
use Magento\Sales\Model\Order as O;
use Magento\Store\Model\Store;
use Magento\Store\Model\Website as W;

/**
 * 2024-06-02
 * 1) "Implement `df_subscriber()`": https://github.com/thehcginstitute-com/m1/issues/627
 * 2) https://3v4l.org/tIHdP
 * 3) "Port `df_subscriber()` from `thehcginstitute-com/m1`": https://github.com/mage2pro/core/issues/399
 * 4) @deprecated It is unused.
 * @param string|C|O|null $v [optional]
 * @param W|Store|int|string|null|bool $w [optional]
 */
function df_subscriber($v = null, $w = null):S {
	$r = df_new_om(S::class); /** @var S $r */
	return !$v ? $r : (
		df_is_email($v) ? $r->loadBySubscriberEmail($v, df_website_id($w)) : (
			$v instanceof C ? $r->loadByCustomer(df_customer_id($v), $v->getWebsiteId()) : (
				df_is_oq($v) ? $r->loadBySubscriberEmail(df_oq_email($v), df_website_id($v)) :
					df_error(['v' => $v])
			)
		)
	);
}