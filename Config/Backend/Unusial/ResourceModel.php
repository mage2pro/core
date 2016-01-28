<?php
namespace Df\Config\Backend\Unusial;
use Magento\Framework\Model\ResourceModel\AbstractResource;
/**
 * 2016-01-26
 * @used-by \Df\Config\Backend\Unusial\Model::getResource()
 */
class ResourceModel extends AbstractResource {
	/**
	 * 2016-01-28
	 * @override
	 * @see \Magento\Framework\Model\ResourceModel\AbstractResource::_construct()
	 * @return void
	 */
	protected function _construct() {}

	/**
	 * 2016-01-28
	 * @override
	 * @see \Magento\Framework\Model\ResourceModel\AbstractResource::getConnection()
	 * @return \Magento\Framework\DB\Adapter\AdapterInterface
	 */
	protected function getConnection() {return df_conn();}

	/** @return $this */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}
