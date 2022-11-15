<?php
namespace Df\User\Model\ResourceModel;
class User extends \Magento\User\Model\ResourceModel\User {
	/**
	 * @used-by \Df\User\Plugin\Model\User::aroundAuthenticate()
	 * @return array(string => mixed)|false
	 */
	function loadByEmail(string $email):array {
		$c = $this->getConnection();
		return $c->fetchRow($c->select()->from($this->getMainTable())->where('email=:email'), ['email' => $email]);
	}

	/** @used-by \Df\User\Plugin\Model\User::aroundAuthenticate() */
	static function s():self {static $r; return $r ? $r : $r = df_o(__CLASS__);}
}


