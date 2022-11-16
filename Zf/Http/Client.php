<?php
namespace Df\Zf\Http;
# 2020-03-01
final class Client extends \Zend_Http_Client {
	/**
	 * 2020-03-01
	 * @used-by \Df\API\Client::p()
	 * @return array(string => string)
	 */
	function auth():array {return $this->auth;}
}