<?php
/**
 * 2015-12-25
 * Этот загадочный метод призван заменить код вида:
 * is_array($arguments) ? $arguments : func_get_args()
 * Теперь можно писать так: df_args(func_get_args())
 * @param mixed[] $arguments
 * @return mixed[]
 */
function df_args(array $arguments) {
	return !$arguments || !is_array($arguments[0]) ? $arguments : $arguments[0];
}

/**
 * 2015-12-30
 * Унифицирует вызов калбэков:
 * позволяет в качестве $method передавать как строковое название метода,
 * так и анонимную функцию, которая в качестве аргумента получит $object.
 * https://3v4l.org/pPGtA
 * @param object|mixed $object
 * @param string|callable $method
 * @param mixed[] $params [optional]
 * @return mixed
 */
function df_call($object, $method, $params = []) {
	/** @var mixed $result */
	if (!is_string($method)) {
		$result = call_user_func_array($method, array_merge([$object], $params));
	}
	else {
		/** @var bool $functionExists */
		$functionExists = function_exists($method);
		/** @var bool $methodExists */
		$methodExists = is_callable([$object, $method]);
		/** @var mixed $callable */
		if ($functionExists && !$methodExists) {
			$callable = $method;
		}
		else if ($methodExists && !$functionExists) {
			$callable = [$object, $method];
		}
		else if (!$functionExists) {
			df_error("Unable to call «{$method}».");
		}
		else {
			df_error("An ambiguous name: «{$method}».");
		}
		$result = call_user_func_array($callable, $params);
	}
	return $result;
}

/**
 * 2016-01-06
 * @param string $resultClass
 * @param array(string => mixed) $params [optional]
 * @return \Magento\Framework\DataObject|object
 */
function df_create($resultClass, array $params = []) {
	return df_om()->create($resultClass, ['data' => $params]);
}

/**
 * 2015-08-16
 * https://mage2.ru/t/95
 * https://mage2.pro/t/60
 * @param string $eventName
 * @param array(string => mixed) $data
 * @return void
 */
function df_dispatch($eventName, array $data = []) {
	/** @var \Magento\Framework\Event\ManagerInterface|\Magento\Framework\Event\Manager $manager */
	$manager = df_o(\Magento\Framework\Event\ManagerInterface::class);
	$manager->dispatch($eventName, $data);
}

/**
 * @param mixed $value
 * @return bool
 */
function df_empty_string($value) {return '' === $value;}

/**
 * К сожалению, не можем перекрыть Exception::getTraceAsString(),
 * потому что этот метод — финальный
 *
 * @param Exception $exception
 * @param bool $showCodeContext [optional]
 * @return string
 */
function df_exception_get_trace(Exception $exception, $showCodeContext = false) {
	return \Df\Qa\Message\Failure\Exception::i([
		\Df\Qa\Message\Failure\Exception::P__EXCEPTION => $exception
		,\Df\Qa\Message\Failure\Exception::P__NEED_LOG_TO_FILE => false
		,\Df\Qa\Message\Failure\Exception::P__NEED_NOTIFY_DEVELOPER => false
		,\Df\Qa\Message\Failure\Exception::P__SHOW_CODE_CONTEXT => $showCodeContext
	])->traceS();
}

/**
 * 2015-12-09
 * @param mixed $data
 * @return string
 */
function df_json_encode($data) {
	return df_is_dev() ? df_json_encode_pretty($data) : json_encode($data);
}

/**
 * 2015-12-06
 * @param mixed $data
 * @return string
 */
function df_json_encode_pretty($data) {
	return json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
}

/**
 * @used-by \Df\Core\Model\Format\Html\Tag::getOpenTagWithAttributesAsText()
 * @param mixed $argument
 * @return mixed
 */
function df_nop($argument) {return $argument;}

/**
 * @param mixed|null $value
 * @param bool $skipEmptyCheck [optional]
 * @return mixed[]
 */
function df_nta($value, $skipEmptyCheck = false) {
	if (!is_array($value)) {
		if (!$skipEmptyCheck) {
			df_assert(empty($value));
		}
		$value = [];
	}
	return $value;
}

/**
 * @param mixed|null $value
 * @return mixed
 */
function df_nts($value) {return !is_null($value) ? $value : '';}

/**
 * @param string $type
 * @return mixed
 */
function df_o($type) {
	/** @var array(string => mixed) */
	static $cache;
	if (!isset($cache[$type])) {
		$cache[$type] = df_om()->get($type);
	}
	return $cache[$type];
}

/**
 * @param object|\Magento\Framework\DataObject $entity
 * @param string $key
 * @param mixed $default
 * @return mixed|null
 */
function df_ok($entity, $key, $default = null) {
	/**
	 * Раньше функция @see df_a() была универсальной:
	 * она принимала в качестве аргумента $entity как массивы, так и объекты.
	 * В 99.9% случаев в качестве параметра передавался массив.
	 * Поэтому ради ускорения работы системы
	 * вынес обработку объектов в отдельную функцию @see df_ok()
	 */
	/** @var mixed $result */
	if (!is_object($entity)) {
		df_error('Попытка вызова df_ok для переменной типа «%s».', gettype($entity));
	}
	/** @var mixed|null $result */
	$result = null;
	if ($entity instanceof \Magento\Framework\DataObject) {
		$result = $entity->getData($key);
	}
	if (is_null($result)) {
		/**
		 * Например, @see stdClass.
		 * Используется, например, методом
		 * @used-by Df_Qiwi_Model_Action_Confirm::updateBill()
		 */
		$result = isset($entity->{$key}) ? $entity->{$key} : $default;
	}
	return $result;
}

/** @return \Df\Core\Helper\Output */
function df_output() {return \Df\Core\Helper\Output::s();}

/**
 * @param float|int $value
 * @return int
 */
function df_ceil($value) {return (int)ceil($value);}

/**
 * @param mixed $value
 * @return mixed
 */
function df_empty_to_null($value) {return $value ? $value : null;}

/**
 * @param float|int $value
 * @return int
 */
function df_floor($value) {return (int)floor($value);}

/**
 * @see df_sc()
 * @param string $resultClass
 * @param string $expectedClass
 * @param array(string => mixed) $params [optional]
 * @return \Magento\Framework\DataObject|object
 */
function df_ic($resultClass, $expectedClass, array $params = []) {
	/** @var \Magento\Framework\DataObject|object $result */
	$result = df_create($resultClass, $params);
	df_assert($result instanceof $expectedClass);
	return $result;
}

/**
 * @param bool $condition
 * @param mixed $resultOnTrue
 * @param mixed|null $resultOnFalse [optional]
 * @return mixed
 */
function df_if($condition, $resultOnTrue, $resultOnFalse = null) {
	return $condition ? $resultOnTrue : $resultOnFalse;
}

/**
 * @param \Magento\Framework\DataObject|mixed[]|mixed $value
 * @return void
 */
function df_log($value) {
	/** @var \Psr\Log\LoggerInterface|\Magento\Framework\Logger\Monolog $logger */
	$logger = df_o('Psr\Log\LoggerInterface');
	$logger->debug(df_dump($value));
}

/**
 * Оказывается, что нельзя писать
 * const RM_NULL = 'rm-null';
 * потому что глобальные константы появились только в PHP 5.3.
 * http://www.codingforums.com/php/303927-unexpected-t_const-php-version-5-2-17-a.html#post1363452
 */
define('RM_NULL', 'rm-null');

/**
 * @param mixed|string $value
 * @return mixed|null
 */
function df_n_get($value) {return (RM_NULL === $value) ? null : $value;}
/**
 * @param mixed|null $value
 * @return mixed|string
 */
function df_n_set($value) {return is_null($value) ? RM_NULL : $value;}

/**
 * 2015-08-13
 * @used-by df_o()
 * @used-by df_ic()
 * @return \Magento\Framework\ObjectManagerInterface|\Magento\Framework\App\ObjectManager
 */
function df_om() {return \Magento\Framework\App\ObjectManager::getInstance();}

/**
 * @param float|int $value
 * @return int
 */
function df_round($value) {return (int)round($value);}

/**
 * 2015-03-23
 * @see df_ic()
 * @param string $resultClass
 * @param string $expectedClass
 * @param array(string => mixed) $params [optional]
 * @param string $cacheKeySuffix [optional]
 * @return \Magento\Framework\DataObject|object
 */
function df_sc($resultClass, $expectedClass, array $params = [], $cacheKeySuffix = '') {
	/** @var array(string => object) $cache */
	static $cache;
	/** @var string $key */
	$key = $resultClass . $cacheKeySuffix;
	if (!isset($cache[$key])) {
		$cache[$key] = df_ic($resultClass, $expectedClass, $params);
	}
	return $cache[$key];
}

/**
 * 2015-12-06
 * @param string|object $id
 * @param callable $job
 * @param float $interval [optional]
 * @return mixed
 */
function df_sync($id, $job, $interval = 0.1) {
	return \Df\Core\Sync::execute(is_object($id) ? get_class($id) : $id, $job, $interval);
}