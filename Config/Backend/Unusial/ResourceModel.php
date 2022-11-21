<?php
namespace Df\Config\Backend\Unusial;
use Magento\Framework\DB\Adapter\AdapterInterface as IAdapter;
use Magento\Framework\Model\ResourceModel\AbstractResource;
/**
 * 2016-01-26
 * @used-by \Df\Config\Backend\Unusial\Model::getResource()
 */
class ResourceModel extends AbstractResource {
	/**
	 * 2016-01-28
	 * @override
	 * @see AbstractResource::getConnection()
	 */
	function getConnection():IAdapter {return df_conn();}
	
	/**
	 * 2016-01-28
	 * @override
	 * @see AbstractResource::_construct()
	 */
	protected function _construct() {}

	/**
	 * 2016-01-26
	 * @used-by Model::getResource()
	 */
	static function s():self {static $r; return $r ? $r : $r = new self;}
}