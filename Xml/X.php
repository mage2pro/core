<?php
namespace Df\Xml;
use Df\Core\Text\Marker;
use Magento\Framework\Simplexml\Element as MX;
use \SimpleXMLElement as CX;
use \Throwable as Th; # 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
final class X extends MX {
	/** */
	function __destruct() {unset(self::$_canonicalArray[spl_object_hash($this)]);}

	/**
	 * 2021-12-13
	 * https://www.php.net/manual/en/simplexmlelement.addattribute.php
	 * https://stackoverflow.com/a/9391673
	 * https://stackoverflow.com/a/43566078
	 * 2022-11-15 We can not declare the $k argument type with PHP < 8: https://3v4l.org/ptpUM
	 * 2022-12-20 We can not declare arguments types because they are undeclared in the overriden method.
	 * @override
	 * @see \SimpleXMLElement::addAttribute()
	 * @used-by self::addAttributes()
	 * @used-by self::addChildX()
	 * @param string $k
	 * @param string|null $v [optional]
	 * @param string|null $ns [optional]
	 */
	function addAttribute($k, $v = null, $ns = null):void {parent::addAttribute($this->k($k), $v, $ns);}

	/**
	 * @used-by df_xml_g()
	 * @used-by df_xml_node()
	 * @used-by self::importArray()
	 * @param array(string => string) $atts
	 */
	function addAttributes(array $atts):void {
		foreach ($atts as $k => $v) {/** @var string $k */ /** @var mixed $v */
			df_assert_sne($k);
			# убрал strval($v) для ускорения системы
			if (is_object($v) || is_array($v)) {
				df_log($atts);
				df_error(
					"Значение поля «{$k}» должно быть строкой, однако является %s."
					,is_object($v) ? sprintf('объектом класса %s', get_class($v)) : 'массивом'
				);
			}
			$this->addAttribute($k, $v);
		}
	}

	/**
	 * 2022-11-15
	 * 1) `static` as a return type is not supported by PHP < 8: https://github.com/mage2pro/core/issues/168#user-content-static
	 * 2) We can not declare the $name argument type with PHP < 8: https://3v4l.org/ptpUM
	 * @override
	 * @see \SimpleXMLElement::addChild()
	 * @used-by self::addChildText()
	 * @used-by self::addChildX()
	 * @used-by self::importArray()
	 * @used-by self::importString()
	 * @param string $name
	 * @param string|null $value [optional]
	 * @param string|null $namespace [optional]
	 * @return CX
	 */
	#[\ReturnTypeWillChange]
	function addChild($name, $value = null, $namespace = null):CX {/** @var CX $r */
		try {$r = parent::addChild($this->k($name), $value, $namespace);}
		catch (Th $th) {df_error("Tag <{$name}>. Value: «{$value}». Error: «%s».", df_xts($th));}
		return $r;
	}

	/**
	 * Отличия от родительского метода:
	 * 		1) гарантия, что результат — массив
	 * 		2) кэширование результата
	 * 2022-12-05: We do not need to check that the result is an array: https://3v4l.org/pBUvg
	 * @override
	 * @see \Magento\Framework\Simplexml\Element::asCanonicalArray()
	 * @return array(string => mixed)
	 */
	function asCanonicalArray():array {
		$_this = spl_object_hash($this); /** @var string $_this */
		if (!isset(self::$_canonicalArray[$_this])) {
			/**
			 * @uses \Magento\Framework\Simplexml\Element::asCanonicalArray()
			 * может возвращать строку в случае,
			 * когда структура исходных данных не соответствует массиву.
			 */
			self::$_canonicalArray[$_this] = parent::asCanonicalArray();
		}
		return self::$_canonicalArray[$_this];
	}

	/**
	 * 2016-09-01 Родительский метод задаёт вложенность тремя пробелами, а я предпочитаю символ табуляции.
	 * 2022-12-20 We can not declare arguments types because they are undeclared in the overriden method.
	 * @override
	 * @see \Magento\Framework\Simplexml\Element::asNiceXml()
	 * @used-by df_xml_g()
	 * @used-by df_xml_prettify()
	 * @used-by df_xml_report()
	 * @used-by self::asNiceXml()
	 * @param string $filename [optional]
	 * @param int $level  [optional]
	 */
	function asNiceXml($filename = '', $level = 0):string {
		if (is_numeric($level)) {
			$pad = str_pad('', $level * 1, "\t", STR_PAD_LEFT);
			$nl = "\n";
		} 
		else {
			$pad = '';
			$nl = '';
		}
		$r = $pad . '<' . $this->getName(); /** @var string $r */
		$attributes = $this->attributes();
		if ($attributes) {
			foreach ($attributes as $key => $value) {
				$r .= ' ' . $key . '="' . str_replace('"', '\"', (string)$value) . '"';
			}
		}
		$attributes = $this->attributes('xsi', true);
		if ($attributes) {
			foreach ($attributes as $key => $value) {
				$r .= ' xsi:' . $key . '="' . str_replace('"', '\"', (string)$value) . '"';
			}
		}
		if ($this->hasChildren()) {
			$r .= '>';
			$value = trim((string)$this);
			if (strlen($value)) {
				/**
				 * 2021-12-16
				 * The previous code was: `$this->xmlentities($value)`
				 * @see \Magento\Framework\Simplexml\Element::xmlentities()
				 */
				$r .= df_cdata_raw_if_needed($value);
			}
			$r .= $nl;
			foreach ($this->children() as $child) {/** @var X $child */
				$r .= $child->asNiceXml('', is_numeric($level) ? $level + 1 : true);
			}
			$r .= $pad . '</' . $this->getName() . '>' . $nl;
		}
		else {
			$value = (string)$this;
			if (strlen($value)) {
				/**
				 * 2021-12-16
				 * The previous code was: `$this->xmlentities($value)`
				 * @see \Magento\Framework\Simplexml\Element::xmlentities()
				 */
				$r .= '>' . df_cdata_raw_if_needed($value) . '</' . $this->getName() . '>' . $nl;
			}
			else {
				$r .= '/>' . $nl;
			}
		}
		if ((0 === $level || false === $level) && !empty($filename)) {
			file_put_contents($filename, $r);
		}
		return $r;
	}

	/**
	 * 2015-02-27
	 * Возвращает документ XML в виде текста без заголовка XML.
	 * Раньше алгоритм был таким:
	 * 		str_replace('<?xml version="1.0"?>', '', $this->asXML());
	 * Однако этот алгоритм неверен: ведь в заголовке XML может присутствовать указание кодировки, например:
	 * 		<?xml version='1.0' encoding='utf-8'?>
	 * Новый алгоритм взят отсюда: http://stackoverflow.com/a/5947858
	 * @used-by df_xml_g()
	 */
	function asXMLPart():string {
		$dom = dom_import_simplexml($this); /** @var \DOMElement $dom */
		/**
		 * 2021-12-13
		 * @uses \DOMDocument::saveXML() can return `false`:
		 * https://www.php.net/manual/domdocument.savexml.php#refsect1-domdocument.savexml-returnvalues
		 */
		return df_assert_nef($dom->ownerDocument->saveXML($dom->ownerDocument->documentElement));
	}
	
	/**
	 * 1) Этот метод отличается от родительского только возвращением null вместо false в случае отсутствия значения.
	 * Мы можем так делать, потому что родительский класс сам внутри себя не использует метод descend
	 * (и, соответственно, не полагается на возвращение значения false).
	 * 2) Интерпретатор PHP не разрешает присваивать полям объектов класса CX (и его наследников) значения сложных типов.
	 * Такое присваивание приводит к сбою: «Warning: It is not yet possible to assign complex types to attributes».
	 * По этой причине не используем кэширование результата.
	 * 3) В комментарии к свойству @see \Magento\Framework\Simplexml\Element::$_parent
	 * дана рекомендация использования функции @see spl_object_hash(), однако это слишком сложно,
	 * и неочевидно, ускорит ли работу системы (также могут быть проблемы с расходом оперативной памяти).
	 * 2022-11-15 @deprecated It is unused.
	 * 2022-11-26 We can not declare the $path argument type because it is undeclared in the overriden method.
	 * @override
	 * @see \Magento\Framework\Simplexml\Element::descend()
	 * @param string|string[] $p
	 * @return X|null
	 */
	function descend($p) {return df_ftn(parent::descend($p));}

	/**
	 * @used-by df_xml_g()
	 * @used-by df_xml_node()
	 * @used-by self::importArray()
	 * @param array(string => mixed) $array
	 * @param string[]|bool $wrapInCData [optional]
	 */
	function importArray(array $array, $wrapInCData = []):X {
		foreach ($array as $key => $v) { /** @var string $key */ /** @var mixed $v */
			if ($v instanceof X) {
				/**
				 * 2016-08-31
				 * Случай, который отсутствовал в Российской сборке Magento:
				 *	'Payment' => [
				 *		df_xml_node('TxnList', ['count' => 1], [
				 *			df_xml_node('Txn', ['ID' => 1], [
				 *				'amount' => 200
				 *				,'purchaseOrderNo' => 'test'
				 *				,'txnID' => '009887'
				 *				,'txnSource' => 23
				 *				,'txnType' => 4
				 *			])
				 *		])
				 *	]
				 *	<Payment>
				 *		<TxnList count="1">
				 *			<Txn ID="1">
				 *				<txnType>4</txnType>
				 *				<txnSource>23</txnSource>
				 *				<amount>200</amount>
				 *				<purchaseOrderNo>test</purchaseOrderNo>
				 *				<txnID>009887</txnID>
				 *			</Txn>
				 *		</TxnList>
				 *	</Payment>
				 */
				$this->addChildX($v);
			}
			elseif (!is_array($v)) {
				$this->importString($key, $v, $wrapInCData);
			}
			elseif (df_is_assoc($v) || array_filter($v, function($i) {return $i instanceof X;})) {
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
				$childData = $v; /** @var array|null $childData */
				# Данный программный код позволяет импортировать атрибуты тэгов
				/** @var array(string => string)|null $attributes $attributes */
				$attributes = dfa($v, self::ATTR);
				if (!is_null($attributes)) {
					$childNode->addAttributes(df_assert_array($attributes));
					# Если $v содержит атрибуты,
					# то дочерние значения должны содержаться не непосредственно в $v, а в подмассиве с ключём self::CONTENT.
					$childData = dfa($v, self::CONTENT);
				}
				if (!is_null($childData)) {
					/**
					 * $childData запросто может не быть массивом.
					 * Например, в такой ситуации:
					 *	(
					 *		[_attributes] => Array
					 *			(
					 *				[Код] => 796
					 *				[НаименованиеПолное] => Штука
					 *				[МеждународноеСокращение] => PCE
					 *			)
					 *		[_value] => шт
					 *	)
					 * Здесь $childData — это «шт».
					 */
					if (is_array($childData)) {
						$childNode->importArray($childData, $wrapInCData);
					}
					else {
						# '' означает, что метод importString() не должен создавать дочерний тэг $key,
						# а должен добавить текст в качестве единственного содержимого текущего тэга.
						$childNode->importString('', $childData, $wrapInCData);
					}
				}
			}
			else {
				# Данный код позволяет импортировать структуры с повторяющимися тегами.
				# Например, нам надо сформировать такой документ:
				#	<АдресРегистрации>
				#		<АдресноеПоле>
				#			<Тип>Почтовый индекс</Тип>
				#			<Значение>127238</Значение>
				#		</АдресноеПоле>
				#		<АдресноеПоле>
				#			<Тип>Улица</Тип>
				#			<Значение>Красная Площадь</Значение>
				#		</АдресноеПоле>
				#	</АдресРегистрации>
				#
				# Для этого мы вызываем:
				#
				#	$this->getDocument()
				#		->importArray(
				#			array(
				#				'АдресРегистрации' =>
				#					array(
				#						'АдресноеПоле' =>
				#							array(
				#								array(
				#									'Тип' => 'Почтовый индекс'
				#									,'Значение' => '127238'
				#								)
				#								,array(
				#									'Тип' => 'Улица'
				#									,'Значение' => 'Красная Площадь'
				#								)
				#							)
				#					)
				#			)
				#		)
				#	;
				foreach ($v as $vItem) {
					/** @var array(string => mixed)|string $vItem */
					/**
					 * 2015-01-20
					 * Обратите внимание, что $vItem может быть не только массивом, но и строкой.
					 * Например, такая структура:
					 *	<Группы>
					 *		<Ид>1</Ид>
					 *		<Ид>1</Ид>
					 *		<Ид>1</Ид>
					 *	</Группы>
					 * кодируется так:
					 * array('Группы' => array('Ид' => array(1, 2, 3)))
					 */
					$this->importArray([$key => $vItem], $wrapInCData);
				}
			}
		}
		return $this;
	}

	/**
	 * 2015-08-08
	 * Преобразует структуру вида:
	 *	<СтавкиНалогов>
	 *		<СтавкаНалога>
	 *			<Наименование>НДС</Наименование>
	 *			<Ставка>10</Ставка>
	 *		</СтавкаНалога>
	 *	</СтавкиНалогов>
	 * в массив array('НДС' => '10')
	 * 2022-10-25 @deprecated It is unused.
	 * @see self::xpathMap()
	 * @return array(string => string)
	 */
	function map(string $path, string $keyName, string $valueName):array {
		$r = []; /** @var array(string => string) $r */
		$nodes = $this->xpathA($path); /** @var X[] $nodes */
		foreach ($nodes as $node) {/** @var X $node */
			$r[df_leaf_sne($node->{$keyName})] = df_leaf_s($node->{$valueName});
		}
		return $r;
	}

	/**
	 * 2022-11-15 @deprecated It is unused.
	 * @override
	 * @see \SimpleXMLElement::xpath()
	 * @param string|string[] $p
	 * @return X[]
	 */
	#[\ReturnTypeWillChange]
	function xpath($p):array {
		if (1 < func_num_args()) {
			$p = df_cc_path(func_get_args());
		}
		elseif (is_array($p)) {
			$p = df_cc_path($p);
		}
		return parent::xpath(df_param_sne($p, 0));
	}

	/**
	 * 2022-12-05: We do not need to check that the result is an array: https://3v4l.org/pBUvg
	 * @used-by self::map()
	 * @used-by self::xpathMap()
	 * @param string|string[] $path
	 * @return X[]
	 */
	function xpathA($path):array {
		if (1 < func_num_args()) {
			$path = df_cc_path(func_get_args());
		}
		elseif (is_array($path)) {
			$path = df_cc_path($path);
		}
		df_param_sne($path, 0);
		return parent::xpath($path);
	}

	/**
	 * 2015-08-08
	 * Преобразует структуру вида:
	 *	<СтавкиНалогов>
	 *		<СтавкаНалога>
	 *			<Наименование>НДС</Наименование>
	 *			<Ставка>10</Ставка>
	 *		</СтавкаНалога>
	 *	</СтавкиНалогов>
	 * в массив array('НДС' => '10')
	 * 2022-10-25 @deprecated It is unused.
	 * @see self::map()
	 * @return array(string => string)
	 */
	function xpathMap(string $path, string $keyName, string $valueName):array {
		$r = []; /** @var array(string => string) $r */
		$nodes = $this->xpathA($path); /** @var X[] $nodes */
		foreach ($nodes as $node) { /** @var X $node */
			$r[df_leaf_sne($node->{$keyName})] = df_leaf_s($node->{$valueName});
		}
		return $r;
	}

	/** @used-by self::importString() */
	private function addChildText(string $tag, string $valueAsText):void {
		$r = $this->addChild($tag); /** @var X $r */
		/**
		 * @uses CX::addChild() создаёт и возвращает не просто CX, как говорит документация, а объект класса родителя.
		 * Поэтому в нашем случае addChild создаст объект E.
		 */
		$r->cdata($valueAsText);
	}

	/**
	 * 2016-08-31 http://stackoverflow.com/a/11727581
	 * @used-by self::addChildX()
	 * @used-by self::importArray()
	 */
	private function addChildX(X $child):void {
		$childInThis = $this->addChild($child->getName(), (string)$child); /** @var X $childInThis */
		foreach ($child->attributes() as $attr => $v) { /** @var string $name */ /** @var string $v */
			$childInThis->addAttribute($attr, $v);
		}
		foreach ($child->children() as $childChild) { /** @var X $childChild */
			$childInThis->addChildX($childChild);
		}
	}

	/**
	 * http://stackoverflow.com/a/6260295
	 * @used-by self::addChildText()
	 * @used-by self::importString()
	 */
	private function cdata(string $s):void {
		$e = dom_import_simplexml($this); /** @var \DOMElement $e */
		$e->appendChild($e->ownerDocument->createCDATASection($s));
	}

	/**
	 * @used-by self::importArray()
	 * @param mixed $v
	 * @param string[]|bool $wrapInCData [optional]
	 */
	private function importString(string $k, $v, $wrapInCData = []):void {
		$needWrapInCData = !is_array($wrapInCData) && !!$wrapInCData; /** @var bool $needWrapInCData */
		$wrapInCData = df_eta($wrapInCData);
		# '' означает, что метод `importString` не должен создавать дочерний тэг `$k`,
		# а должен добавить текст в качестве единственного содержимого текущего тэга.
		$kIsEmpty = df_es($k); /** @var bool $kIsEmpty */
		$kAsString = $kIsEmpty ? $this->getName() : $k; /** @var string $kAsString */
		$vIsString = is_string($v); /** @var bool $vIsString */
		$vAsString = ''; /** @var string $vAsString */
		try {$vAsString = $vIsString ? $v : df_string($v);}
		catch (E $e) {df_error("Unable to convert the value of the key «{$kAsString}» to a string.\n%s", df_xts($e));}
		if ($vIsString && $vAsString) {
			/**
			 * Поддержка синтаксиса
			 *	 [
			 *		'Представление' =>
			 *			df_cdata($this->getAddress()->format(Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_TEXT))
			 *	 ]
			 * Обратите внимание, что проверка на синтаксис[[]] должна предшествовать
			 * проверке на принадлежность ключа $kAsString в массиве $wrapInCData,
			 * потому что при соответствии синтаксису[[]] нам надо удалить из значения символы[[]].
			 * Обратите внимание, что нам нужно выполнить проверку на синтаксис df_cdata ([[]])
			 * даже при $needWrapInCData = true, потому что маркеры [[ и ]] из данных надо удалять.
			 */
			if (self::marker()->marked($vAsString)) {
				$vAsString = self::marker()->unmark($vAsString);
				$needWrapInCData = true;
			}
			$needWrapInCData = $needWrapInCData || in_array($kAsString, $wrapInCData) || df_needs_cdata($vAsString);
		}
		$needWrapInCData
			? ($kIsEmpty ? $this->cdata($vAsString) : $this->addChildText($kAsString, $vAsString))
			: (
				$kIsEmpty
				? $this->setValue($vAsString)
				: $this->addChild(
					$kAsString
					/**
					 * Обратите внимание, что мы намеренно не добавляем htmlspecialchars:
					 * пусть вместо этого источник данных помечает те даннные, которые
					 * могут содержать неразрешённые в качестве содержимого тегов XML
					 * значения посредством @see df_cdata()
					 */
					,$vAsString
				)
			)
		;
	}

	/**
	 * 2021-12-16
	 * https://stackoverflow.com/a/9391673
	 * https://stackoverflow.com/a/43566078
	 * https://stackoverflow.com/a/6928183
	 * @used-by self::addAttribute()
	 * @used-by self::addChild()
	 */
	private function k(string $s):string {return !df_contains($s, ':') ? $s : "xmlns:$s";}

	/**
	 * http://stackoverflow.com/a/3153704
	 * @used-by self::importString()
	 * @param mixed $v
	 */
	private function setValue($v):self {$this[0] = $v; return $v;}

	/** @used-by self::importArray() */
	const ATTR = '_attr';

	/** @used-by self::importArray() */
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
	 *	<url>
	 *		<demo>http://fortis.magento-demo.ru/default/</demo>
	 *		<demo>http://fortis.magento-demo.ru/second/</demo>
	 *		<demo>http://fortis.magento-demo.ru/third/</demo>
	 *		<demo>http://fortis.magento-demo.ru/fourth/</demo>
	 *		<demo>http://fortis.magento-demo.ru/fifth/</demo>
	 *		<demo_images_base>http://fortis.infortis-themes.com/demo/</demo_images_base>
	 *		<forum>http://magento-forum.ru/forum/350/</forum>
	 *		<official_site>http://themeforest.net/item/fortis-responsive-magento-theme/1744309?ref=dfediuk</official_site>
	 *	</url>
	 * будет сконвертировано в такой массив:
	 *	[url] => Array
	 *	 (
	 *		 [demo] => http://fortis.magento-demo.ru/fifth/
	 *		 [demo_images_base] => http://fortis.infortis-themes.com/demo/
	 *		 [forum] => http://magento-forum.ru/forum/350/
	 *		 [official_site] => http://themeforest.net/item/fortis-responsive-magento-theme/1744309?ref=dfediuk
	 *	 )
	 * Обратите внимание, что содержимым ключа «demo» массива
	 * стало содержимое последнего (по порядку следования) дочернего узла исходного дерева XML:
	 *	 <demo>http://fortis.magento-demo.ru/fifth/</demo>
	 *
	 * Наш метод @see asMultiArray()
	 * при наличии в исходном дереве XML нескольких одноимённых дочерних узлов
	 * добавляет их все в массив, создавая подмассив:
	 *	[url] => Array
	 *	 (
	 *		 [demo] => Array
	 *		  (
	 *			[0] => http://fortis.magento-demo.ru/default/
	 *			[1] => http://fortis.magento-demo.ru/second/
	 *			[2] => http://fortis.magento-demo.ru/third/
	 *			[3] => http://fortis.magento-demo.ru/fourth/
	 *			[4] => http://fortis.magento-demo.ru/fifth/
	 *		  )
	 *		 [demo_images_base] => http://fortis.infortis-themes.com/demo/
	 *		 [forum] => http://magento-forum.ru/forum/350/
	 *		 [official_site] => http://themeforest.net/item/fortis-responsive-magento-theme/1744309?ref=dfediuk
	 *	 )
	 *
	 * @return array(string => string|array)
	 */
	static function asMultiArray(MX $e, bool $isCanonical = true):array {
		$r = []; /** @var array(string => string|array) $r */
		if (!$e->hasChildren()) {
			/** Просто повторяем алгоритм метода @see \Magento\Framework\Simplexml\Element::_asArray() */
			$r = $e->_asArray($isCanonical);
		}
		elseif (!$isCanonical) {
			/** Просто повторяем алгоритм метода @see \Magento\Framework\Simplexml\Element::_asArray() */
			foreach ($e->attributes() as $attributeName => $attribute) {
				/** @var string $attributeName */
				/** @var MX $attribute */
				if ($attribute) {
					$r['@'][$attributeName] = (string)$attribute;
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
				/** @var array(string => string|array) $childAsArray */
				$childAsArray = self::asMultiArray($child, $isCanonical);
				if (!isset($r[$childName])) {
					/**
					 * Просто повторяем алгоритм метода
					 * @see \Magento\Framework\Simplexml\Element::_asArray()
					 */
					$r[$childName] = $childAsArray;
				}
				else {
					# у нас уже есть дочерний узел с данным именем
					if (!is_array($r[$childName])) {
						# преобразуем узел в массив
						$r[$childName] = [$r[$childName]];
					}
					$r[$childName][] = $childAsArray;
				}
			}
		}
		return $r;
	}

	/**
	 * Убрал df_param_s и df_result_s для ускорения работы модуля Яндекс.Маркет
	 * @used-by df_cdata()
	 */
	static function markAsCData(string $s):string {return self::marker()->mark($s);}

	/**
	 * 2021-12-12
	 * @used-by self::importString()
	 * @used-by self::markAsCData()
	 */
	private static function marker():Marker {static $r; return $r ?: $r = new Marker('[[', ']]');}

	/**
	 * @used-by self::__destruct()
	 * @used-by self::asCanonicalArray()
	 * @var array(string => array(string => mixed))
	 */
	private static $_canonicalArray = [];
}