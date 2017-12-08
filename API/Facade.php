<?php
namespace Df\API;
use Df\API\Document as D;
use Df\API\Operation as O;
use Df\Core\Exception as DFE;
use Zend_Http_Client as Z;
/**
 * 2017-07-13
 * @see \Dfe\AlphaCommerceHub\API\Facade
 * @see \Dfe\Moip\API\Facade\Customer
 * @see \Dfe\Moip\API\Facade\Notification
 * @see \Dfe\Moip\API\Facade\Order
 * @see \Dfe\Qiwi\API\Bill
 * @see \Dfe\Square\API\Facade\Card
 * @see \Dfe\Square\API\Facade\Customer
 * @see \Dfe\Square\API\Facade\Location
 * @see \Dfe\Square\API\Facade\LocationBased
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
	 * @used-by \Dfe\Square\Facade\Charge::refund()
	 * @used-by \Dfe\Square\Facade\Customer::_get()
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
	 * 2017-10-08
	 * @used-by \Dfe\AlphaCommerceHub\API\Facade\BankCard::op()
	 * @used-by \Dfe\Square\Facade\Charge::create()
	 * @used-by \Dfe\Square\Facade\Customer::create()
	 * @param int|string|array(string => mixed)|array(int|string, array(int|string => mixed)) $p
	 * @param string|null $suffix [optional]
	 * @return O
	 * @throws DFE
	 */
	final function post($p, $suffix = null) {return $this->p($p, null, $suffix);}

	/**
	 * 2017-09-03
	 * @used-by \Dfe\Qiwi\Init\Action::preorder()
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
	 * @used-by \Dfe\Moip\API\Facade\Order::payment()
	 * @used-by \Dfe\Qiwi\API\Bill::refund()
	 * @used-by \Dfe\Square\API\Facade\Transaction::capture()
	 * @used-by \Dfe\Square\API\Facade\Transaction::void_()
	 * @param int|string|array(string => mixed)|array(int|string, array(int|string => mixed)) $p [optional]
	 * @param string|null $method [optional]
	 * @param string|null $suffix [optional]
	 * @return O
	 * @throws DFE
	 */
	final protected function p($p = [], $method = null, $suffix = null) {
		$methodF = strtoupper(df_caller_f()); /** @var string $method */
		$method = $method ?: (in_array($methodF, [Z::POST, Z::PUT, Z::DELETE, Z::PATCH]) ? $methodF : Z::GET);
		/** @var int|string|null $id */
		list($id, $p) = !is_array($p) ? [$p, []] : (!df_is_assoc($p) ? $p : [null, $p]);
		$client = df_newa(df_con($this, 'API\\Client'), Client::class,
			$this->path($id, $suffix), $p, $method, $this->zfConfig()
		); /** @var Client $client */
		/**
		 * 2017-08-08
		 * We use @uses df_eta() to handle the HTTP 204 («No Content») null response
		 * (e.g., on a @see Z::DELETE request).
		 * 2017-12-03
		 * The previous code was:
		 * 		return new O(new D($id ? $p : df_clean(['id' => $id, 'p' => $p])), new D(df_eta($client->p())));
		 * https://github.com/mage2pro/core/blob/3.3.40/API/Facade.php#L123
		 * It was introduced at 2017-09-03 in the 2.11.10 version by the following commit:
		 * https://github.com/mage2pro/core/commit/31063704
		 * I think, $id instead of !$id was just a bug.
		 * Prior the 2.11.10 version, the code was:
		 * 		return new O(new D($p ?: df_clean(['id' => $id])), new D(df_eta($client->p())));
		 * https://github.com/mage2pro/core/blob/2.11.9/API/Facade.php#L68
		 */
		return new O(new D(!$id ? $p : df_clean(['id' => $id, 'p' => $p])), new D(df_eta($client->p())));
	}

	/**
	 * 2017-12-03
	 * @used-by p()
	 * @see \Dfe\AlphaCommerceHub\API\Facade::path()
	 * @param int|string|null $id
	 * @param string|null $suffix
	 * @return string
	 */
	protected function path($id, $suffix) {return df_cc_path(
		$this->prefix(), strtolower(df_class_l($this)) . 's', urlencode($id), $suffix
	);}

	/**
	 * 2017-08-07
	 * @used-by path()
	 * @see \Dfe\Moip\API\Facade\Notification::prefix()
	 * @see \Dfe\Square\API\Facade\Card::prefix()
	 * @see \Dfe\Square\API\Facade\LocationBased::prefix()
	 * @return string
	 */
	protected function prefix() {return '';}

	/**
	 * 2017-10-19
	 * @used-by p()
	 * @see \Dfe\Moip\API\Facade\Notification::zfConfig()
	 * @return array(string => mixed)
	 */
	protected function zfConfig() {return [];}

	/**
	 * 2017-07-13
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Dfe\AlphaCommerceHub\Method::_refund()
	 * @used-by \Dfe\AlphaCommerceHub\Method::charge()
	 * @used-by \Dfe\AlphaCommerceHub\W\Reader::reqFilter()
	 * @return self
	 */
	static function s() {return dfcf(function($c) {return new $c;}, [static::class]);}
}