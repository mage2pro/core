<?php
use Df\Core\Exception as DFE;
use Magento\Customer\Model\Address\AbstractAddress as AA;
use Throwable as T;

/**
 * 2024-05-16 "Implement `df_assert_address_type()`": https://github.com/mage2pro/core/issues/372
 * @deprecated It is unused.
 * @param string|T $m [optional]
 * @throws DFE
 */
function df_assert_address_type(string $t, $m = null):string {return df_assert_in(
	$t, [AA::TYPE_BILLING, AA::TYPE_SHIPPING], $m ?: "Invalid address type: «{$t}»."
);}