<?php
use Df\Core\A;
use Magento\Framework\DataObject;
/**
 * @param mixed|mixed[] $value
 * @return mixed[]|string[]|float[]|int[]
 */
function df_array($value) {return is_array($value) ? $value : [$value];}

/**
 * 2015-02-07
 * Обратите внимание,
 * что во многих случаях эффективней использовавать @see array_filter() вместо @see df_clean().
 * http://php.net/manual/function.array-filter.php
 * @see array_filter() с единственным параметром удалит из массива все элементы,
 * чьи значения приводятся к логическому «false».
 * Т.е., помимо наших array('', null, array()),
 * @see array_filter() будет удалять из массива также элементы со значениями «false» и «0».
 * Если это соответствует требуемому поведению в конретной точке программного кода,
 * то используйте именно @see array_filter(),
 * потому что встроенная функция @see array_filter() в силу реализации на языке С
 * будет работать на порядки быстрее, нежели @see df_clean().
 *
 * 2015-01-22
 * Теперь из исходного массива будут удаляться элементы,
 * чьим значением является пустой массив.
 * @param mixed[] $array
 * @param mixed[] $additionalValuesToClean [optional]
 * @return mixed[]
 */
function df_clean(array $array, array $additionalValuesToClean = []) {
	/** @var mixed[] $result */
	$result = [];
	// 2015-01-22
	// Теперь из исходного массива будут удаляться элементы,
	// чьим значением является пустой массив.
	/** @var mixed[] $valuesToClean */
	$valuesToClean = array_merge(['', null, []], $additionalValuesToClean);
	/** @var bool $isAssoc */
	$isAssoc = df_is_assoc($array);
	foreach ($array as $key => $value) {
		/** @var int|string $key */
		/** @var mixed $value */
		if (!in_array($value, $valuesToClean, true)) {
			if ($isAssoc) {
				$result[$key]= $value;
			}
			else {
				$result[]= $value;
			}
		}
	}
	return $result;
}

/**
 * Отличается от @see df_clean() дополнительным удалением их исходного массива элементов,
 * чьим значением является применение @see df_cdata() к пустой строке.
 * Пример применения:
 * @used-by Df_1C_Cml2_Export_Processor_Catalog_Product::getElement_Производитель()
 * @param array(string => mixed) $array
 * @return array(string => mixed)
 */
function df_clean_xml(array $array) {return df_clean($array, [df_cdata('')]);}

/**
 * 2015-02-11
 * Аналог @see array_column() для коллекций.
 * Ещё один аналог: @see \Magento\Framework\Data\Collection::getColumnValues(),
 * но его результат — не ассоциативный.
 *
 * 2016-07-31
 * При вызове с 2-мя параметрами эта функция идентична функции @see df_each()
 *
 * @param \Traversable|array(int|string => DataObject) $collection
 * @param string|\Closure $methodForValue
 * @param string|null $methodForKey [optional]
 * @return array(int|string => mixed)
 */
function df_column($collection, $methodForValue, $methodForKey = null) {
	/** @var  $result */
	$result = [];
	foreach ($collection as $id => $object) {
		/** @var int|string $id */
		/** @var DataObject|callable $object */
		/** @var int|string $key */
		$key = !$methodForKey ? $id : df_call($object, $methodForKey);
		$result[$key] = df_call($object, $methodForValue);
	}
	return $result;
}

/**
 * 2015-02-07
 * Эта функция аналогична методу @see \Magento\Framework\Data\Collection::walk(),
 * и даже может использоваться вместо @see \Magento\Framework\Data\Collection::walk(),
 * однако, в отличие от @see \Magento\Framework\Data\Collection::walk(),
 * она способна работать не только с коллекцией,
 * но также с массивом объектов и объектом, поддерживающим интерфейс @see \Traversable.
 *
 * 2016-07-31
 * При вызове с 2-мя параметрами эта функция идентична функции @see df_column()
 *
 * @param \Traversable|array(int|string => DataObject) $collection
 * @param string|callable $method
 * @param mixed ...$params
 * @return mixed[]
 */
function df_each($collection, $method, ...$params) {
	/** @var array(int|string => mixed) $result */
	$result = [];
	foreach ($collection as $key => $object) {
		/** @var int|string $key */
		/** @var object $object */
		$result[$key] = df_call($object, $method, $params);
	}
	return $result;
}

/**
 * 2015-02-18
 * По смыслу функция @see df_extend() аналогична методу @see \Magento\Framework\Simplexml\Element::extend()
 * и предназначена для слияния настроечных опций,
 * только, в отличие от @see \Magento\Framework\Simplexml\Element::extend(),
 * @see df_extend() сливает не XML, а ассоциативные массивы.
 *
 * Обратите внимание, что вместо @see df_extend() нельзя использовать ни
 * @see array_replace_recursive(), ни @see array_merge_recursive(),
 * ни тем более @see array_replace() и @see array_merge()
 * Нерекурсивные аналоги отметаются сразу, потому что не способны сливать вложенные структуры.
 * Но и стандартные рекурсивные функции тоже не подходят:
 *
 * 1)
 * array_merge_recursive(array('width' => 180), array('width' => 200))
 * вернёт: array(array('width' => array(180, 200)))
 * http://php.net/manual/function.array-merge-recursive.php
 * Наша функция df_extend(array('width' => 180), array('width' => 200))
 * вернёт array('width' => 200)
 *
 * 2)
 * array_replace_recursive(array('x' => array('A', 'B')), array('x' => 'C'))
 * вернёт: array('x' => array('С', 'B'))
 * http://php.net/manual/function.array-replace-recursive.php
 * Наша функция df_extend(array('x' => array('A', 'B')), array('x' => 'C'))
 * вернёт array('x' => 'C')
 *
 * @param array(string => mixed) $defaults
 * @param array(string => mixed) $newValues
 * @return array(string => mixed)
 */
function df_extend(array $defaults, array $newValues) {
	/** @var array(string => mixed) $result */
	// Здесь ошибочно было бы $result = [],
	// потому что если ключ отсутствует в $newValues,
	// то тогда он не попадёт в $result.
	$result = $defaults;
	foreach ($newValues as $key => $newValue) {
		/** @var int|string $key */
		/** @var mixed $newValue */
		/** @var mixed $defaultValue */
		$defaultValue = dfa($defaults, $key);
		if (!is_array($defaultValue)) {
			// 2016-08-23
			// unset добавил сегодня.
			if (is_null($newValue)) {
				unset($result[$key]);
			}
			else {
				$result[$key] = $newValue;
			}
		}
		else {
			if (is_array($newValue)) {
				$result[$key] = df_extend($defaultValue, $newValue);
			}
			else {
				if (is_null($newValue)) {
					unset($result[$key]);
				}
				else {
					// Если значение по умолчанию является массивом,
					// а новое значение не является массивом,
					// то это наверняка говорит об ошибке программиста.
					df_error(
						"df_extend: значением по умолчанию ключа «{$key}» является массив {defaultValue},"
						. "\nоднако программист ошибочно пытается заместить его"
						. ' значением {newValue} типа «{newType}», что недопустимо:'
						. "\nзамещаемое значение для массива должно быть либо массивом, либо «null»."
						,[
							'{defaultValue}' => df_t()->singleLine(df_dump($defaultValue))
							,'{newType}' => gettype($newValue)
							,'{newValue}' => df_dump($newValue)
						]
					);
				}
			}
		}
	}
	return $result;
}

/**
 * Функция возвращает null, если массив пуст.
 * Обратите внимание, что неверен код
	$result = reset($array);
	return (false === $result) ? null : $result;
 * потому что если @uses reset() вернуло false, это не всегда означает сбой метода:
 * ведь первый элемент массива может быть равен false.
 * @see df_last()
 * @see df_tail()
 * @param array $array
 * @return mixed|null
 */
function df_first(array $array) {return !$array ? null : reset($array);}

/**
 * 2015-03-13
 * Отсекает последний элемент массива и возвращает «голову» (массив оставшихся элементов).
 * Похожая системная функция @see array_pop() возвращает отсечённый последний элемент.
 * Противоположная системная функция @see df_tail() отсекает первый элемент массива.
 * @used-by Df_Core_Model_Action::delegate()
 * @used-by Portal_Page_Block_Frontend::portalRenderChild()
 * @param mixed[] $array
 * @return mixed[]|string[]
 */
function df_head(array $array) {return array_slice($array, 0, -1);}

/**
 * 2015-12-30
 * Преобразует коллекцию или массив в карту.
 * @param string|\Closure $method
 * @param \Traversable|array(int|string => DataObject) $items
 * @return mixed[]
 */
function df_index($method, $items) {return array_combine(df_column($items, $method), $items);}

/**
 * 2015-02-07
 * Обратите внимание, что алгоритмов проверки массива на ассоциативность найдено очень много:
 * http://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
 * Я уже давно (несоколько лет) использую приведённый ниже.
 * Пока он меня устраивает, да и сама задача такой проверки
 * возникает у меня в Российской сборке Magento редко
 * и не замечал её особого влияния на производительность системы.
 * Возможно, другие алгоритмы лучше, лень разбираться.
 * @param array(int|string => mixed) $array
 * @return bool
 */
function df_is_assoc(array $array) {
	$result = false;
	foreach (array_keys($array) as $key => $value) {
		/**
		 * Согласно спецификации PHP, ключами массива могут быть целые числа, либо строки.
		 * Третьего не дано.
		 * http://php.net/manual/language.types.array.php
		 */
		if (
			/**
			 * Раньше тут стояло !is_int($key)
			 * Способ проверки $key !== $value нашёл по ссылке ниже:
			 * http://www.php.net/manual/en/function.is-array.php#84488
			 */
			$key !== $value
		) {
			$result = true;
			break;
		}
	}
	return $result;
}

/**
 * 2015-04-17
 * Проверяет, является ли массив многомерным.
 * http://stackoverflow.com/a/145348
 * Пока никем не используется.
 * @param array(int|string => mixed) $array
 * @return bool
 */
function df_is_multi(array $array) {
	/** @var bool $result */
	$result = false;
	foreach ($array as $value) {
		/** @var mixed $value */
		if (is_array($value)) {
			$result = true;
			break;
		}
	}
	return $result;
}

/**
 * 2015-02-11
 * Эта функция отличается от @see iterator_to_array() тем, что допускает в качестве параметра
 * не только @see \Traversable, но и массив.
 * @param \Traversable|array $traversable
 * @return array
 */
function df_iterator_to_array($traversable) {
	return is_array($traversable) ? $traversable : iterator_to_array($traversable);
}

/**
 * 2016-01-29
 * @see df_usort()
 * @param array(int|string => mixed) $array
 * @return array(int|string => mixed)
 */
function df_ksort(array $array) {
	ksort($array);
	return $array;
}

// Глобальные константы появились в PHP 5.3.
// http://www.codingforums.com/php/303927-unexpected-t_const-php-version-5-2-17-a.html#post1363452
const DF_AFTER = 1;
const DF_BEFORE = -1;

/**
 * 2015-02-11
 * Эта функция аналогична @see array_map(), но обладает 3-мя дополнительными возможностями:
 * 1) её можно применять не только к массивам, но и к @see \Traversable.
 * 2) она позволяет удобным способом передавать в $callback дополнительные параметры
 * 3) позволяет передавать в $callback ключи массива
 * до и после основного параметра (элемента массива).
 * Обратите внимание, что
		df_map('Df_Cms_Model_ContentsMenu_Applicator::i', $this->getCmsRootNodes())
 * эквивалентно
		$this->getCmsRootNodes()->walk('Df_Cms_Model_ContentsMenu_Applicator::i')
 * @param callable $f
 * @param array(int|string => mixed)|\Traversable $array
 * @param mixed|mixed[] $paramsToAppend [optional]
 * @param mixed|mixed[] $paramsToPrepend [optional]
 * @param int $keyPosition [optional]
 * @return array(int|string => mixed)
 */
function df_map(callable $f, $array, $paramsToAppend = [], $paramsToPrepend = [], $keyPosition = 0) {
	$array = df_iterator_to_array($array);
	/** @var array(int|string => mixed) $result */
	if (!$paramsToAppend && !$paramsToPrepend && 0 === $keyPosition) {
		$result = array_map($f, $array);
	}
	else {
		$paramsToAppend = df_array($paramsToAppend);
		$paramsToPrepend = df_array($paramsToPrepend);
		$result = [];
		foreach ($array as $key => $item) {
			/** @var int|string $key */
			/** @var mixed $item */
			/** @var mixed[] $primaryArgument */
			switch ($keyPosition) {
				case DF_BEFORE:
					$primaryArgument = [$key, $item];
					break;
				case DF_AFTER:
					$primaryArgument = [$item, $key];
					break;
				default:
					$primaryArgument = [$item];
			}
			/** @var mixed[] $arguments */
			$arguments = array_merge($paramsToPrepend, $primaryArgument, $paramsToAppend);
			$result[$key] = call_user_func_array($f, $arguments);
		}
	}
	return $result;
}

/**
 * 2018-08-09
 * Функция принимает аргументы в любом порядке.
 * @param callable|array(int|string => mixed)|array[]\Traversable $a1
 * @param callable|array(int|string => mixed)|array[]|\Traversable $a2
 * @return array(int|string => mixed)
 */
function df_map_k($a1, $a2) {
	/** @var callable $callback */
	/** @var array(int|string => mixed)|\Traversable $array */
	if (is_callable($a1)) {
		$callback = $a1;
		df_assert_traversable($a2);
		$array = $a2;
	}
	else {
		df_assert_callable($a2);
		$callback = $a2;
		$array = $a1;
	}
	return df_map($callback, $array, [], [], DF_BEFORE);
}

/**
 * Оба входных массива должны быть ассоциативными
 * @param array(string => mixed) $array1
 * @param array(string => mixed) $array2
 * @return array(string => mixed)
 */
function df_merge_not_empty(array $array1, array $array2) {return array_filter($array2) + $array1;}

/**
 * 2015-02-11
 * Эта функция отличается от @see array_merge() только тем,
 * что все вместо нескольких параметров принимает массив из параметров.
 * Это бывает удобно в функциональном программировании, например:
 * @used-by Df_Dataflow_Model_Registry_MultiCollection::getEntities()
 * @used-by Df_Dellin_Model_Request_Rate::getDates()
 * @param array(array(int|string => mixed)) $arrays
 * @return array(int|string => mixed)
 */
function df_merge_single(array $arrays) {return call_user_func_array('array_merge', $arrays); }

/**
 * @param array(string => mixed) $array
 * @return array(string => mixed)
 */
function df_key_uc(array $array) {return dfa_change_key_case($array, CASE_UPPER);}

/**
 * Функция возвращает null, если массив пуст.
 * Если использовать @see end() вместо @see df_last(),
 * то указатель массива после вызова end сместится к последнему элементу.
 * При использовании @see df_last() смещения указателя не происходит,
 * потому что в @see df_last() попадает лишь копия массива.
 *
 * Обратите внимание, что неверен код
 	$result = end($array);
 	return (false === $result) ? null : $result;
 * потому что если @uses end() вернуло false, это не всегда означает сбой метода:
 * ведь последний элемент массива может быть равен false.
 * http://www.php.net/manual/en/function.end.php#107733
 * @see df_first()
 * @see df_tail()
 * @param mixed[] $array
 * @return mixed|null
 */
function df_last(array $array) {return !$array ? null : end($array);}

/**
 * @used-by Df_InTime_Api::call()
 * http://stackoverflow.com/a/18576902
 * @param mixed $value
 * @return array
 */
function df_stdclass_to_array($value) {return df_json_decode(json_encode($value));}

/**
 * Отсекает первый элемент массива и возвращает хвост (аналог CDR в Lisp).
 * Обратите внимание, что если исходный массив содержит меньше 2 элементов,
 * то функция вернёт пустой массив.
 * @see df_first()
 * @see df_last()
 * @param mixed[] $array
 * @return mixed[]|string[]
 */
function df_tail(array $array) {return array_slice($array, 1);}

/**
 * http://en.wikipedia.org/wiki/Tuple
 * @param array $arrays
 * @return array
 */
function df_tuple(array $arrays) {
	/** @var array $result */
	$result = [];
	/** @var int $count */
	$countItems = max(array_map('count', $arrays));
	for ($ordering = 0; $ordering < $countItems; $ordering++) {
		/** @var array $item */
		$item = [];
		foreach ($arrays as $arrayName => $array) {
			$item[$arrayName]= dfa($array, $ordering);
		}
		$result[$ordering] = $item;
	}
	return $result;
}

/**
 * 2016-07-18
 * @see df_ksort()
 * @param array(int|string => mixed) $array
 * @param callable $comparator
 * @return array(int|string => mixed)
 */
function df_usort(array $array, callable $comparator) {
	/**
	 * 2016-08-10
	 * С сегодняшнего дня я использую функцию @see df_caller_f(),
	 * которая, в свою очередь, использует @debug_backtrace()
	 * Это приводит к сбою: «Warning: usort(): Array was modified by the user comparison function».
	 * http://stackoverflow.com/questions/3235387
	 * https://bugs.php.net/bug.php?id=50688
	 * По этой причине добавил собаку.
	 */
	/** @noinspection PhpUsageOfSilenceOperatorInspection */
	@usort($array, $comparator);
	return $array;
}

/**
 * Раньше функция @see dfa() была универсальной:
 * она принимала в качестве аргумента $entity как массивы, так и объекты.
 * В 99.9% случаев в качестве параметра передавался массив.
 * Поэтому ради ускорения работы системы
 * вынес обработку объектов в отдельную функцию @see dfo()
 * @param mixed[]|array(string => int[]) $array
 * @param string|int $key
 * @param mixed|callable $default
 * @return mixed|null
 */
function dfa(array $array, $key, $default = null) {
	/**
	 * 2016-02-13
	 * Нельзя здесь писать return df_if2(isset($array[$key]), $array[$key], $default);
	 * потому что получим «Notice: Undefined index».
	 *
	 * 2016-08-07
	 * В @see \Closure мы можем безнаказанно передавать параметры,
	 * даже если closure их не поддерживает https://3v4l.org/9Sf7n
	 */
	return isset($array[$key]) ? $array[$key] : df_call_if($default, $key);
}

/**
 * 2016-08-21
 * @param mixed[] $array
 * @return A
 */
function dfao(array $array) {return new A($array);}

/**
 * 2015-02-07
 * Аналог @see array_change_key_case() с поддержкой UTF-8.
 * Реализацию взял отсюда: http://php.net/manual/function.array-change-key-case.php#107715
 * Обратите внимание, что @see array_change_key_case() некорректно работает с UTF-8.
 * Например:
		$countries = array('Россия' => 'RU', 'Украина' => 'UA', 'Казахстан' => 'KZ');
	array_change_key_case($countries, CASE_UPPER)
 * вернёт:
	(
		[РнссШя] => RU
		[УЪраШна] => UA
		[Њазахстан] => KZ
	)
 * @used-by df_key_uc()
 * @param array(string => mixed) $input
 * @param int $case
 * @return array(string => mixed)
 */
function dfa_change_key_case(array $input, $case = CASE_LOWER) {
	$case = ($case == CASE_LOWER) ? MB_CASE_LOWER : MB_CASE_UPPER;
	/** @var array(string => mixed) $result */
	$result = [];
	foreach ($input as $key => $value) {
		/** @var string $key */
		/** @var mixed $value */
		$result[mb_convert_case($key, $case, 'UTF-8')] = $value;
	}
	return $result;
}

/**
 * 2016-09-07
 * @param string[] $a
 * @param int $length
 * @return string[]
 * @uses mb_substr()
 */
function dfa_chop(array $a, $length) {return df_map('mb_substr', $a, [0, $length]);}

/**
 * Этот метод предназначен для извлечения некоторого значения
 * из многомерного массива посредством нотации ключ1/ключ2/ключ3
 * Например: dfa_deep(array('test' => array('eee' => 3)), 'test/eee') вернёт «3».
 * Обратите внимание, что ядро Magento реализует аналогичный алгоритм
 * в методе @see \Magento\Framework\DataObject::getData()
 * Наша функция работает не только с объектами @see \Magento\Framework\DataObject, но и с любыми массивами.
 * @param array(string => mixed)$array
 * @param string|string[] $path
 * @param mixed $defaultValue [optional]
 * @return mixed|null
 */
function dfa_deep(array $array, $path, $defaultValue = null) {
	if (is_array($path)) {
		$pathParts = $path;
	}
	else {
		df_param_string_not_empty($path, 1);
		/**
		 * 2015-02-06
		 * Обратите внимание, что если разделитель отсутствует в строке,
		 * то @uses explode() вернёт не строку, а массив со одим элементом — строкой.
		 * Это вполне укладывается в наш универсальный алгоритм.
		 */
		/** @var string[] $pathParts */
		$pathParts = df_explode_xpath($path);
	}
	/** @var mixed|null $result */
	$result = null;
	while ($pathParts) {
		$result = dfa($array, array_shift($pathParts));
		if (is_array($result)) {
			$array = $result;
		}
		else {
			if ($pathParts) {
				// Ещё не прошли весь путь, а уже наткнулись на не-массив.
				$result = null;
			}
			break;
		}
	}
	if (is_null($result)) {
		$result = $defaultValue;
	}
	return $result;
}

/**
 * 2015-12-07
 * @param array(string => mixed) $array
 * @param string|string[] $path
 * @param mixed $value
 * @return void
 */
function dfa_deep_set(array &$array, $path, $value) {
	if (is_array($path)) {
		$pathParts = $path;
	}
	else {
		df_param_string_not_empty($path, 1);
		/**
		 * 2015-02-06
		 * Обратите внимание, что если разделитель отсутствует в строке,
		 * то @uses explode() вернёт не строку, а массив со одим элементом — строкой.
		 * Это вполне укладывается в наш универсальный алгоритм.
		 */
		/** @var string[] $pathParts */
		$pathParts = df_explode_xpath($path);
	}
	/** @var array(string => mixed) $a */
	$a = &$array;
	while ($pathParts) {
		/** @var string $key */
		$key = array_shift($pathParts);
		if (!isset($a[$key])) {
			$a[$key] = [];
		}
		$a = &$a[$key];
		if (!is_array($a)) {
			$a = [];
		}
	}
	$a = $value;
}

/**
 * Эта функция отличается от @uses array_fill() только тем,
 * что разрешает параметру $length быть равным нулю.
 * Если $length = 0, то функция возвращает пустой массив.
 * @uses array_fill() разрешает параметру $num (аналог $length)
 * быть равным нулю только начиная с PHP 5.6:
 * http://php.net/manual/function.array-fill.php
 * «5.6.0	num may now be zero. Previously, num was required to be greater than zero»
 * @param int $startIndex
 * @param int $length
 * @param mixed $value
 * @return mixed[]
 */
function dfa_fill($startIndex, $length, $value) {
	return !$length ? [] : array_fill($startIndex, $length, $value);
}

/**
 * 2016-10-25
 * Оказалось, что в ядре нет такой функции.
 * @param array(int|string => mixed) $a
 * @param mixed $value
 * @param callable $f
 * @param mixed|null $d [optional]
 * @return int|string|null
 */
function dfa_find_k(array $a, $value, $f, $d = null) {
	/** @var int|string|null $result */
	$result = $d;
	foreach ($a as $k => $v) {
		/** @var int|string $key */
		/** @var mixed $v */
		if (call_user_func($f, $v, $value)) {
			$result = $k;
			break;
		}
	}
	return $result;
}

/**
 * 2016-10-25
 * Оказалось, что в ядре нет такой функции.
 * @param array(int|string => mixed) $a
 * @param mixed $value
 * @param callable $f
 * @param mixed|null $d [optional]
 * @return mixed|null
 */
function dfa_find_v(array $a, $value, $f, $d = null) {
	/** @var int|string|null $result */
	$key = dfa_find_k($a, $value, $f, $d);
	return is_null($key) ? null : $a[$key];
}

/**
 * 2016-03-25
 * http://stackoverflow.com/a/1320156
 * @used-by df_cc_class()
 * @used-by df_cc_class_uc()
 * @param array $a
 * @return mixed[]
 */
function dfa_flatten(array $a) {
	/** @var mixed[] $result */
	$result = [];
	array_walk_recursive($a, function($a) use (&$result) {$result[]= $a;});
	return $result;
}

/**
 * 2016-07-31
 * @param \Traversable|array(int|string => DataObject) $collection
 * @return int[]|string[]
 */
function dfa_ids($collection) {return df_each($collection, 'getId');}

/**
 * 2016-08-26
 * Вставляет новые элементы внутрь массива.
 * http://php.net/manual/function.array-splice.php
 * Если нужно вставить только один элемент, то необязательно обрамлять его в массив.
 * @param mixed[] $a
 * @param int $position
 * @param mixed|mixed[] $addition
 * @return mixed[]
 */
function dfa_insert(array $a, $position, $addition) {
	array_splice($a, $position, 0, $addition);
	return $a;
}

/**
 * 2015-02-07
 * Функция предназначена для работы только с ассоциативными массивами!
 * Фантастически лаконичное и красивое решение!
 * Вынес его в отдельную функцию только для того, чтобы не забыть!
 * Пример применения:
 * @used-by Df_Directory_Model_Resource_Country_Collection::toOptionArrayRm()
 * Операция «+» игнорирует те элементы второго массива,
 * ключи которого присутствуют в первом массиве:
 * «The keys from the first array will be preserved.
 * If an array key exists in both arrays,
 * then the element from the first array will be used
 * and the matching key's element from the second array will be ignored.»
 * http://php.net/manual/function.array-merge.php
 * Остальные элементы второго массива (ключи которых отсутствуют в первом массиве)
 * будут добавлены к результату.
 * Например:
		$source = array(
			'RU' => 'Россия', 'KZ' => 'Казахстан', 'TJ' => 'Таджикистан', 'US' => 'США', 'CA' => 'Канада'
		);
  		$priorityItems = array('TJ' => 'Таджикистан', 'CA' => 'Канада');
		print_r($priorityItems + $source);
 * Вернёт:
		Array
		(
			[TJ] => Таджикистан
			[CA] => Канада
			[RU] => Россия
			[KZ] => Казахстан
			[US] => США
		)
 * http://3v4l.org/CFM4L
 * @param array(string => mixed) $source
 * @param array(string => mixed) $priorityItems
 * @return array(string => mixed)
 */
function dfa_prepend(array $source, array $priorityItems) {return $priorityItems + $source;}

/**
 * 2015-02-07
 * Функция предназначена для работы только с ассоциативными массивами!
 * Фантастически лаконичное и красивое решение!
 * Вынес его в отдельную функцию, чтобы не забыть!
 * Например:
		$source = array(
			'RU' => 'Россия', 'KZ' => 'Казахстан', 'TJ' => 'Таджикистан','US' => 'США','CA' => 'Канада'
 		);
		$priorityKeys = array('TJ', 'CA');
		print_r(dfa_prepend_by_keys($source, $priorityKeys));
 * Вернёт:
	 Array
	 (
		 [TJ] => Таджикистан
		 [CA] => Канада
		 [RU] => Россия
		 [KZ] => Казахстан
		 [US] => США
	 )
 * http://3v4l.org/QYffO
 * Обратите внимание, что @uses array_flip() корректно работает с пустыми массивами:
	print_r(array_flip(array()));
 * вернёт array()
 * http://3v4l.org/Kd01X
 * @uses dfa_prepend()
 * @used-by Df_Directory_Model_Resource_Country_Collection::toOptionArrayRm()
 * @param array(string => mixed) $source
 * @param string[] $priorityKeys
 * @return array(string => mixed)
 */
function dfa_prepend_by_keys(array $source, array $priorityKeys) {
	return dfa_prepend($source, dfa_select_ordered($source, $priorityKeys));
}

/**
 * 2015-02-07
 * Функция предназначена для работы только с ассоциативными массивами!
 * Фантастически лаконичное и красивое решение!
 * Вынес его в отдельную функцию, чтобы не забыть!
 * Например:
		$source = array(
			'Россия' => 'RU'
			,'Казахстан' => 'KZ'
			,'Таджикистан' => 'TJ'
			,'США' => 'US'
			,'Канада' => 'CA'
		);
		$priorityValues = array('TJ', 'CA');
		print_r(dfa_prepend_by_values($source, $priorityValues));
 * вернёт:
		Array
		(
			[Таджикистан] => TJ
			[Канада] => CA
			[Россия] => RU
			[Казахстан] => KZ
			[США] => US
		)
 * http://3v4l.org/tNms4
 * @uses dfa_prepend_by_keys()
 * @used-by Df_Directory_Model_Resource_Country_Collection::toOptionArrayRm()
 * @param array(string => mixed) $source
 * @param string[] $priorityValues
 * @return array(string => mixed)
 */
function dfa_prepend_by_values(array $source, array $priorityValues) {
	return array_flip(dfa_prepend_by_keys(array_flip($source), $priorityValues));
}

/**
 * 2016-07-31
 * Возвращает повторяющиеся элементы исходного массива (не повторяя их).
 * https://3v4l.org/YEf5r
 * В алгоритме пользуемся тем, что @uses array_unique() сохраняет ключи исходного массива.
 * @param array $a
 * @return array
 */
function dfa_repeated(array $a) {
	return array_values(array_unique(array_diff_key($a, array_unique($a))));
}

/**
 * 2016-09-02
 * @uses array_flip() корректно работает с пустыми массивами.
 * @param array(string => mixed) $a
 * @param string[] ...$keys
 * @return array(string => mixed)
 */
function dfa_unset(array $a, ...$keys) {return array_diff_key($a, array_flip(df_args($keys)));}

/**
 * 2015-02-11
 * Из ассоциативного массива $source выбирает элементы с ключами $keys.
 * В отличие от @see dfa_select_ordered() не учитывает порядок ключей $keys
 * и поэтому работает быстрее, чем @see dfa_select_ordered().
 * @param array(string => string)|\Traversable $source
 * @param string[] $keys
 * @return array(string => string)
 */
function dfa_select($source, array $keys)  {
	return array_intersect_key(df_iterator_to_array($source), array_fill_keys($keys, null));
}

/**
 * 2015-02-08
 * Из ассоциативного массива $source выбирает элементы с ключами $orderedKeys
 * и возвращает их в том же порядке, в каком они перечислены в $orderedKeys.
 * Если порядок ключей не важен, но используйте более быстрый аналог @see dfa_select().
 * @param array(string => string)|\Traversable $source
 * @param string[] $orderedKeys
 * @return array(string => string)
 */
function dfa_select_ordered($source, array $orderedKeys)  {
	/** @var array(string => null) $resultKeys */
	$resultKeys = array_fill_keys($orderedKeys, null);
	/** @var array(string => string) $resultWithGarbage */
	$resultWithGarbage = array_merge($resultKeys, df_iterator_to_array($source));
	return array_intersect_key($resultWithGarbage, $resultKeys);
}

/**
 * Работает в разы быстрее, чем @see array_unique()
 * «Just found that array_keys(array_flip($array)); is amazingly faster than array_unique();.
  * About 80% faster on 100 element array,
  * 95% faster on 1000 element array
  * and 99% faster on 10000+ element array.»
 * http://stackoverflow.com/questions/5036504/php-performance-question-faster-to-leave-duplicates-in-array-that-will-be-searc#comment19991540_5036538
 * http://www.php.net/manual/en/function.array-unique.php#70786
 * 2015-02-06
 * Обратите внимание, что т.к. алгоритм @see dfa_unique_fast() использует @uses array_flip(),
 * то @see dfa_unique_fast() можно применять только в тех ситуациях,
 * когда массив содержит только строки и целые числа,
 * иначе вызов @uses array_flip() завершится сбоем уровня E_WARNING:
 * «array_flip(): Can only flip STRING and INTEGER values»
 * http://magento-forum.ru/topic/4695/
 * В реальной практике сбой случается, например, когда массив содержит значение null:
 * http://3v4l.org/bat52
 * Пример кода, приводящего к сбою: dfa_unique_fast(array(1, 2, 2, 3, null))
 * В то же время, несмотря на E_WARNING, метод всё-таки возвращает результат,
 * правда, без недопустимых значений:
 * при подавлении E_WARNING dfa_unique_fast(array(1, 2, 2, 3, null)) вернёт:
 * array(1, 2, 3).
 * Более того, даже если сбойный элемент содержится в середине исходного массива,
 * то результат при подавлении сбоя E_WARNING будет корректным (без недопустимых элементов):
 * dfa_unique_fast(array(1, 2, null,  2, 3)) вернёт тот же результат array(1, 2, 3).
 * http://3v4l.org/uvJoI
 * По этой причине добавил оператор @ перед @uses array_flip()
 * @param array(int|string => int|string) $array
 * @return array(int|string => int|string)
 */
function dfa_unique_fast(array $array) {
	/** @noinspection PhpUsageOfSilenceOperatorInspection */
	return array_keys(@array_flip($array));
}

/**
 * Алгоритм взят отсюда:
 * http://php.net/manual/function.array-unshift.php#106570
 * @param array(string => mixed) $array
 * @param string $key
 * @param mixed $value
 */
function dfa_unshift_assoc(&$array, $key, $value)  {
	$array = array_reverse($array, $preserve_keys = true);
	$array[$key] = $value;
	$array = array_reverse($array, $preserve_keys = true);
}