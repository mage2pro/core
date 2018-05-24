<?php
use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel as M;

/**
 * @see df_sc()
 * @param string $resultClass
 * @param string|null|array(string => mixed) $a2 [optional]
 * @param array(string => mixed) $a3 [optional]
 * @return DataObject|object
 */
function df_ic($resultClass, $a2 = null, array $a3 = []) {
	/** @var string|null $expectedClass */
	/** @var array(string => mixed) $params */
	list($expectedClass, $params) = is_array($a2) ? [null, $a2] : [$a2, $a3];
	return df_ar(new $resultClass($params), $expectedClass);
}

/**
 * 2016-08-24
 * 2016-09-04
 * Метод getId присутствует не только у потомков @see \Magento\Framework\Model\AbstractModel,
 * но и у классов сторонних библиотек, например:
 * https://github.com/CKOTech/checkout-php-library/blob/v1.2.4/com/checkout/ApiServices/Charges/ResponseModels/Charge.php?ts=4#L170-L173
 * По возможности, задействуем и сторонние реализации.
 *
 * К сожалению, нельзя здесь для проверки публичности метода использовать @see is_callable(),
 * потому что наличие @see \Magento\Framework\DataObject::__call()
 * приводит к тому, что @see is_callable всегда возвращает true.
 * Обратите внимание, что @uses method_exists(), в отличие от @see is_callable(),
 * не гарантирует публичную доступность метода:
 * т.е. метод может у класса быть, но вызывать его всё равно извне класса нельзя,
 * потому что он имеет доступность private или protected.
 * Пока эта проблема никак не решена.
 *
 * 2016-09-05
 * Этот код прекрасно работает с объектами классов типа @see \Magento\Directory\Model\Currency
 * благодаря тому, что @uses \Magento\Framework\Model\AbstractModel::getId()
 * не просто тупо считывает значение поля id, а вызывает метод
 * @see \Magento\Framework\Model\AbstractModel::getIdFieldName()
 * который, в ссвою очередь, узнаёт имя идентифицирующего поля из своего ресурса:
 * @see \Magento\Framework\Model\AbstractModel::_init()
 * @see \Magento\Directory\Model\ResourceModel\Currency::_construct()
 *
 * @see dfo_hash() использует тот же алгоритм, но не вызывает @see df_id() ради ускорения.
 *
 * @param object|int|string $o
 * @param bool $allowNull [optional]
 * @return int|string|null
 */
function df_id($o, $allowNull = false) {
	/** @var int|string|null $result */
	$result = !is_object($o) ? $o : (
		$o instanceof M || method_exists($o, 'getId') ? $o->getId() : null
	);
	df_assert($allowNull || $result);
	return $result;
}

/**
 * 2016-09-05
 * @param object|int|string $o
 * @param bool $allowNull [optional]
 * @return int
 */
function df_idn($o, $allowNull = false) {return df_nat(df_id($o, $allowNull), $allowNull);}

/**
 * 2017-01-12
 * 1) PHP, к сожалению, не разрешает в выражении с new делать выражением имя класса:
 * https://3v4l.org/U6TJR
 * Поэтому и создал эту небольшую функцию.
 * В отличие от @see df_new_om(), она не использует Object Manager.
 * 2) Впервые использую в своём коде возможность argument unpacking, появившуюся в PHP 5.6:
 * https://3v4l.org/eI2vf
 * http://stackoverflow.com/a/25781989
 * http://php.net/manual/en/functions.arguments.php#example-145
 * @used-by \Df\API\Facade::p()
 * @used-by \Df\Payment\Currency::f()
 * @param string $c
 * @param array ...$args
 * @return object
 */
function df_new($c, ...$args) {return new $c(...$args);}

/**
 * 2017-01-12
 * PHP, к сожалению, не разрешает в выражении с new делать выражением имя класса.
 * Поэтому и создал эту небольшую функцию.
 * В отличие от @see df_new_om(), она не использует Object Manager.
 * @used-by dfs_con()
 * @param string $c
 * @param string $expected
 * @param array ...$args
 * @return object
 */
function df_newa($c, $expected, ...$args) {return df_ar(df_new($c, ...$args), $expected);}

/**
 * 2016-01-06
 * 2017-01-12 Если Вам не нужен Object Manager, то используйте более простую функцию @see df_new()
 * @see df_new_omd()
 * @used-by df_cms_blocks()
 * @used-by df_controller_raw()
 * @used-by df_csv_o()
 * @used-by df_currency()
 * @used-by df_db_transaction()
 * @used-by df_load()
 * @used-by df_url_backend_new()
 * @used-by dfp_refund()
 * @used-by \Df\Customer\Plugin\Model\ResourceModel\AddressRepository::aroundSave()
 * @used-by \Df\Directory\Model\Country::c()
 * @used-by \Df\Framework\Plugin\Data\Form\Element\Fieldset::beforeAddField()
 * @used-by \Df\Framework\Upgrade::sEav()
 * @used-by \Df\Payment\ConfigProvider::p()
 * @used-by \Df\Sales\Model\Order\Payment::getInvoiceForTransactionId()
 * @used-by \Df\Sales\Plugin\Model\Order\Address\Renderer::addressConfig()
 * @used-by \Df\Sso\CustomerReturn::mc()
 * @used-by \Df\Sso\CustomerReturn::register()
 * @used-by \Dfe\SalesSequence\Plugin\Model\Manager::aroundGetSequence()
 * @used-by \Dfr\Framework\Plugin\Translate::en()
 * @used-by \Doormall\Shipping\Method::collectRates()
 * @used-by \Frugue\Shipping\Method::collectRates()
 * @param string $c
 * @param array(string => mixed) $p [optional]
 * @return \Magento\Framework\DataObject|object
 */
function df_new_om($c, array $p = []) {return df_om()->create($c, $p);}

/**
 * 2017-04-08
 * @used-by df_oq_sa()
 * @used-by \Dfe\Markdown\Plugin\Ui\Component\Form\Element\Wysiwyg::beforePrepare()
 * @used-by \Doormall\Shipping\Method::collectRates()
 * @used-by \Frugue\Shipping\Method::collectRates()
 * @param string $c
 * @param array(string => mixed) $data [optional]
 * @return \Magento\Framework\DataObject|object
 */
function df_new_omd($c, array $data = []) {return df_om()->create($c, ['data' => $data]);}

/**
 * 2015-03-23
 * @see df_ic()
 * @used-by \Df\Config\Settings::child()
 * @used-by \Df\Config\Settings::s()
 * @used-by \Df\Core\O::s()
 * @used-by \Df\GingerPaymentsBase\Settings::os()
 * @used-by \Df\Payment\Settings::_options()
 * @used-by \Dfr\Core\Dictionary::sc()
 * @param string $resultClass
 * @param string|null $expectedClass [optional]
 * @param array(string => mixed) $params [optional]
 * @param string $cacheKeySuffix [optional]
 * @return DataObject|object
 */
function df_sc($resultClass, $expectedClass = null, array $params = [], $cacheKeySuffix = '') {
	static $cache; /** @var array(string => object) $cache */
	$key = $resultClass . $cacheKeySuffix; /** @var string $key */
	if (!isset($cache[$key])) {
		$cache[$key] = df_ic($resultClass, $expectedClass, $params);
	}
	return $cache[$key];
}

/**
 * 2016-08-23
 * 2017-10-08
 * isset($object->{$key}) returns false for the non-public properties: https://3v4l.org/bRAcp
 * E.g., the following code returns `0`:
 * 		class A {private $b = 3;}
 * 		$a = new A;
 * 		echo intval(isset($a->{'b'}));
 * @see dfa()
 * @used-by \Df\StripeClone\Facade\Charge::cardData()
 * @param object $object
 * @param string|int $key
 * @param mixed|callable $default
 * @return mixed|null
 */
function dfo($object, $key, $default = null) {return
	isset($object->{$key}) ? $object->{$key} : df_call_if($default, $key)
;}

/**
 * 2017-07-11
 * It returns a singleton of a class from the $caller module with the $owner or $suf suffix.
 * The result should be a descendant of the $owner, and should exist (it is not defaulted to $owner).
 * @used-by df_oauth_app()
 * @used-by \Df\Zoho\App::s()
 * @used-by \Df\ZohoBI\API\Facade::s()
 * @param string|object $caller
 * @param string|null $suf [optional]
 * @return object
 */
function dfs_con($caller, $suf = null) {
	$owner = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['class']; /** @var string $owner */
	return dfcf(function($owner, $m, $suf) {return
		df_newa(df_con($m, $suf), $owner)
	;}, [$owner, df_module_name_c($caller), $suf ?: df_class_suffix($owner)]);
}