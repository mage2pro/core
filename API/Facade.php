<?php
namespace Df\API;
use Df\API\Operation as Op;
use Df\Core\Exception as DFE;
use Df\Core\O;
use Magento\Sales\Model\Order;
use Magento\Store\Model\Store;
use Zend_Http_Client as Z;
/**
 * 2017-07-13
 * @see \Dfe\AlphaCommerceHub\API\Facade
 * @see \Dfe\Moip\API\Facade\Customer
 * @see \Dfe\Moip\API\Facade\Notification
 * @see \Dfe\Moip\API\Facade\Order
 * @see \Dfe\Qiwi\API\Bill
 * @see \Dfe\Sift\API\Facade
 * @see \Dfe\Square\API\Facade\Card
 * @see \Dfe\Square\API\Facade\Customer
 * @see \Dfe\Square\API\Facade\Location
 * @see \Dfe\Square\API\Facade\LocationBased
 * @see \Dfe\TBCBank\API\Facade
 * @see \Dfe\Vantiv\API\Facade
 * @see \Inkifi\Mediaclip\API\Facade\Order
 * @see \Inkifi\Mediaclip\API\Facade\User
 * @see \Inkifi\Pwinty\API\Facade\Order
 * @see \Stock2Shop\OrderExport\API\Facade
 */
abstract class Facade {
	/**
	 * 2019-01-11
	 * @used-by self::s()
	 * @see \Dfe\Square\API\Facade\Card::__construct()
	 * @see \Inkifi\Mediaclip\API\Facade\User::__construct()
	 * @see \Inkifi\Mediaclip\API\Facade\Order\Item::__construct()
	 * @param Store|string|int|null $s [optional]
	 */
	function __construct($s = null) {$this->_store = df_store($s);}

	/**
	 * 2017-08-07
	 * @used-by \Dfe\Moip\API\Facade\Notification::targets()
	 * @used-by \Dfe\Moip\Test\CaseT\Notification::t01_all()
	 * @used-by \Dfe\Moip\Test\CaseT\Notification::t04_delete_all()
	 */
	final function all():Op {return $this->p();}

	/**
	 * 2017-07-13
	 * @used-by \Dfe\Moip\Backend\Enable::dfSaveAfter()
	 * @used-by \Dfe\Moip\Facade\Customer::create()
	 * @used-by \Dfe\Moip\Facade\Preorder::create()
	 * @used-by \Dfe\Moip\Test\CaseT\Customer::t01_create()
	 * @used-by \Dfe\Moip\Test\CaseT\Notification::create()
	 * @used-by \Dfe\Moip\Test\Order::create()
	 * @param array(string => mixed) $a
	 * @throws DFE
	 */
	final function create(array $a):Op {return $this->p($a, Z::POST);}

	/**
	 * 2017-08-08
	 * @used-by \Dfe\Moip\Test\CaseT\Notification::t03_delete()
	 * @used-by \Dfe\Moip\Test\CaseT\Notification::t04_delete_all()
	 * @param string $id
	 */
	final function delete($id):Op {return $this->p($id);}

	/**
	 * 2017-07-13
	 * 2017-04-05
	 * $suffix is @used-by \Inkifi\Pwinty\API\B\Order\Validate::p()
	 * @used-by ikf_api_oi()
	 * @used-by \Dfe\Moip\Facade\Customer::_get()
	 * @used-by \Dfe\Moip\Test\CaseT\Customer::t02_get()
	 * @used-by \Dfe\Sift\API\B\GetDecisions::decisions()
	 * @used-by \Dfe\Square\Facade\Charge::refund()
	 * @used-by \Dfe\Square\Facade\Customer::_get()
	 * @used-by \Inkifi\Mediaclip\API\Facade\Order\Item::files()
	 * @used-by \Inkifi\Pwinty\API\B\Order\Get::p()
	 * @used-by \Inkifi\Pwinty\API\B\Order\Validate::p()
	 * @param int|string $id
	 * @param string|null $suffix [optional]
	 * @param FacadeOptions|null $opt [optional]
	 */
	final function get($id, $suffix = null, FacadeOptions $opt = null):Op {return $this->p($id, null, $suffix, $opt);}

	/**
	 * 2017-09-04 @deprecated It is unused.
	 * @param int|string|array(string => mixed) $p
	 * @throws DFE
	 */
	final function patch($p):Op {return $this->p($p);}

	/**
	 * 2017-10-08
	 * @used-by \Dfe\AlphaCommerceHub\API\Facade\BankCard::op()
	 * @used-by \Dfe\Sift\API\B\Event::p()
	 * @used-by \Dfe\Square\Facade\Charge::create()
	 * @used-by \Dfe\Square\Facade\Customer::create()
	 * @used-by \Dfe\TBCBank\API\Facade::check()
	 * @used-by \Dfe\TBCBank\API\Facade::postAndReturnId()
	 * @used-by \Dfe\TBCBank\Facade\Charge::create()
	 * @used-by \Dfe\Vantiv\Facade\Charge::create()
	 * @used-by \Inkifi\Mediaclip\API\Facade\User::consolidate()
	 * @used-by \Inkifi\Pwinty\API\B\Order\AddImage::p()
	 * @used-by \Inkifi\Pwinty\API\B\Order\AddImages::p()
	 * @used-by \Inkifi\Pwinty\API\B\Order\Create::p()
	 * @used-by \Inkifi\Pwinty\API\B\Order\Submit::p()
	 * @used-by \Stock2Shop\OrderExport\Observer\OrderSaveAfter::execute()
	 * @param int|string|array(string => mixed) $p
	 * @param string|null $suffix [optional]
	 * @param FacadeOptions|null $opt [optional]
	 * @throws DFE
	 */
	final function post($p, $suffix = null, FacadeOptions $opt = null):Op {return $this->p($p, null, $suffix, $opt);}

	/**
	 * 2017-09-03
	 * @used-by \Dfe\Qiwi\Init\Action::preorder()
	 * @param array(string => mixed) $p
	 * @param string|null $suffix [optional]
	 * @throws DFE
	 */
	final function put(array $p, $suffix = null):Op {return $this->p($p, null, $suffix);}

	/**
	 * 2019-03-04
	 * @used-by self::p()
	 * @see \Dfe\Sift\API\Facade::adjustClient()
	 * @see \Inkifi\Mediaclip\API\Facade\Order\Item::adjustClient()
	 * @see \Inkifi\Pwinty\API\Facade\Order::adjustClient()
	 * @param Client $c
	 */
	protected function adjustClient(Client $c):void {}

	/**
	 * 2017-07-13
	 * @used-by self::all()
	 * @used-by self::create()
	 * @used-by self::delete()
	 * @used-by self::get()
	 * @used-by self::patch()
	 * @used-by self::put()
	 * @used-by \Dfe\Moip\API\Facade\Customer::addCard()
	 * @used-by \Dfe\Moip\API\Facade\Order::payment()
	 * @used-by \Dfe\Qiwi\API\Bill::refund()
	 * @used-by \Dfe\Square\API\Facade\Transaction::capture()
	 * @used-by \Dfe\Square\API\Facade\Transaction::void_()
	 * @used-by \Inkifi\Mediaclip\API\Facade\User::projects()
	 * @param int|string|array(string => mixed) $p [optional]
	 * @param string|null $method [optional]
	 * @param string|null $suffix [optional]
	 * @param FacadeOptions|null $opt [optional]
	 * @throws DFE
	 */
	final protected function p($p = [], $method = null, $suffix = null, FacadeOptions $opt = null):Op {
		$opt = $opt ?: $this->opts();
		$methodF = strtoupper(df_caller_f()); /** @var string $method */
		$method = $method ?: (in_array($methodF, [Z::POST, Z::PUT, Z::DELETE, Z::PATCH]) ? $methodF : Z::GET);
		# 2020-03-02, 2022-10-31
		# 1) Symmetric array destructuring requires PHP ≥ 7.1:
		#		[$a, $b] = [1, 2];
		# https://github.com/mage2pro/core/issues/96#issuecomment-593392100
		# We should support PHP 7.0.
		# https://3v4l.org/3O92j
		# https://www.php.net/manual/migration71.new-features.php#migration71.new-features.symmetric-array-destructuring
		# https://stackoverflow.com/a/28233499
		list($id, $p) = is_array($p) ? [null, $p] : [$p, []]; /** @var int|string|null $id */
		/** @uses \Df\API\Client::__construct() */
		$c = df_newa(df_con($this, 'API\\Client'), Client::class,
			# 2022-11-17
			# 1) PHP of any versions does not accept `null` for arguments of type `string`:
			# https://github.com/mage2pro/core/issues/174
			# 2) PHP ≥ 7 accepts `int` and `float` for arguments of type `string`: https://github.com/mage2pro/core/issues/174
			$this->path(df_nts($id), $suffix), $p, $method, $this->zfConfig()
			,(is_null($id) ? null : $this->storeByP($id)) ?: $this->_store
		); /** @var Client $c */
		$this->adjustClient($c);
		/**
		 * 2019-01-12 It is used by the Inkifi_Mediaclip module.
		 * 2019-04-05
		 * A silent request is not logged. @see \Df\API\Client::_p():
		 *	if (!$this->_silent) {
		 *		df_log_l($m, $ex);
		 *		df_sentry($m, $short, ['extra' => ['Request' => $req, 'Response' => $long]]);
		 *	}
		 * https://github.com/mage2pro/core/blob/4.2.8/API/Client.php#L358-L361
		 */
		if ($opt->silent()) {
			$c->silent();
		}
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
		/** @noinspection PhpParamsInspection */  # 2019-04-05 For `df_newa()`
		return new Op(
			new O(!$id ? $p : df_clean(['id' => $id, 'p' => $p]))
			/**
			 * 2018-08-11
			 * Some API's can return not a complex value (which is convertable to an array),
			 * but a simple textual value:
			 * @see \Stock2Shop\OrderExport\Observer\OrderSaveAfter::execute()
			 * So, now I handle this possibility.
			 */
			,df_newa($opt->resC(), O::class,
				is_array($res = $c->p()) ? df_eta($res) : df_array($res) /** @var mixed $res */
			)
		);
	}

	/**
	 * 2017-12-03
	 * 2022-11-17 PHP ≥ 7 accepts `int` and `float` for arguments of type `string`: https://github.com/mage2pro/core/issues/174
	 * @used-by self::p()
	 * @see \Dfe\AlphaCommerceHub\API\Facade::path()
	 * @see \Dfe\Sift\API\Facade\Event::path()
	 * @see \Dfe\TBCBank\API\Facade::path()
	 * @see \Dfe\Vantiv\API\Facade::path()
	 * @see \Inkifi\Mediaclip\API\Facade\User::path()
	 */
	protected function path(string $id, string $suffix = ''):string {return df_cc_path(
		$this->prefix(), strtolower(df_class_l($this)) . 's', urlencode($id), $suffix
	);}

	/**
	 * 2017-08-07
	 * @used-by self::path()
	 * @see \Dfe\Moip\API\Facade\Notification::prefix()
	 * @see \Dfe\Sift\API\Facade::prefix()
	 * @see \Dfe\Square\API\Facade\Card::prefix()
	 * @see \Dfe\Square\API\Facade\LocationBased::prefix()
	 */
	protected function prefix():string {return '';}

	/**
	 * 2019-02-26
	 * @used-by self::p()
	 * @see \Inkifi\Mediaclip\API\Facade\Order::storeByP()
	 * @param int|string|array(string => mixed)|array(int|string, array(int|string => mixed)) $p
	 * @return Store|null
	 */
	protected function storeByP($p) {return null;}

	/**
	 * 2017-10-19
	 * 2018-11-11
	 * Now we have also @see \Df\API\Client::zfConfig()
	 * 	*) Use @see \Df\API\Client::zfConfig() if you need to provide a common configuration for all API requests.
	 * 	*) Use @see self::zfConfig() if you need to provide a custom configuration for an API request group.
	 * @used-by self::p()
	 * @see \Dfe\Moip\API\Facade\Notification::zfConfig()
	 * @return array(string => mixed)
	 */
	protected function zfConfig():array {return [];}

	/**
	 * 2019-04-05
	 * @used-by self::p()
	 * @used-by \Inkifi\Mediaclip\API\Facade\User::projects()
	 */
	final protected function opts():FacadeOptions {return FacadeOptions::i();}

	/**
	 * 2019-01-11
	 * @used-by self::__construct()
	 * @used-by self::p()
	 * @var Store
	 */
	private $_store;

	/**
	 * 2017-07-13
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by ikf_api_oi()
	 * @used-by \Dfe\AlphaCommerceHub\Method::_refund()
	 * @used-by \Dfe\AlphaCommerceHub\Method::charge()
	 * @used-by \Dfe\AlphaCommerceHub\W\Reader::reqFilter()
	 * @used-by \Dfe\Sift\API\B\Event::p()
	 * @used-by \Dfe\Square\Facade\Customer::create()
	 * @used-by \Dfe\TBCBank\Facade\Charge::capturePreauthorized()
	 * @used-by \Dfe\TBCBank\Facade\Charge::create()
	 * @used-by \Dfe\TBCBank\Init::p()
	 * @used-by \Dfe\TBCBank\Test\CaseT\Init::transId()
	 * @used-by \Dfe\TBCBank\Test\CaseT\Regular::transId()
	 * @used-by \Dfe\TBCBank\W\Reader::reqFilter()
	 * @used-by \Dfe\Vantiv\Facade\Charge::create()
	 * @used-by \Inkifi\Pwinty\API\B\Order\Create::p()
	 * @used-by \Inkifi\Pwinty\API\B\Order\Validate::p()
	 * @used-by \Stock2Shop\OrderExport\Observer\OrderSaveAfter::execute()
	 * @param Store|Order $s [optional]
	 * @return self
	 */
	static function s($s = null):self {return dfcf(
		function($c, Store $s) {return new $c($s);}, [static::class, df_store($s)]
	);}
}