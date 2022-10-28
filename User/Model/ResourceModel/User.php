<?php
namespace Df\User\Model\ResourceModel;
class User extends \Magento\User\Model\ResourceModel\User {
	/**
	 * @param string $email
	 * @return array(string => mixed)|false
	 */
	function loadByEmail($email) {
		$conn = $this->getConnection();
		$select = $conn->select()->from($this->getMainTable())->where('email=:email');
		$binds = ['email' => $email];
		return $conn->fetchRow($select, $binds);
	}

	/** @used-by \Df\User\Plugin\Model\User::aroundAuthenticate() */
	static function s():self {static $r; return $r ? $r : $r = df_o(__CLASS__);}
}


