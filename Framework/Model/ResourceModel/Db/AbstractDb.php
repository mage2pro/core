<?php
namespace Df\Framework\Model\ResourceModel\Db;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Adapter\AdapterInterface as IAdapter;
use Magento\Framework\Exception\LocalizedException as LE;
// 2018-03-21
/** @see \Dfe\Logo\R\Logo */
abstract class AbstractDb extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
	/**
	 * 2018-03-21
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @used-by \Dfe\Logo\R\Logo::loadByIds()
	 * @return Mysql|IAdapter
	 */
	function c() {return $this->getConnection();}
	
	/**
	 * 2018-03-21
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @used-by \Dfe\Logo\R\Logo::insertOnDuplicate()
	 * @param string|null $n [optional]
	 * @return string
	 * @throws LE
	 */
	function t($n = null) {return !$n ? $this->getMainTable() : $this->getTable($n);}
}