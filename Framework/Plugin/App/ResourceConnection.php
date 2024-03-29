<?php
namespace Df\Framework\Plugin\App;
use Magento\Framework\App\ResourceConnection as Sb;
# 2020-11-22
final class ResourceConnection {
	/**
	 * 2020-11-22
	 * @see \Magento\Framework\App\ResourceConnection::getConnection()
	 * @return string[]
	 */
	function beforeGetConnection(Sb $sb, string $n = Sb::DEFAULT_CONNECTION):array {return [
		Sb::DEFAULT_CONNECTION !== $n || !self::$CUSTOM ? $n : self::$CUSTOM
	];}

	/**
	 * 2020-11-22
	 * @used-by df_with_conn()
	 * @used-by self::beforeGetConnection()
	 * @var string|null
	 */
	static $CUSTOM;
}