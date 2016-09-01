<?php
namespace Df\Xml;
use \Exception as E;
use \SimpleXMLElement as CX;
use Magento\Framework\Simplexml\Element as MX;
class X extends MX {
	/** @return void */
	public function __destruct() {unset(self::$_canonicalArray[spl_object_hash($this)]);}

	/**
	 * @param array(string => string) $attributes
	 * @return $this
	 */
	public function addAttributes(array $attributes) {
		foreach ($attributes as $name => $value) {
			/** @var string $name */
			/** @var mixed $value */
			df_assert_string($name);
			// убрал strval($value) для ускорения системы
			if (is_object($value) || is_array($value)) {
				df_log($attributes);
				df_error(
					'Значение поля «%s» должно быть строкой, однако является %s.'
					, $name
					, is_object($value) ? sprintf('объектом класса %s', get_class($value)) : 'массивом'
				);
			}
			$this->addAttribute($name, $value);
		}
		return $this;
	}

	/**
	 * @param string $name
	 * @param string|null $value [optional]
	 * @param string|null $namespace [optional]
	 * @return CX
	 * @throws E
	 */
	public function addChild($name, $value = null, $namespace = null) {
		/** @var CX $result */
		try {
			$result = parent::addChild($name, $value, $namespace);
		}
		catch (E $e) {
			df_error(
				'При назначении тэгу «%s» значения «%s» произошёл сбой: «%s».'
				, $name, $value, df_ets($e)
			);
		}
		return $result;
	}

	/**
	 * 2016-08-31
	 * http://stackoverflow.com/a/11727581
	 * @param X $child
	 * @return void
	 */
	public function addChildX(X $child) {
		/** @var X $childInThis */
		$childInThis = $this->addChild($child->getName(), (string)$child);
		foreach ($child->attributes() as $attr => $value) {
			/** @var string $name */
			/** @var string $value */
			$childInThis->addAttribute($attr, $value);
		}
		foreach ($child->children() as $childChild) {
			/** @var X $childChild */
			$childInThis->addChildX($childChild);
		}
	}

	/**
	 * @param string $tagName
	 * @param string $valueAsText
	 * @return X
	 */
	public function addChildText($tagName, $valueAsText) {
		/** @var X $result */
		$result = $this->addChild($tagName);
		/**
		 * Обратите внимание, что
		 * CX::addChild создаёт и возвращает не просто CX,
		 * как говорит документация, а объект класса родителя.
		 * Поэтому в нашем случае addChild создаст объект E.
		 */
		$result->setCData($valueAsText);
		return $result;
	}

	/**
	 * Отличия от родительского метода:
	 * 1) гарантия, что результат — массив
	 * 2) кэширование результата
	 * @override
	 * @return array(string => mixed)
	 */
	public function asCanonicalArray() {
		/** @var string $_this */
		$_this = spl_object_hash($this);
		if (!isset(self::$_canonicalArray[$_this])) {
			self::$_canonicalArray[$_this] = parent::asCanonicalArray();
			/**
			 * @uses \Magento\Framework\Simplexml\Element::asCanonicalArray()
			 * может возвращать строку в случае,
			 * когда структура исходных данных не соответствует массиву.
			 */
			df_result_array(self::$_canonicalArray[$_this]);
		}
		return self::$_canonicalArray[$_this];
	}

	/**
	 * 2016-09-01
	 * @override
	 * @see \Magento\Framework\Simplexml\Element::asNiceXml()
	 * Родительсктй метод задаёт вложенность тремя пробелами,
	 * а я предпочитаю символ табуляции.
	 * @param string $filename [optional]
	 * @param int $level  [optional]
	 * @return string
	 */
	public function asNiceXml($filename = '', $level = 0) {
		if (is_numeric($level)) {
			$pad = str_pad('', $level * 1, "\t", STR_PAD_LEFT);
			$nl = "\n";
		} else {
			$pad = '';
			$nl = '';
		}
		$out = $pad . '<' . $this->getName();
		$attributes = $this->attributes();
		if ($attributes) {
			foreach ($attributes as $key => $value) {
				$out .= ' ' . $key . '="' . str_replace('"', '\"', (string)$value) . '"';
			}
		}
		$attributes = $this->attributes('xsi', true);
		if ($attributes) {
			foreach ($attributes as $key => $value) {
				$out .= ' xsi:' . $key . '="' . str_replace('"', '\"', (string)$value) . '"';
			}
		}
		if ($this->hasChildren()) {
			$out .= '>';
			$value = trim((string)$this);
			if (strlen($value)) {
				$out .= $this->xmlentities($value);
			}
			$out .= $nl;
			foreach ($this->children() as $child) {
				/** @var X $child */
				$out .= $child->asNiceXml('', is_numeric($level) ? $level + 1 : true);
			}
			$out .= $pad . '</' . $this->getName() . '>' . $nl;
		}
		else {
			$value = (string)$this;
			if (strlen($value)) {
				$out .= '>' . $this->xmlentities($value) . '</' . $this->getName() . '>' . $nl;
			}
			else {
				$out .= '/>' . $nl;
			}
		}
		if ((0 === $level || false === $level) && !empty($filename)) {
			file_put_contents($filename, $out);
		}
		return $out;
	}

	/**
	 * 2015-02-27
	 * Возвращает документ XML в виде текста без заголовка XML.
	 * Раньше алгоритм был таким:
		str_replace('<?xml version="1.0"?>', '', $this->asXML());
	 * Однако этот алгоритм неверен:
	 * ведь в заголовке XML может присутствовать указание кодировки, например:
	 * <?xml version='1.0' encoding='utf-8'?>
	 * Новый алгоритм взят отсюда: http://stackoverflow.com/a/5947858
	 * @return string
	 */
	public function asXMLPart() {
		/** @var \DOMElement $dom */
		$dom = dom_import_simplexml($this);
		return $dom->ownerDocument->saveXML($dom->ownerDocument->documentElement);
	}

	/**
	 * 2015-08-15
	 * @param string $name
	 * @param bool $required [optional]
	 * @return X|null
	 */
	public function child($name, $required = false) {return df_xml_child($this, $name, $required);}

	/** @return string[] */
	public function childrenNames() {
		/** @var string $result */
		$result = [];
		if ($this->children()) {
			foreach ($this->children() as $name => $value) {
				/** @var string $name */
				/** @var X $value */
				$result[]= $name;
			}
		}
		return $result;
	}	
	
	/**
	 * Этот метод отличается от родительского
	 * только возвращением null вместо false в случае отсутствия значения.
	 *
	 * Обратите внимание, что мы можем так делать,
	 * потому что родительский класс сам внутри себя не использует метод descend
	 * (и, соответственно, не полагается на возвращение значения false).
	 *
	 * Обратите внимание, что интерпретатор PHP не разрешает
	 * присваивать полям объектов класса CX (и его наследников)
	 * значения сложных типов.
	 * Такое присваивание приводит к сбою:
	 * «Warning: It is not yet possible to assign complex types to attributes».
	 *
	 * По этой причине не используем кэширование результата.
	 *
	 * в комментарии к свойству @see \Magento\Framework\Simplexml\Element::$_parent
	 * дана рекомендация использования функции @see spl_object_hash(),
	 * однако это слишком сложно и необчевидно, ускорит ли работу системы
	 * (также могут быть проблемы с расходом оперативной памяти).
	 *
	 * @override
	 * @param string|string[] $path
	 * @return X|null
	 */
	public function descend($path) {return df_ftn(parent::descend($path));}

	/**
	 * @param string|string[] $path
	 * @return X
	 */
	public function descendO($path) {
		$result = $this->descend($path);
		df_assert($result instanceof X);
		return $result;
	}

	/**
	 * @param array(string => mixed) $array
	 * @param string[]|bool $wrapInCData [optional]
	 * @return X
	 */
	public function importArray(array $array, $wrapInCData = []) {
		foreach ($array as $key => $value) {
			/** @var string $key */
			/** @var mixed $value */
			if ($value instanceof X) {
				/**
				 * 2016-08-31
				 * Случай, который отсутствовал в Российсеой сборке Magento:
					'Payment' => [
						df_xml_node('TxnList', ['count' => 1], [
							df_xml_node('Txn', ['ID' => 1], [
								'amount' => 200
								,'purchaseOrderNo' => 'test'
								,'txnID' => '009887'
								,'txnSource' => 23
								,'txnType' => 4
							])
						])
					]
				 *
					<Payment>
						<TxnList count="1">
							<Txn ID="1">
								<txnType>4</txnType>
								<txnSource>23</txnSource>
								<amount>200</amount>
								<purchaseOrderNo>test</purchaseOrderNo>
								<txnID>009887</txnID>
							</Txn>
						</TxnList>
					</Payment>
				 */
				$this->addChildX($value);
			}
			else if (!is_array($value)) {
				$this->importString($key, $value, $wrapInCData);
			}
			else if (
				df_is_assoc($value)
				|| array_filter($value, function($i) {return $i instanceof X;}))
			{
				/** @var X $childNode */
				$childNode =
					$this->addChild(
						/**
						 * Раньше тут стояло df_string($key)
						 * Для ускорения модуля Яндекс.Маркет @see df_string() убрал.
						 * Вроде ничего не ломается.
						 */
						$key
					)
				;
				/** @var array|null $childData */
				$childData = $value;
				// Данный программный код позволяет импортировать атрибуты тэгов
				/** @var array(string => string)|null $attributes $attributes */
				$attributes = dfa($value, self::ATTR);
				if (!is_null($attributes)) {
					df_assert_array($attributes);
					$childNode->addAttributes($attributes);
					/**
					 * Если $value содержит атрибуты,
					 * то дочерние значения должны содержаться
					 * не непосредственно в $value, а в подмассиве с ключём self::CONTENT
					 */
					$childData = dfa($value, self::CONTENT);
				}
				if (!is_null($childData)) {
					/**
					 * $childData запросто может не быть массивом.
					 * Например, в такой ситуации:
						(
							[_attributes] => Array
								(
									[Код] => 796
									[НаименованиеПолное] => Штука
									[МеждународноеСокращение] => PCE
								)
							[_value] => шт
						)
					 * Здесь $childData — это «шт».
					 */
					if (is_array($childData)) {
						$childNode->importArray($childData, $wrapInCData);
					}
					else {
						$childNode->importString(
							/**
							 * null означает, что метод @uses importString()
							 * не должен создавать дочерний тэг $key,
							 * а должен добавить текст
							 * в качестве единственного содержимого текущего тэга
							 */
							$key = null
							,$childData
							,$wrapInCData
						);
					}
				}
			}
			else {
				/**
				 * Данный код позволяет импортировать структуры с повторяющимися тегами.
				 * Например, нам надо сформировать такой документ:
					<АдресРегистрации>
						<АдресноеПоле>
							<Тип>Почтовый индекс</Тип>
							<Значение>127238</Значение>
						</АдресноеПоле>
						<АдресноеПоле>
							<Тип>Улица</Тип>
							<Значение>Красная Площадь</Значение>
						</АдресноеПоле>
					</АдресРегистрации>
				 *
				 * Для этого мы вызываем:
				 *
					$this->getDocument()
						->importArray(
							array(
				 				'АдресРегистрации' =>
									array(
										'АдресноеПоле' =>
											array(
												array(
													'Тип' => 'Почтовый индекс'
													,'Значение' => '127238'
												)
												,array(
													'Тип' => 'Улица'
													,'Значение' => 'Красная Площадь'
												)
											)
									)
							)
						)
					;
				 *
				 */
				foreach ($value as $valueItem) {
					/** @var array(string => mixed)|string $valueItem */
					/**
					 * 2015-01-20
					 * Обратите внимание, что $valueItem может быть не только массивом, но и строкой.
					 * Например, такая структура:
						<Группы>
							<Ид>1</Ид>
							<Ид>1</Ид>
							<Ид>1</Ид>
						</Группы>
					 * кодируется так:
					 * array('Группы' => array('Ид' => array(1, 2, 3)))
					 */
					$this->importArray([$key => $valueItem], $wrapInCData);
				}
			}
		}
		return $this;
	}

	/**
	 * 2015-08-16
	 * @param string $child
	 * @return bool
	 */
	public function leafB($child) {return df_leaf_b($this->{$child});}

	/**
	 * 2015-08-15
	 * @used-by Df_1C_Cml2_Import_Data_Collection_ProductPart_AttributeValues_Custom::itemClassAdvanced()
	 * @param string $child
	 * @return string|null
	 */
	public function leaf($child) {return df_leaf($this->{$child});}

	/**
	 * 2015-08-16
	 * @param string $child
	 * @return string
	 */
	public function leafSne($child) {return df_leaf_sne($this->{$child});}

	/**
	 * 2015-08-15
	 * @used-by \Dfr\Translation\Realtime\Dictionary::hasEntry()
	 * @param string $path
	 * @return string[]
	 */
	public function leafs($path) {
		/** @var array(string => string) $result */
		$result = [];
		/** @var X[] $nodes */
		$nodes = $this->xpathA($path);
		foreach ($nodes as $node) {
			/** @var X $node */
			$result[] = df_leaf_s($node);
		}
		return $result;
	}

	/**
	 * 2015-08-08
	 * Преобразует структуру вида:
		<СтавкиНалогов>
			<СтавкаНалога>
				<Наименование>НДС</Наименование>
				<Ставка>10</Ставка>
			</СтавкаНалога>
		</СтавкиНалогов>
	 * в массив array('НДС' => '10')
	 * @used-by Df_1C_Cml2_Import_Data_Entity_Product::getTaxes()
	 * @param string $path
	 * @param string $keyName
	 * @param string $valueName
	 * @return array(string => string)
	 */
	public function map($path, $keyName, $valueName) {
		/** @var array(string => string) $result */
		$result = [];
		/** @var X[] $nodes */
		$nodes = $this->xpathA($path);
		foreach ($nodes as $node) {
			/** @var X $node */
			$result[df_leaf_sne($node->{$keyName})] = df_leaf_s($node->{$valueName});
		}
		return $result;
	}

	/**
	 * http://stackoverflow.com/a/3153704
	 * @param mixed $value
	 * @return $this
	 */
	public function setValue($value) {
		$this->{0} = $value;
		return $this;
	}

	/**
	 * @override
	 * @param string|string[] $path
	 * @return X[]
	 */
	public function xpath($path) {
		if (1 < func_num_args()) {
			$path = df_cc_path(func_get_args());
		}
		else if (is_array($path)) {
			$path = df_cc_path($path);
		}
		df_param_string_not_empty($path, 0);
		return parent::xpath($path);
	}

	/**
	 * @param string|string[] $path
	 * @return X[]
	 */
	public function xpathA($path) {
		if (1 < func_num_args()) {
			$path = df_cc_path(func_get_args());
		}
		else if (is_array($path)) {
			$path = df_cc_path($path);
		}
		df_param_string_not_empty($path, 0);
		/** @var X[] $result */
		$result = parent::xpath($path);
		df_result_array($result);
		return $result;
	}

	/**
	 * 2015-08-08
	 * Преобразует структуру вида:
		<СтавкиНалогов>
			<СтавкаНалога>
				<Наименование>НДС</Наименование>
				<Ставка>10</Ставка>
			</СтавкаНалога>
		</СтавкиНалогов>
	 * в массив array('НДС' => '10')
	 * @used-by Df_1C_Cml2_Import_Data_Entity_Product::getTaxes()
	 * @param string $path
	 * @param string $keyName
	 * @param string $valueName
	 * @return array(string => string)
	 */
	function xpathMap($path, $keyName, $valueName) {
		/** @var array(string => string) $result */
		$result = [];
		/** @var X[] $nodes */
		$nodes = $this->xpathA($path);
		foreach ($nodes as $node) {
			/** @var X $node */
			$result[df_leaf_sne($node->{$keyName})] = df_leaf_s($node->{$valueName});
		}
		return $result;
	}

	/**
	 * @param string|null $key
	 * @param mixed $value
	 * @param string[]|bool $wrapInCData [optional]
	 * @return X
	 */
	private function importString($key, $value, $wrapInCData = []) {
		/** @var bool $wrapInCDataAll */
		$wrapInCDataAll = is_array($wrapInCData) ? false : !!$wrapInCData;
		$wrapInCData = df_nta($wrapInCData);
		/**
		 * null означает, что метод importString
		 * не должен создавать дочерний тэг $key,
		 * а должен добавить текст
		 * в качестве единственного содержимого текущего тэга
		 */
		if (!is_null($key)) {
			df_param_string($key, 0);
		}
		/** @var string $keyAsString */
		$keyAsString =
			is_null($key)
			? $this->getName()
			:
				/**
				 * Раньше тут стояло df_string($key).
				 * Убрал df_string для ускорения модуля Яндекс.Маркет.
				 * Более того, выше стоит проверка df_param_string,
				 * так что если $key не null, то $key гарантированно строка
				 */
				$key
		;
		/**
		 * @var bool $valueIsString
		 */
		$valueIsString = is_string($value);
		/** @var string $valueAsString */
		$valueAsString = null;
		try {
			$valueAsString = $valueIsString ? $value : df_string($value);
		}
		catch (E $e) {
			df_error(
				"Не могу сконвертировать значение ключа «%s» в строку.\n%s"
				, $keyAsString
				, df_ets($e)
			);
		}
		/** @var bool $needWrapInCData */
		$needWrapInCData = $wrapInCDataAll;
		if ($valueIsString && $valueAsString) {
			/**
			 * Поддержка синтаксиса
				 array(
					'Представление' =>
						df_cdata(
							$this->getAddress()->format(
								Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_TEXT
							)
						)
				 )
			 * Обратите внимание, что проверка на синтаксис[[]] должна предшествовать
			 * проверке на принадлежность ключа $keyAsString в массиве $wrapInCData,
			 * потому что при соответствии синтаксису[[]] нам надо удалить из значения символы[[]].
			 * Обратите внимание, что нам нужно выполнить проверку на синтаксис df_cdata ([[]])
			 * даже при $wrapInCDataAll = true, потому что маркеры [[ и ]] из данных надо удалять.
			 */
			/**
			 * Перед вызовом медленной функции @see preg_match
			 * выполняем более быструю и простую проверку @see df_contains
			 */
			if (df_contains($valueAsString, '[[') && df_contains($valueAsString, ']]')) {
				/** @var string $pattern */
				$pattern = "#\[\[([\s\S]*)\]\]#mu";
				/** @var string[] $matches */
				$matches = [];
				if (1 === preg_match($pattern, $valueAsString, $matches)) {
					$valueAsString = $matches[1];
					$needWrapInCData = true;
				}
			}
			$needWrapInCData = $needWrapInCData || in_array($keyAsString, $wrapInCData);
		}
		/** @var X $result */
		$result =
				$needWrapInCData
			?
				(
					is_null($key)
					? $this->setCData($valueAsString)
					: $this->addChildText($keyAsString, $valueAsString)
				)
			:
				(
						is_null($key)
					?
						$this->setValue($valueAsString)
					:
						$this->addChild(
							$keyAsString
							/**
							 * Обратите внимание, что мы намеренно не добавляем htmlspecialchars:
							 * пусть вместо этого источник данных помечает те даннные, которые
							 * могут содержать неразрешённые в качестве содержимого тегов XML
							 * значения посредством @see df_cdata()
							 */
							,$valueAsString
						)
				)
		;
		df_assert($result instanceof X);
		return $result;
	}

	/**
	 * http://stackoverflow.com/a/6260295
	 * @param string $text
	 * @return $this
	 */
	public function setCData($text) {
		/** @var \DOMElement $domElement */
		$domElement = dom_import_simplexml($this);
		$domElement->appendChild($domElement->ownerDocument->createCDATASection($text));
		return $this;
	}

	const ATTR = '_attr';
	const CONTENT = '_content';

	/**
	 * Этот метод разработал сам, но не тестировал,
	 * потому что после разработки только заметил,
	 * что применять его к стандартным файлам XML (@see Mage::getConfig()) всё равно нельзя:
	 * в стандартном мега-файле, возвращаемом Mage::getConfig(),
	 * одноимённые дочерние узлы уже отсутствуют (перетёрты друг другом).
	 *
	 * Отличие от стандартного метода @see asArray():
	 * если дерево XML содержит несколько одноимённых дочерних узлов,
	 * то родительский метод при конвертации дерева XML в массив
	 * перетирает содержимое дочерних узлов друг другом:
	 * @see \Magento\Framework\Simplexml\Element::_asArray():
	 * $result[$childName] = $child->_asArray($isCanonical);
	 * Например, дерево XML
		<url>
			<demo>http://fortis.magento-demo.ru/default/</demo>
			<demo>http://fortis.magento-demo.ru/second/</demo>
			<demo>http://fortis.magento-demo.ru/third/</demo>
			<demo>http://fortis.magento-demo.ru/fourth/</demo>
			<demo>http://fortis.magento-demo.ru/fifth/</demo>
			<demo_images_base>http://fortis.infortis-themes.com/demo/</demo_images_base>
			<forum>http://magento-forum.ru/forum/350/</forum>
			<official_site>http://themeforest.net/item/fortis-responsive-magento-theme/1744309?ref=dfediuk</official_site>
		</url>
	 * будет сконвертировано в такой массив:
		[url] => Array
		 (
			 [demo] => http://fortis.magento-demo.ru/fifth/
			 [demo_images_base] => http://fortis.infortis-themes.com/demo/
			 [forum] => http://magento-forum.ru/forum/350/
			 [official_site] => http://themeforest.net/item/fortis-responsive-magento-theme/1744309?ref=dfediuk
		 )
	 * Обратите внимание, что содержимым ключа «demo» массива
	 * стало содержимое последнего (по порядку следования) дочернего узла исходного дерева XML:
	 	 <demo>http://fortis.magento-demo.ru/fifth/</demo>
	 *
	 * Наш метод @see asMultiArray()
	 * при наличии в исходном дереве XML нескольких одноимённых дочерних узлов
	 * добавляет их все в массив, создавая подмассив:
		[url] => Array
		 (
			 [demo] => Array
			  (
				[0] => http://fortis.magento-demo.ru/default/
				[1] => http://fortis.magento-demo.ru/second/
				[2] => http://fortis.magento-demo.ru/third/
				[3] => http://fortis.magento-demo.ru/fourth/
				[4] => http://fortis.magento-demo.ru/fifth/
	 		  )
			 [demo_images_base] => http://fortis.infortis-themes.com/demo/
			 [forum] => http://magento-forum.ru/forum/350/
			 [official_site] => http://themeforest.net/item/fortis-responsive-magento-theme/1744309?ref=dfediuk
		 )
	 *
	 * @param MX $e
	 * @param bool $isCanonical [optional]
	 * @return array(string => string|array())
	 */
	public static function asMultiArray(MX $e, $isCanonical = true) {
		/** @var array(string => string|array()) $result */
		$result = [];
		if (!$e->hasChildren()) {
			/** Просто повторяем алгоритм метода @see \Magento\Framework\Simplexml\Element::_asArray() */
			$result = $e->_asArray($isCanonical);
		}
		else {
			if (!$isCanonical) {
				/** Просто повторяем алгоритм метода @see \Magento\Framework\Simplexml\Element::_asArray() */
				foreach ($e->attributes() as $attributeName => $attribute) {
					/** @var string $attributeName */
					/** @var MX $attribute */
					if ($attribute) {
						$result['@'][$attributeName] = (string)$attribute;
					}
				}
			}
			else {
				/**
				 * Обратите внимание, что,
				 * в отличие от метода @see \Magento\Framework\Simplexml\Element::_asArray(),
				 * мы не можем использовать синтаксис
				 * foreach ($e->children() as $childName => $child) {
				 * потому что при таком синтаксисе мы не сможем получить доступ
				 * ко всем одноимённым дочерним узлам.
				 */
				foreach ($e->children() as $child) {
					/** @var MX $child */
					/** @var string $childName */
					$childName = $child->getName();
					/** @var array(string => string|array()) $childAsArray */
					$childAsArray = self::asMultiArray($child, $isCanonical);
					if (!isset($result[$childName])) {
						/**
						 * Просто повторяем алгоритм метода
						 * @see \Magento\Framework\Simplexml\Element::_asArray()
						 */
						$result[$childName] = $childAsArray;
					}
					else {
						// у нас уже есть дочерний узел с данным именем
						if (!is_array($result[$childName])) {
							// преобразуем узел в массив
							$result[$childName] = [$result[$childName]];
						}
						$result[$childName][] = $childAsArray;
					}
				}
			}
		}
		return $result;
	}

	/**
	 * Убрал df_param_string и df_result_string для ускорения работы модуля Яндекс.Маркет
	 * @param string|null $text
	 * @return string
	 */
	public static function markAsCData($text) {return '[[' . $text . ']]';}

	/**
	 * @used-by __destruct()
	 * @used-by asCanonicalArray()
	 * @var array(string => array(string => mixed))
	 */
	private static $_canonicalArray = [];
}