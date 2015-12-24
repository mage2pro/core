<?php
/** @return \Df\Core\Visitor */
function df_visitor() {return \Df\Core\Visitor::s();}

/** @return string */
function df_visitor_ip() {
	/** @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $a */
	$a = df_o(\Magento\Framework\HTTP\PhpEnvironment\RemoteAddress::class);
	return df_is_it_my_local_pc() ? '92.243.166.8' : $a->getRemoteAddress();
}


