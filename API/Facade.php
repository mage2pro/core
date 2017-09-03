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
 * @see \Dfe\Qiwi\API\Bill
 */
abstract class Facade {
	/**
	 * 2017-08-07
	 * @used-by \Dfe\Moip\API\Facade\Notification::targets()
	 * @used-by \Dfe\Moip\T\CaseT\Notification::t01_all()
	 * @used-by \Dfe\Moip\T\CaseT\Notification::t04_delete_all()
	 * @return O
	 */
	final function all() {return $this->p();}

	/**
	 * 2017-07-13
	 * @used-by \Dfe\Moip\Backend\Enable::dfSaveAfter()
	 * @used-by \Dfe\Moip\Facade\Customer::create()
	 * @used-by \Dfe\Moip\Facade\Preorder::create()
	 * @used-by \Dfe\Moip\T\CaseT\Customer::t01_create()
	 * @used-by \Dfe\Moip\T\CaseT\Notification::create()
	 * @used-by \Dfe\Moip\T\Order::create()
	 * @param array(string => mixed) $a
	 * @return O
	 * @throws DFE
	 */
	final function create(array $a) {return $this->p($a, Z::POST);}

	/**
	 * 2017-08-08
	 * @used-by \Dfe\Moip\T\CaseT\Notification::t03_delete()
	 * @used-by \Dfe\Moip\T\CaseT\Notification::t04_delete_all()
	 * @param string $id
	 * @return O
	 */
	final function delete($id) {return $this->p($id);}

	/**
	 * 2017-07-13
	 * @used-by \Dfe\Moip\Facade\Customer::_get()
	 * @used-by \Dfe\Moip\T\CaseT\Customer::t02_get()
	 * @param string $id
	 * @return O
	 */
	final function get($id) {return $this->p($id);}

	/**
	 * 2017-09-04
	 * Currently it is never used.
	 * @param int|string|array(string => mixed)|array(int|string, array(int|string => mixed)) $p
	 * @return O
	 * @throws DFE
	 */
	final function patch($p) {return $this->p($p);}

	/**
	 * 2017-09-03
	 * Currently it is never used.
	 * @param int|string|array(string => mixed)|array(int|string, array(int|string => mixed)) $p
	 * @return O
	 * @throws DFE
	 */
	final function put($p) {return $this->p($p);}

	/**
	 * 2017-07-13
	 * @used-by all()
	 * @used-by create()
	 * @used-by delete()
	 * @used-by get()
	 * @used-by patch()
	 * @used-by put()
	 * @used-by \Dfe\Moip\API\Facade\Customer::addCard()
	 * @param int|string|array(string => mixed)|array(int|string, array(int|string => mixed)) $p [optional]
	 * @param string|null $method [optional]
	 * @param string|null $path [optional]
	 * @return O
	 * @throws DFE
	 */
	final protected function p($p = [], $method = null, $path = null) {
		$methodF = strtoupper(df_caller_f()); /** @var string $method */
		$method = $method ?: (in_array($methodF, [Z::POST, Z::PUT, Z::DELETE, Z::PATCH]) ? $methodF : Z::GET);
		/** @var int|string|null $id */
		list($id, $p) = !is_array($p) ? [$p, []] : (!df_is_assoc($p) ? $p : [null, $p]);
		/** @var Client $client */
		$client = df_newa(df_con($this, 'API\\Client'), Client::class,
			$path ?: df_cc_path($this->prefix(), strtolower(df_class_l($this)) . 's', $id), $p, $method
		);
		/**
		 * 2017-08-08
		 * We use @uses df_eta() to handle the HTTP 204 («No Content») null response
		 * (e.g., on a @see Z::DELETE request).
		 */
		return new O(new D($id ? $p : df_clean(['id' => $id, 'p' => $p])), new D(df_eta($client->p())));
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