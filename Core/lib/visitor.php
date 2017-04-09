<?php
use Df\Core\Visitor as V;
use Magento\Sales\Model\Order as O;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress as RA;
/**
 * 2016-05-20
 * @param string|null|O $ip [optional]
 * @return V
 */
function df_visitor($ip = null) {return V::sp(df_is_o($ip) ? $ip->getRemoteIp() : $ip);}

/** @return string */
function df_visitor_ip() {
	/** @var RA $a */ $a = df_o(RA::class); return df_my_local() ? '92.243.166.8' : $a->getRemoteAddress();
}