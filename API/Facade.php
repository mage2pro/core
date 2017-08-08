<?php
namespace Df\API;
use Df\API\Document as D;
use Df\API\Operation as O;
use Df\Core\Exception as DFE;
use Zend_Http_Client as Z;
/**
 * 2017-07-13
 * @see \Dfe\Moip\API\Facade\Customer
 * @see \Dfe\Moip\API\Facade\Notification
 * @see \Dfe\Moip\API\Facade\Order
 * @see \Dfe\Moip\API\Facade\Payment
 */
abstract class Facade {
	/**
	 * 2017-08-07
	 * @return O
	 */
	final function all() {return $this->p();}

	/**
	 * 2017-07-13
	 * @param array(string => mixed) $a
	 * @return O
	 * @throws DFE
	 */
	final function create(array $a) {return $this->p($a, Z::POST);}

	/**
	 * 2017-08-08
	 * @param string $id
	 * @return O
	 */
	final function delete($id) {return $this->p($id, Z::DELETE);}

	/**
	 * 2017-07-13
	 * @param string $id
	 * @return O
	 */
	final function get($id) {return $this->p($id);}

	/**
	 * 2017-07-13
	 * @used-by create()
	 * @used-by get()
	 * @used-by \Dfe\Moip\API\Facade\Customer::addCard()
	 * @param string|array(string => mixed) $p [optional]
	 * @param string|null $method [optional]
	 * @param string|null $path [optional]
	 * @return O
	 * @throws DFE
	 */
	final protected function p($p = [], $method = null, $path = null) {
		$method = $method ?: Z::GET;
		list($id, $p) = in_array($method, [Z::DELETE, Z::GET]) ? [$p, []] : [null, $p]; /** @var string|null $id */
		/** @var Client $client */
		$client = df_newa(df_con($this, 'API\\Client'), Client::class,
			$path ?: df_cc_path($this->prefix(), strtolower(df_class_l($this)) . 's', $id), $p, $method
		);
		/**
		 * 2017-08-08
		 * We use @uses df_eta() to handle the HTTP 204 («No Content») null response
		 * (e.g., on a @see Z::DELETE request).
		 */
		return new O(new D($p ?: df_clean(['id' => $id])), new D(df_eta($client->p())));
	}

	/**
	 * 2017-08-07
	 * @used-by p()
	 * @see \Dfe\Moip\API\Facade\Notification::prefix()
	 * @return string
	 */
	protected function prefix() {return '';}

	/**
	 * 2017-07-13
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @return self
	 */
	static function s() {return dfcf(function($c) {return new $c;}, [static::class]);}
}