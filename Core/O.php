<?php
namespace Df\Core;
use Magento\Framework\View\Element\BlockInterface;
/**
 * 2015-12-14
 * Необходимости реализации интерфейса @see \Magento\Framework\View\Element\BlockInterface
 * нужна нам, чтобы объекты класса O можно было использовать в качестве контекста
 * при рисовании блоков:
 * @used-by \Magento\Framework\View\Element\Template::setTemplateContext()
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/View/Element/Template.php#L141-L150
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/View/Element/Template.php#L255
 *
 * Я так понял, при использовании шаблонов *.phtml реализацию метода
 * @see \Magento\Framework\View\Element\BlockInterface::toHtml()
 * можно сделать пустой, потому что этот метод не вызывается:
 * @see \Magento\Framework\View\TemplateEngine\Php::render()
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/View/TemplateEngine/Php.php#L52-L68
 *
 * Кстати, я так и не разобрался до конца (не было надобности),
 * используется ли метод @see \Magento\Framework\View\Element\BlockInterface::toHtml()
 * для второго движка шаблонов:
 * @see \Magento\Framework\View\TemplateEngine\Xhtml::render()
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/View/TemplateEngine/Xhtml.php#L75-L86
 * Возможно, что тоже нет. Но я пока для своих целей не планирую использовать этот движок.
 *
 * @see df_block()
 */
class O extends \Magento\Framework\DataObject implements BlockInterface {
	/**
	 * Обратите внимание,
	 * что родительский деструктор вызывать не надо и по правилам PHP даже нельзя,
	 * потому что в родительском классе (и всех классах-предках)
	 * метод @see __destruct() не объявлен.
	 */
	function __destruct() {
		/**
		 * Для глобальных объекто-одиночек,
		 * чей метод @uses isDestructableSingleton() возвращает true,
		 * метод @see _destruct()
		 * будет вызван на событие «controller_front_send_response_after»:
		 * @see \Df\Core\Observer\ControllerFrontSendResponseAfter::execute()
		 *
		 * 2015-08-14
		 * Как правило, это связано с кэшированием данных на диск.
		 * Единственное на данный момент исключение (из РСМ 3):
		 * метод @see Df_Eav_Model_Translator::_destruct(),
		 * который использует деструктор не для кэширования на диск, а для логирования.
		 */
		if (!$this->isDestructableSingleton()) {
			$this->_destruct();
		}
	}

	/**
	 * Размещайте программный код деинициализации объекта именно в этом методе,
	 * а не в стандартном деструкторе @see __destruct().
	 *
	 * Не все потомки класса @see \Df\Core\O
	 * деинициализируется посредством стандартного деструктора.
	 *
	 * В частности, глобальные объекты-одиночки
	 * деинициализировать посредством глобального деструктора опасно,
	 * потому что к моменту вызова стандартного деструктора
	 * сборщик мусора Zend Engine мог уже уничтожить другие объекты,
	 * которые требуются для деинициализации.
	 *
	 * Для глобальных объекто-одиночек,
	 * чей метод @see isDestructableSingleton() возвращает true,
	 * метод @see _destruct() будет вызван на событие «controller_front_send_response_after»:
	 * @see \Df\Core\Observer::controllerFrontSendResponseAfter().
	 *
	 * 2015-08-14
	 * Как правило, это связано с кэшированием данных на диск.
	 * Единственное на данный момент исключение (из РСМ 3):
	 * метод @see Df_Eav_Model_Translator::_destruct(),
	 * который использует деструктор не для кэширования на диск, а для логирования.
	 *
	 * @used-by __destruct()
	 * @used-by \Df\Core\GlobalSingletonDestructor::process()
	 */
	function _destruct() {$this->cacheSave();}

	/**
	 * Этот метод отличается от методов @see getData(), @see offsetGet(), @see _getData()
	 * возможностью указать значение по умолчанию.
	 * @param string $key
	 * @param mixed|callable $default [optional]
	 * @return mixed
	 */
	function cfg($key, $default = null) {
		/** @var mixed $result */
		/**
		 * 2015-03-26
		 * Раньше здесь стоял вызов @see getData()
		 * Однако при новой реализации @see getData()
		 * разумнее вызывать сразу @uses offsetGet():
		 * нам тогда не приходится обрабатывать ситуацию с пустым ключом $key:
		 * при вызове @see cfg() ключ не может быть пустым.
		 *
		 * Обратите внимание, что вызывать @see _getData() здесь ошибочно,
		 * потому что тогда могут не сработать валидаторы и фильтры.
		 */
		$result = $this->offsetGet($key);
		// Некоторые фильтры заменяют null на некоторое другое значение,
		// поэтому обязательно учитываем равенство null
		// значения свойства ДО применения фильтров.
		//
		// Раньше вместо !is_null($result) стояло !$result.
		// !is_null выглядит логичней.
		//
		// 2015-02-10
		// Раньше код был таким:
		// $valueWasNullBeforeFilters = dfa($this->_valueWasNullBeforeFilters, $key, true);
		// return !is_null($result) && !$valueWasNullBeforeFilters ? $result : $default;
		// Изменил его ради ускорения.
		// Неожиданным результатом стала простота и понятность нового кода.
		return df_if1(
			null === $result
			|| !isset($this->_valueWasNullBeforeFilters[$key])
			|| $this->_valueWasNullBeforeFilters[$key]
			, $default
			, $result
		);
	}

	/**
	 * @override
	 * Обратите внимание, что мы сознательно никак не используем параметр $index
	 * и не поддерживаем сложные ключи $key, как это делает родительский метод.
	 *
	 * Фильтры и валидаторы для присутствующих в @see $_data ключей
	 * уже были применены при вызове @see _prop(),
	 * поэтому данные уже проверены и отфильтрованы,
	 * и при вызове @see getData() без параметров
	 * мы можем спокойно вернуть массив @see $_data.
	 *
	 * @see \Magento\Framework\DataObject::getData()
	 * @param string $key
	 * @param null|string|int $index
	 * @return mixed|array(string => mixed)
	 */
	function getData($key = '', $index = null) {return
		'' === $key ? $this->_data : $this->offsetGet($key)
	;}

	/**
	 * @used-by \Magento\Framework\Data\Collection::addItem()
	 * @return string|int
	 */
	function getId() {
		if (!isset($this->_id)) {
			$this->_id = df_uid();
		}
		return $this->_id;
	}

	/**
	 * @override
	 * @used-by getData()
	 * @see \Magento\Framework\DataObject::offsetGet()
	 * @see ArrayAccess::offsetGet()
	 * @param string $offset
	 * @return mixed
	*/
	function offsetGet($offset) {
		/** @var mixed $result */
		if (array_key_exists($offset, $this->_data)) {
			/**
			 * Фильтры и валидаторы для присутствующих в @see $_data ключей
			 * уже были применены при вызове @see \Df\Core\O::_prop(),
			 * поэтому данные уже проверены и отфильтрованы.
			 */
			$result = $this->_data[$offset];
		}
		else {
			// Обратите внимание, что фильтры и валидаторы применяются только единократно,
			// и повторно мы в эту ветку кода не попадём
			// из-за срабатывания условия array_key_exists($key, $this->_data) выше
			// (даже если фильтры для null вернут null, наличие ключа array('ключ' => null))
			// достаточно, чтобы не попадать в данную точку программы повторно.
			//
			// Обрабатываем здесь только те случаи,
			// когда запрашиваются значения неицициализированных свойств объекта
			$result = $this->_applyFilters($offset, null);
			$this->_validate($offset, $result);
			$this->_data[$offset] = $result;
		}
		return $result;
	}

	/**
	 * @override
	 * @see \Magento\Framework\DataObject::setData()
	 * @param string|array(string => mixed) $key
	 * @param mixed $value
	 * @return $this
	 */
	function setData($key, $value = null) {
		/**
		 * Раньше мы проводили валидацию лишь при извлечении значения свойства,
		 * в методе @see \Df\Core\O::getData().
		 * Однако затем мы сделали улучшение:
		 * перенести валидацию на более раннюю стадию — инициализацию свойства
		 * @see \Df\Core\O::setData(),
		 * и инициализацию валидатора/фильтра
		 * @see \Df\Core\O::_prop().
		 * Это улучшило диагностику случаев установки объекту некорректных значений свойств,
		 * потому что теперь мы возбуждаем исключительную ситуацию
		 * сразу при попытке установки некорректного значения.
		 * А раньше, когда мы проводили валидацию лишь при извлечении значения свойства,
		 * то при диагностике было не вполне понятно,
		 * когда конкретно объекту было присвоено некорректное значение свойства.
		 */
		if (is_array($key)) {
			$this->_checkForNullArray($key);
			$key = $this->_applyFiltersToArray($key);
			$this->_validateArray($key);
		}
		else {
			$this->_checkForNull($key, $value);
			$value = $this->_applyFilters($key, $value);
			$this->_validate($key, $value);
		}
		parent::setData($key, $value);
		return $this;
	}

	/**
	 * @override
	 * @param string|int $value
	 */
	function setId($value) {$this->_id = $value;}

	/**
	 * 2015-12-14
	 * Смотрите комментарий в шапке класса.
	 * @see df_block()
	 * @see \Magento\Framework\View\Element\BlockInterface::toHtml()
	 * @return string
	 */
	function toHtml() {df_abstract($this); return null;}

	/**
	 * @param string $key
	 * @param \Zend_Validate_Interface|\Df\Zf\Validate\Type|string|mixed[] $validator
	 * @param bool|null $isRequired [optional]
	 * @throws \Df\Core\Exception
	 * @return $this
	 */
	protected function _prop($key, $validator, $isRequired = null) {
		/**
		 * Полезная проверка!
		 * Как-то раз ошибочно описал поле без значения:
			private static $P__TYPE;
		 * И при вызове $this->_prop(self::$P__TYPE, DF_V_STRING_NE)
		 * получил диагностическое сообщение: «значение «» недопустимо для свойства «».»
		 */
		df_param_sne($key, 0);
		/**
		 * Обратите внимание, что если метод @see _prop() был вызван с двумя параметрами,
		 * то и count($arguments) вернёт 2,
		 * хотя в методе @see _prop() всегда доступен и 3-х параметр: $isRequired.
		 * Другими словами, @see func_get_args() не возвращает параметры по умолчанию,
		 * если они не были реально указаны при вызове текущего метода.
		 */
		/**
		 * Хотя документация к PHP говорит,
		 * что @uses func_num_args() быть параметром других функций лишь с версии 5.3 PHP,
		 * однако на самом деле @uses func_num_args() быть параметром других функций
		 * в любых версиях PHP 5 и даже PHP 4.
		 * http://3v4l.org/HKFP7
		 * http://php.net/manual/function.func-num-args.php
		 */
		if (2 < func_num_args()) {
			$arguments = func_get_args(); /** @var mixed[] $arguments */
			$isRequired = df_last($arguments);
			$hasRequiredFlag = is_bool($isRequired) || is_null($isRequired); /** @var bool $hasRequiredFlag */
			if ($hasRequiredFlag) {
				$validator = array_slice($arguments, 1, -1);
			}
			else {
				$isRequired = null;
				$validator = df_tail($arguments);
			}
		}
		$additionalValidators = []; /** @var \Zend_Validate_Interface[] $additionalValidators */
		$additionalFilters = []; /** @var \Zend_Filter_Interface[] $additionalFilters */
		if (!is_array($validator)) {
			$validator = Validator::resolveForProperty(
				$this, $validator, $key, $skipOnNull = false === $isRequired
			);
			df_assert($validator instanceof \Zend_Validate_Interface);
		}
		else {
			/** @var array(\Zend_Validate_Interface|Df_Zf_Validate_Type|string) $additionalValidatorsRaw */
			$additionalValidatorsRaw = df_tail($validator);
			$validator = Validator::resolveForProperty(
				$this, df_first($validator), $key, $skipOnNull = false === $isRequired
			);
			df_assert($validator instanceof \Zend_Validate_Interface);
			foreach ($additionalValidatorsRaw as $additionalValidatorRaw) {
				/** @var \Zend_Validate_Interface|\Zend_Filter_Interface|string $additionalValidatorsRaw */
				/** @var \Zend_Validate_Interface|\Zend_Filter_Interface $additionalValidator */
				$additionalValidator = Validator::resolveForProperty(
					$this, $additionalValidatorRaw, $key
				);
				if ($additionalValidator instanceof \Zend_Validate_Interface) {
					$additionalValidators[]= $additionalValidator;
				}
				if ($additionalValidator instanceof \Zend_Filter_Interface) {
					$additionalFilters[]= $additionalValidator;
				}
			}
		}
		$this->_validators[$key][] = $validator;
		if ($validator instanceof \Zend_Filter_Interface) {
			/** @var \Zend_Filter_Interface $filter */
			$filter = $validator;
			$this->_addFilter($key, $filter);
		}
		foreach ($additionalFilters as $additionalFilter) {
			/** @var \Zend_Filter_Interface $additionalFilter */
			$this->_addFilter($key, $additionalFilter);
		}
		/**
		 * Раньше мы проводили валидацию лишь при извлечении значения свойства,
		 * в методе @see getData().
		 * Однако затем мы сделали улучшение:
		 * перенести валидацию на более раннюю стадию — инициализацию свойства @see setData(),
		 * и инициализацию валидатора/фильтра @see _prop().
		 * Это улучшило диагностику случаев установки объекту некорректных значений свойств,
		 * потому что теперь мы возбуждаем исключительную ситуацию
		 * сразу при попытке установки некорректного значения.
		 * А раньше, когда мы проводили валидацию лишь при извлечении значения свойства,
		 * то при диагностике было не вполне понятно,
		 * когда конкретно объекту было присвоено некорректное значение свойства.
		 */
		/** @var bool $hasValueVorTheKey */
		$hasValueVorTheKey = array_key_exists($key, $this->_data);
		if ($hasValueVorTheKey) {
			Validator::checkProperty($this, $key, $this->_data[$key], $validator);
		}
		foreach ($additionalValidators as $additionalValidator) {
			/** @var \Zend_Validate_Interface $additionalValidator */
			$this->_validators[$key][] = $additionalValidator;
			if ($hasValueVorTheKey) {
				Validator::checkProperty($this, $key, $this->_data[$key], $additionalValidator);
			}
		}
		return $this;
	}

	/**
	 * @used-by cachedI()
	 * @return string[]
	 */
	protected function cached() {return [];}

	/**
	 * 2015-08-14
	 * Отныне значения свойств по умолчанию кэшируются для каждой витрины отдельно.
	 * Если нужно, чтобы кэшированным значением свойства
	 * могли пользоваться сразу все витрины, то перечислите это свойсто массиве,
	 * возвращаемом данным методом @see cachedGlobal()
	 * @used-by cachedGlobalI()
	 * @return string[]
	 */
	protected function cachedGlobal() {return [];}

	/**
	 * 2015-08-14
	 * @used-by cachedGlobalObjectsI()
	 * @return string[]
	 */
	protected function cachedGlobalObjects() {return [];}

	/**
	 * 2015-08-14
	 * Отныне по умолчанию для кэшируемых свойств
	 * используются упрощённые быстрый алгоритмы сериализации и десериализации
	 * @uses json_encode() / @uses json_decode()
	 * Эти алгоритмы быстры, но не умеют работать с объектами.
	 *
	 * Если Вам нужно кэшировать свойства, содержащее объекты,
	 * то перечислите это свойсто массиве,
	 * возвращаемом данным методом @see cachedObjects()
	 * Тогда для сериализации и десериализации этих свойств
	 * будут использоваться более медленные функции @see serialize() / @see unserialize().
	 *
	 * http://stackoverflow.com/a/7723730
	 * http://stackoverflow.com/a/804053
	 * @used-by cachedObjectsI()
	 * @return string[]
	 */
	protected function cachedObjects() {return [];}

	/**
	 * @used-by cacheKeyGlobal()
	 * @return string
	 */
	protected function cacheKeySuffix() {return '';}

	/**
	 * @used-by cacheSaveProperty()
	 * @return int|null
	 */
	protected function cacheLifetime() {return null; /* пожизненно*/}

	/**
	 * @used-by cacheSave()
	 */
	protected function cacheSaveBefore() {}

	/**
	 * @used-by cacheSaveProperty()
	 * @return string|string[]
	 */
	protected function cacheTags() {return [];}

	/**
	 * @used-by isCacheEnabled()
	 * @return string
	 */
	protected function cacheType() {return '';}

	/**
	 * @used-by cacheLoad()
	 * @used-by cacheSave()
	 * @return bool
	 */
	protected function isCacheEnabled() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->hasPropertiesToCache()
				&& (!$this->cacheType() || df_cache_enabled($this->cacheType()))
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Если этот метод вернёт true,
	 * то система вызовет метод @see \Df\Core\O::_destruct()
	 * не в стандартном деструкторе __destruct(),
	 * а на событие «controller_front_send_response_after»:
	 * @see \Df\Core\Observer::controllerFrontSendResponseAfter().
	 *
	 * Опасно проводить деинициализацию глобальных объектов-одиночек
	 * в стандартном деструкторе @see __destruct(),
	 * потому что к моменту вызова деструктора для данного объекта-одиночки
	 * сборщик Zend Engine мог уже уничтожить другие глобальные объекты,
	 * требуемые при деинициализации (например, для сохранения кэша).
	 *
	 * 2015-08-14
	 * Как правило, это связано с кэшированием данных на диск.
	 * Единственное на данный момент исключение (из РСМ 3):
	 * метод @see Df_Eav_Model_Translator::_destruct(),
	 * который использует деструктор не для кэширования на диск, а для логирования.
	 *
	 * @used-by __destruct()
	 * @used-by _construct()
	 * @return bool
	 */
	protected function isDestructableSingleton() {
		// 2015-08-14
		// Я так понял, что если объекту нужно сохранить кэш на диск,
		// то он — 100% должен делать это на событие «controller_front_send_response_after»
		// а не когда стандартный сборщик мусора будет всё рушить.
		return $this->hasPropertiesToCache();
	}

	/**
	 * Вызывайте этот метод для тех свойств,чьё кэшрованное значение изменилось.
	 * Такие свойства система заново сохранит в кэше в конце сеанса работы.
	 * Например, такое свойство может быть ассоциативным массивом,
	 * который заполняется постепенно, от сеанса к сеансу.
	 * Во время первого сеанса (начальное формирование кэша)
	 * могут быть заполнены лишь некоторые ключи такого массива
	 * (те, в которых была потребность в данном сеане),
	 * а вот во время следующих сеансов этот массив может дополняться новыми значениями.
	 * @param string $propertyName
	 */
	protected function markCachedPropertyAsModified($propertyName) {
		$this->_cachedPropertiesModified[$propertyName] = true;
	}

	/**
	 * @param string $key
	 * @param \Zend_Filter_Interface $filter
	 */
	private function _addFilter($key, \Zend_Filter_Interface $filter) {
		$this->_filters[$key][] = $filter;
		/**
		 * Не используем @see isset(), потому что для массива
		 * $array = array('a' => null)
		 * isset($array['a']) вернёт false,
		 * что не позволит нам фильтровать значения параметров,
		 * сознательно установленные в null при конструировании объекта.
		 */
		if (array_key_exists($key, $this->_data)) {
			$this->_data[$key] = $filter->filter($this->_data[$key]);
		}
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return mixed
	 */
	private function _applyFilters($key, $value) {
		/** @var \Zend_Filter_Interface[] $filters */
		/** @noinspection PhpParamsInspection */
		$filters = dfa($this->_filters, $key, []);
		foreach ($filters as $filter) {
			/** @var \Zend_Filter_Interface $filter */
			$value = $filter->filter($value);
		}
		return $value;
	}

	/**
	 * @param array(string => mixed) $params
	 * @return array(string => mixed)
	 */
	private function _applyFiltersToArray(array $params) {
		foreach ($params as $key => $value) {
			/** @var string $key */
			/** @var mixed $value */
			$params[$key] = $this->_applyFilters($key, $value);
		}
		return $params;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	private function _checkForNull($key, $value) {
		$this->_valueWasNullBeforeFilters[$key] = is_null($value);
	}

	/**
	 * @param array(string => mixed) $params
	 */
	private function _checkForNullArray(array $params) {
		foreach ($params as $key => $value) {
			/** @var string $key */
			/** @var mixed $value */
			$this->_checkForNull($key, $value);
		}
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @throws \Df\Core\Exception
	 */
	private function _validate($key, $value) {
		/** @var @var array(\Zend_Validate_Interface|Df_Zf_Validate_Type) $validators */
		/** @noinspection PhpParamsInspection */
		$validators = dfa($this->_validators, $key, []);
		foreach ($validators as $validator) {
			/** @var \Zend_Validate_Interface|\Df\Zf\Validate\Type $validator */
			Validator::checkProperty($this, $key, $value, $validator);
		}
	}

	/**
	 * @param array(string => mixed) $params
	 * @throws \Df\Core\Exception
	 */
	private function _validateArray(array $params) {
		foreach ($params as $key => $value) {
			/** @var string $key */
			/** @var mixed $value */
			$params[$key] = $this->_validate($key, $value);
		}
	}

	/**
	 * 2015-08-14
	 * @used-by hasPropertiesToCache()
	 * @return string[]
	 */
	private function cachedAll() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_merge($this->cachedAllGlobal(), $this->cachedAllPerStore());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-14
	 * @used-by cachedAll()
	 * @used-by cacheLoad()
	 * @used-by cacheSave()
	 * @return string[]
	 */
	private function cachedAllGlobal() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_merge($this->cachedGlobalI(), $this->cachedGlobalObjectsI());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-14
	 * @used-by cachedAll()
	 * @used-by cacheLoad()
	 * @used-by cacheSave()
	 * @return string[]
	 */
	private function cachedAllPerStore() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_merge($this->cachedI(), $this->cachedObjectsI());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-14
	 * @used-by _construct()
	 * @return string[]
	 */
	private function cachedAllSimple() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_merge($this->cachedI(), $this->cachedGlobalI());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-14
	 * @used-by cachedAllGlobal()
	 * @return string[]
	 */
	private function cachedGlobalI() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cachedGlobal();
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-14
	 * @used-by cachedAllGlobal()
	 * @return string[]
	 */
	private function cachedGlobalObjectsI() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cachedGlobalObjects();
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-14
	 * @used-by cacheLoad()
	 * @used-by cacheSave()
	 * @used-by isCacheEnabled()
	 * @return string[]
	 */
	private function cachedI() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cached();
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-08-14
	 * @used-by _construct()
	 * @return string[]
	 */
	private function cachedObjectsI() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cachedObjects();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by cacheKeyPerStore()
	 * @used-by cacheLoad()
	 * @used-by cacheSave()
	 * @return string
	 */
	private function cacheKeyGlobal() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $suffix */
			$suffix = (string)$this->cacheKeySuffix();
			if ('' !== $suffix) {
				/**
				 * 2015-08-15
				 * Не все символы позволены в качестве символов ключа кэширования.
				 * Как ни странно, неизвестно, что быстрее: @uses md5() или @see sha1()
				 * http://stackoverflow.com/questions/2722943
				 */
				$suffix = md5($suffix);
			}
			$this->{__METHOD__} = get_class($this) . $suffix;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by cacheLoad()
	 * @used-by cacheSave()
	 * @return string
	 */
	private function cacheKeyPerStore() {
		if (!isset($this->{__METHOD__})) {
			if (!\Df\Core\State::s()->storeInitialized()) {
				df_error(
					'При кэшировании в разрезе магазина для объекта класса «%s» произошёл сбой,'
					. ' потому что система ещё не инициализировала текущий магазин.'
					, get_class($this)
				);
			}
			$this->{__METHOD__} = $this->cacheKeyGlobal() . '[' . df_store()->getCode() . ']';
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by _construct()
	 */
	private function cacheLoad() {
		if ($this->isCacheEnabled()) {
			$this->cacheLoadArea($this->cachedAllGlobal(), $this->cacheKeyGlobal());
			/**
			 * При вызове метода @uses Df_Core_Model::getCacheKeyPerStore()
			 * может произойти исключительная ситуация в том случае,
			 * когда текущий магазин системы ещё не инициализирован
			 * (вызов Mage::app()->getStore() приводит к исключительной ситуации),
			 * поэтому вызываем @uses Df_Core_Model::getCacheKeyPerStore()
			 * только если в этом методе есть реальная потребность,
			 * т.е. если класс действительно имеет свойства, подлежащие кэшированию в разрезе магазина,
			 * и текущий магазин уже инициализирован.
			 */
			if ($this->cachedAllPerStore() && \Df\Core\State::s()->storeInitialized()) {
				$this->cacheLoadArea($this->cachedAllPerStore(), $this->cacheKeyPerStore());
			}
		}
	}

	/**
	 * @param string[] $propertyNames
	 * @param string $cacheKey
	 */
	private function cacheLoadArea(array $propertyNames, $cacheKey) {
		if ($propertyNames) {
			$cacheKey .= '::';
			foreach ($propertyNames as $propertyName) {
				/** @var string $propertyName */
				$this->cacheLoadProperty($propertyName, $cacheKey);
			}
		}
	}

	/**
	 * @param string $propertyName
	 * @param string $cacheKey
	 */
	private function cacheLoadProperty($propertyName, $cacheKey) {
		$cacheKey = $cacheKey . $propertyName;
		/** @var string|bool $propertyValueSerialized */
		$propertyValueSerialized = df_cache_load($cacheKey);
		if ($propertyValueSerialized) {
			/** @var mixed $propertyValue */
			/**
			 * Обратите внимание,
			 * что @see json_decode() в случае невозможности деколирования возвращает NULL,
			 * а @see unserialize в случае невозможности деколирования возвращает FALSE.
			 */
			$propertyValue =
				isset($this->_cachedPropertiesSimpleMap[$propertyName])
				? df_unserialize_simple($propertyValueSerialized)
				: df_ftn(df_unserialize($propertyValueSerialized))
			;
			if (!is_null($propertyValue)) {
				$this->_cachedPropertiesLoaded[$propertyName] = true;
				$this->$propertyName = $propertyValue;
			}
		}
	}

	/**
	 * @used-by _destruct()
	 */
	private function cacheSave() {
		if ($this->isCacheEnabled()) {
			$this->cacheSaveBefore();
			$this->cacheSaveArea($this->cachedAllGlobal(), $this->cacheKeyGlobal());
			/**
			 * При вызове метода @uses Df_Core_Model::getCacheKeyPerStore()
			 * может произойти исключительная ситуация в том случае,
			 * когда текущий магазин системы ещё не инициализирован
			 * (вызов Mage::app()->getStore() приводит к исключительной ситуации),
			 * поэтому вызываем @uses Df_Core_Model::getCacheKeyPerStore()
			 * только если в этом методе есть реальная потребность,
			 * т.е. если класс действительно имеет свойства, подлежащие кэшированию в разрезе магазина,
			 * и если текущий магазин уже инициализирован.
			 */
			if ($this->cachedAllPerStore() && \Df\Core\State::s()->storeInitialized()) {
				$this->cacheSaveArea($this->cachedAllPerStore(), $this->cacheKeyPerStore());
			}
		}
	}

	/**
	 * @buyer {buyer}
	 * @param string[] $propertyNames
	 * @param string $cacheKey
	 */
	private function cacheSaveArea(array $propertyNames, $cacheKey) {
		if ($propertyNames) {
			$cacheKey = $cacheKey . '::';
			foreach ($propertyNames as $propertyName) {
				/** @var string $propertyName */
				if (
						isset($this->$propertyName)
					&&
						(
								/**
								 * Сохраняем в кэше только те свойства,
								 * которые либо еще не сохранены там,
								 * либо чьё значение изменилось после загрузки из кэша:
								 * @see \Df\Core\O::markCachedPropertyAsModified()
								 */
								!isset($this->_cachedPropertiesLoaded[$propertyName])
							||
								isset($this->_cachedPropertiesModified[$propertyName])
						)

				) {
					$this->cacheSaveProperty($propertyName, $cacheKey);
				}
			}
		}
	}

	/**
	 * @param string $propertyName
	 * @param string $cacheKey
	 */
	private function cacheSaveProperty($propertyName, $cacheKey) {
		$cacheKey = $cacheKey . $propertyName;
		/** @var mixed $propertyValue */
		$propertyValue = $this->$propertyName;
		/** @var string|bool $propertyValueSerialized */
		$propertyValueSerialized =
			isset($this->_cachedPropertiesSimpleMap[$propertyName])
			? df_serialize_simple($propertyValue)
			: df_serialize($propertyValue)
		;
		if ($propertyValueSerialized) {
			df_cache_save(
				$data = $propertyValueSerialized
				,$id = $cacheKey
				,$tags = df_array($this->cacheTags())
				,$lifeTime = $this->cacheLifetime()
			);
		}
	}

	/**
	 * 2015-08-14
	 * @used-by isDestructableSingleton()
	 * @used-by isCacheEnabled()
	 * @return bool
	 */
	private function hasPropertiesToCache() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = !!$this->cachedAll();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @param [] $data [optional]
	 */
	function __construct(array $data = []) {
		parent::__construct($data);
		$this->_construct();
	}

	/**
	 * 2015-08-14
	 * @used-by __construct()
	 * 2015-10-25
	 * https://github.com/magento/magento2/issues/2188
	 * «Suggest to restore parameterless constructor _construct
	 * in the base class @see \Magento\Framework\DataObject
	 * (like Varien_Object::_construct() in Magento 1.x).
	 * For now, the method is duplicated in the base block, base model, base form and so on.»
	 */
	protected function _construct() {
		$this->_cachedPropertiesSimpleMap = array_flip($this->cachedAllSimple());
		if ($this->_data) {
			$this->_checkForNullArray($this->_data);
			/**
			 * Фильтры мы здесь пока применять не можем,
			 * потому что они ещё не инициализированы
			 * (фильтры будут инициализированы потомками
			 * уже после вызова @see \Df\Core\O::_construct()).
			 * Вместо этого применяем фильтры для начальных данных
			 * в методе @see \Df\Core\O::_prop(),
			 * а для дополнительных данных — в методе @see \Df\Core\O::setData().
			 */
		}
		$this->cacheLoad();
		if ($this->isDestructableSingleton()) {
			df_destructable_sg($this);
		}
	}

	/** @var array(string => bool) */
	private $_cachedPropertiesLoaded = [];
	/** @var array(string => bool) */
	private $_cachedPropertiesModified = [];
	/** @var array(string => null) */
	private $_cachedPropertiesSimpleMap;
	/** @var array(string => \Zend_Filter_Interface[]) */
	private $_filters = [];
	/**
	 * @used-by getId()
	 * @used-by setId()
	 * @var int|string
	 */
	private $_id;
	/** @var array(string => \Zend_Validate_Interface[]) */
	private $_validators = [];
	/** @var array(string => bool) */
	private $_valueWasNullBeforeFilters = [];

	/**
	 * 2016-07-12
	 * http://php.net/manual/function.get-called-class.php#115790
	 * @param string $c [optional]
	 * @param array(string => mixed) $p [optional]
	 * @return self
	 */
	static function s($c = null, array $p = []) {return df_sc($c ? df_cts($c) : static::class, static::class, $p);}

	/**
	 * @param string $class
	 * @param string|string[] $functions
	 * @return string[]
	 */
	protected static function _m($class, $functions) {
		df_assert($functions);
		/** @var string[] $result */
		$result = [];
		if (!is_array($functions)) {
			$functions = df_tail(func_get_args());
		}
		foreach ($functions as $function) {
			/** @var string $function */
			$result[]= df_cc_method($class, $function);
		}
		return $result;
	}
}