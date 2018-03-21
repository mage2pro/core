<?php
namespace Df\Framework\Model\ResourceModel\Db;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Adapter\AdapterInterface as IAdapter;
// 2018-03-21
/** @see \Dfe\Logo\R\Logo */
abstract class AbstractDb extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
	/**
	 * 2018-03-21
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @return Mysql|IAdapter
	 */
	function c() {return $this->getConnection();}
}