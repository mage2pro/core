<?php
use Df\Core\Exception as E;
use Df\Xml\X;
use SimpleXMLElement as CX;
use Magento\Framework\Simplexml\Element as MX;

/**
 * @used-by df_xml_output_html()
 * @used-by df_xml_output_plain()
 * @used-by df_xml_report()
 */
const DF_XML_BEGIN = '{df-xml}';
const DF_XML_END = '{/df-xml}';

/**
 * @param CX $e
 * @return CX
 * @throws E
 */
function df_assert_leaf(CX $e) {return df_check_leaf($e) ? $e : df_error(
	"Требуется лист XML, однако получена ветка XML:\n%s.", df_xml_report($e)
);}

/**
 * @param string $text
 * @return string
 */
function df_cdata($text) {return X::markAsCData($text);}

/**
 * 2015-02-27
 * Обратите внимание,
 * что метод @see \SimpleXMLElement::count() появился только в PHP 5.3,
 * поэтому мы его не используем: http://php.net/manual/simplexmlelement.count.php
 * Также обратите внимание, что count($e->children())
 * некорректно возвращает 1 для листов в PHP 5.1: http://3v4l.org/PT6Pt
 * Однако нам не нужно поддерживать PHP 5.1.
 *
 * Обратите внимание, что для несуществующего узла попытка вызова @uses count()
 * привелёт к сбою: «Warning: count(): Node no longer exists»
 * http://3v4l.org/PsIPe#v512
 *
 * Текущий алгоритм проверен на работоспособность здесь: http://3v4l.org/VldTN
 *
 * 2015-08-16
 *
 * Как ни странно, написанное выше действительно верно: http://3v4l.org/covo1
 *
 * Обратите внимение, что класс @see \SimpleXMLElement не реализует интерфейс @see Iterator,
 * а реализует только интерфейс @see Traversable.
 * http://php.net/manual/class.iterator.php
 * http://php.net/manual/class.traversable.php
 * http://php.net/manual/en/simplexmlelement.count.php
 * Однако @uses count() почему-то работает для него.
 * @see \SimpleXMLElement — самый загадочный класс PHP.
 *
 * 2015-08-15
 * Нельзя здесь использовать count($e->children()),
 * потому что класс @see SimpleXmlElement не реализует интерфейс @see Iterator,
 * а реализует только интерфейс @see Traversable.
 * http://php.net/manual/class.iterator.php
 * http://php.net/manual/class.traversable.php
 * http://php.net/manual/en/simplexmlelement.count.php
 *
 * @param CX $e
 * @return bool
 */
function df_check_leaf(CX $e) {return !df_xml_exists($e) || !$e->children()->count();}

/**
 * 2016-09-01
 * Вообще говоря, заголовок у XML необязателен,
 * но моя функция @see df_xml_prettify() его добавляет,
 * поэтому меня пока данный алгоритм устраивает.
 * Более качественный алгоритм будет более ресурсоёмким: нам надо будет разбирать весь XML.
 * @param mixed $v
 * @return bool
 */
function df_check_xml($v) {return is_string($v) && df_starts_with($v, '<?xml');}

/**
 * 2015-02-27
 * Обратите внимание на разницу между @see \SimpleXMLElement::asXML()
 * и @see \SimpleXMLElement::__toString() / оператор (string)$this.
 *
 * @see \SimpleXMLElement::__toString() и (string)$this
 * возвращают непустую строку только для концевых узлов (листьев дерева XML).
 * Пример:
	<?xml version='1.0' encoding='utf-8'?>
		<menu>
			<product>
				<cms>
					<class>aaa</class>
					<weight>1</weight>
				</cms>
				<test>
					<class>bbb</class>
					<weight>2</weight>
				</test>
			</product>
		</menu>
 * Здесь для $e1 = $xml->{'product'}->{'cms'}->{'class'}
 * мы можем использовать $e1->__toString() и (string)$e1.
 * http://3v4l.org/rAq3F
 * Однако для $e2 = $xml->{'product'}->{'cms'}
 * мы не можем использовать $e2->__toString() и (string)$e2,
 * потому что узел «cms» не является концевым узлом (листом дерева XML).
 * http://3v4l.org/Pkj37
 * Более того, метод @see \SimpleXMLElement::__toString()
 * отсутствует в PHP версий 5.2.17 и ниже:
 * http://3v4l.org/Wiia2#v500
 *
 * 2015-03-02
 * Обратите внимание,
 * то мы специально допускаем возможность для первого параметра $e принимать значение null:
 * это даёт нам возможность писать код типа:
 * @used-by Df_Page_Helper_Head::needSkipAsStandardCss()
	df_leaf_b(df_config_node(
		'df/page/skip_standard_css/', df_state()->getController()->getFullActionName()
	))
 * без дополнительных проверок, имеется ли в наличии запрашиваемый лист дерева XML
 * (если лист отсутствует, то @see df_config_node() вернёт null)
 *
 * @param CX|null $e [optional]
 * @param string|null|callable $default [optional]
 * @return string|null
 */
function df_leaf(CX $e = null, $default = null) {
	/** @var string $result */
	/**
	 * 2015-08-04
	 * Нельзя здесь использовать !$e,
	 * потому что для концевых текстовых узлов с ненулевым целым значением (например: «147»)
	 * такое выражение довольно-таки неожиданно возвращает true.
	 * @see \SimpleXMLElement вообще необычный класс с нестандартным поведением.
	 * Чтобы понять, почему в данном случае !$e равно true, посморите функцию @see df_xml_exists()
	 *
	 * Так вот, @see df_xml_exists() для текстового узла всегда возвращает false,
	 * даже если текстовое значение не приводится к false (то же «147»).
	 *
	 * Почему так происходит — видно из реализации @see df_xml_exists(): !empty($e)
	 * То есть, empty($e) для текстовых узлов возвращает true.
	 *
	 * Например:
		<Остаток>
			<Склад>
				<Ид>6f87e83f-722c-11df-b336-0011955cba6b</Ид>
				<Количество>147</Количество>
			</Склад>
		</Остаток>
	 * Если здесь сделать xpath Остаток/Склад/Количество,
	 * то для узла «147» !$e почему-то вернёт true,
	 * хотя в данном случае $e является полноценным объектом @see \SimpleXMLElement
	 * и (string)$e возвращает «147».
	 */
	if (is_null($e)) {
		$result = df_call_if($default);
	}
	else {
		df_assert_leaf($e);
		$result = (string)$e;
		if (df_empty_string($result)) {
			/**
			 * 2015-09-25
			 * Добавил данное условие, чтобы различать случай пустого узла и отсутствия узла.
			 * Пример пустого узла ru_RU:
			 * <term>
			 * 		<en_US>Order Total</en_US>
			 * 		<ru_RU></ru_RU>
			 * </term>
			 * Так вот, для пустого узла empty($e) вернёт false,
			 * а для отсутствующего узла — true.
			 */
			$result = df_if1(empty($e), $default, '');
		}
	}
	return $result;
}

/**
 * @param CX|null $e [optional]
 * @param bool $default|callable [optional]
 * @return bool
 */
function df_leaf_b(CX $e = null, $default = false) {return df_bool(df_leaf($e, $default));}

/**
 * @param CX $e
 * @param string $child
 * @param string|mixed|null|callable $default [optional]
 * @return string|mixed|null
 */
function df_leaf_child(CX $e, $child, $default = null) {return df_leaf($e->{$child}, $default);}

/**
 * 2015-08-16
 * Намеренно убрал параметр $default.
 * @param CX|null $e [optional]
 * @return float
 */
function df_leaf_f(CX $e = null) {return df_float(df_leaf($e));}

/**
 * 2015-08-16
 * Намеренно убрал параметр $default.
 * @param CX|null $e [optional]
 * @return int
 */
function df_leaf_i(CX $e = null) {return df_int(df_leaf($e));}

/**
 * @param CX|null $e [optional]
 * @param string $default|callable [optional]
 * @return string
 */
function df_leaf_s(CX $e = null, $default = '') {return (string)df_leaf($e, $default);}

/**
 * @param CX|null $e [optional]
 * @param string $default|callable [optional]
 * @return string
 */
function df_leaf_sne(CX $e = null, $default = '') {
	/** @var string $result */
	$result = df_leaf_s($e, $default);
	if (df_empty_string($result)) {
		df_error('Лист дерева XML должен быть непуст, однако он пуст.');
	}
	return $result;
}

/**
 * @param CX $e
 * @param string $name
 * @param bool $required [optional]
 * @return CX|null
 * @throws E
 */
function df_xml_child(CX $e, $name, $required = false) {
	/** @var CX[] $childNodes */
	$childNodes = df_xml_children($e, $name, $required);
	/** @var CX|null $result */
	if (is_null($childNodes)) {
		$result = null;
	}
	else {
		/**
		 * Обратите внимание, что если мы имеем структуру:
			<dictionary>
				<rule/>
				<rule/>
				<rule/>
			</dictionary>
		 * то $this->e()->{'rule'} вернёт не массив, а объект (!),
		 * но при этом @see count() для этого объекта работает как для массива (!),
		 * то есть реально возвращает количество детей типа rule.
		 * Далее, оператор [] также работает, как для массива (!)
		 * http://stackoverflow.com/a/16100099
		 * Класс \SimpleXMLElement — вообще один из самых необычных классов PHP.
		 */
		df_assert_eq(1, count($childNodes));
		$result = $childNodes[0];
		df_assert($result instanceof CX);
	}
	return $result;
}

/**
 * @param CX $e
 * @param string $name
 * @param bool $required [optional]
 * @return CX|null
 * @throws E
 */
function df_xml_children(CX $e, $name, $required = false) {
	df_param_sne($name, 0);
	/** @var CX|null $result */
	if (df_xml_exists_child($e, $name)) {
		/**
		 * Обратите внимание, что если мы имеем структуру:
		 *	<dictionary>
		 *		<rule/>
		 *		<rule/>
		 *		<rule/>
		 *	</dictionary>
		 * то $e->{'rule'} вернёт не массив, а объект (!),
		 * но при этом @see count() для этого объекта работает как для массива (!),
		 * то есть реально возвращает количество детей типа rule.
		 * Далее, оператор [] также работает, как для массива (!)
		 * http://stackoverflow.com/a/16100099
		 * Класс \SimpleXMLElement — вообще один из самых необычных классов PHP.
		 */
		$result = $e->{$name};
	}
	elseif (!$required) {
		$result = null;
	}
	else {
		df_error("Требуемый узел «{$name}» отсутствует в документе:\n{xml}", ['{xml}' => df_xml_report($e)]);
	}
	return $result;
}

/**
 * 2015-02-27
 * Алгоритм взят отсюда: http://stackoverflow.com/a/5344560
 * Проверил, что он работает: http://3v4l.org/tnEIJ
 * Обратите внимание, что isset() вместо empty() не сработает: http://3v4l.org/2P5o0
 * isset, однако, работает для проверки наличия дочерних листов: @see df_xml_exists_child()
 *
 * Обратите внимание, что оператор $e->{'тест'} всегда возвращает объект @see \SimpleXMLElement,
 * вне зависимости от наличия узла «тест», просто для отсутствующего узла данный объект будет пуст,
 * и empty() для него вернёт true.
 *
 * 2015-08-04
 * Заметил, что empty($e) для текстовых узлов всегда возвращает true,
 * даже если узел как строка приводится к true (например: «147»).
 * Например:
 * Например:
	<Остаток>
		<Склад>
			<Ид>6f87e83f-722c-11df-b336-0011955cba6b</Ид>
			<Количество>147</Количество>
		</Склад>
	</Остаток>
 * Если здесь сделать xpath Остаток/Склад/Количество,
 * то для узла «147» @see df_xml_exists($e) вернёт false.
 *
 * Обратите внимание, что эту особенность использует алгоритм @see df_check_leaf():
 * return !df_xml_exists($e) || !count($e->children());
 *
 * @param CX|null $e
 * @return bool
 */
function df_xml_exists(CX $e = null) {return !empty($e);}

/**
 * http://stackoverflow.com/questions/1560827/php-simplexml-check-if-a-child-exist#comment20135428_1562158
 * @param CX $e
 * @param string $child
 * @return bool
 */
function df_xml_exists_child(CX $e, $child) {return isset($e->{$child});}

/**
 * 2016-08-31
 * @used-by \Dfe\Qiwi\Result::__toString()
 * @used-by \Dfe\SecurePay\Refund::process()
 * @used-by \Dfe\YandexKassa\Result::__toString()
 * @param string $tag
 * @param array(string => mixed) $contents
 * @param array(string => mixed) $p [optional]
 * @return string
 */
function df_xml_g($tag, array $contents, array $p = []) {return \Df\Xml\G::p($tag, $contents, $p);}

/**
 * 2016-09-01
 * @see df_xml_parse_header()
 * @used-by \Df\Xml\G::_p()
 * @param string $encoding [optional]
 * @param string $version [optional]
 * @return string
 */
function df_xml_header($encoding = 'UTF-8', $version = '1.0') {return
	"<?xml version='{$version}' encoding='{$encoding}'?>"
;}

/**
 * 2015-08-24
 * @used-by Df_Localization_Dictionary::e()
 * @param string $filename
 * @return X
 */
function df_xml_load_file($filename) {
	/** @var X $result */
	libxml_use_internal_errors(true);
	if (!($result = @simplexml_load_file($filename, X::class))) {
		df_xml_throw_last("При разборе файла XML произошёл сбой.\nФайл: " . df_path_relative($filename));
	}
	return $result;
}

/**
 * @param string $tag
 * @param array(string => string) $attributes [optional]
 * @param mixed[] $contents [optional]
 * @return X
 */
function df_xml_node($tag, array $attributes = [], array $contents = []) {
	/** @var X $result */
	$result = df_xml_parse(df_sprintf('<%s/>', $tag));
	$result->addAttributes($attributes);
	$result->importArray($contents);
	return $result;
}

/**
 * @used-by df_exception_to_session()
 * @param string[] ...$args
 * @return string|string[]
 */
function df_xml_output_html(...$args) {return df_call_a(function($text) {return
	!df_contains($text, DF_XML_BEGIN) ? $text :
		preg_replace_callback(sprintf('#%s([\s\S]*)%s#mui', DF_XML_BEGIN, DF_XML_END),
			/**
			 * Обратите внимание, что тег должен быть именно <pre>, именно в нижнем регистре
			 * и только с атрибутом class, потому что этот тег разбирается регулярным выражением
			 * в методе @see \Df\Core\Helper\Text::nl2br()
			 * @param string[] $matches
			 * @return string
			 */
			function (array $matches) {return
				strtr('<pre class="df-xml">{contents}</div>', [
					'{contents}' => df_e(df_normalize(dfa($matches, 1, '')))
				])
			;}
		, $text)
;}, $args);}

/**
 * @used-by Df_Qa_Message::sections()
 * @param string[] $args
 * @return string|string[]
 */
function df_xml_output_plain(...$args) {return df_call_a(function($text) {return
	str_replace([DF_XML_BEGIN, DF_XML_END], null, $text)
;}, $args);}

/**
 * @param string|X $x
 * @param bool $throw [optional]
 * @return X|null
 * @throws E
 */
function df_xml_parse($x, $throw = true) {
	/** @var X $result */
	if ($x instanceof X) {
		$result = $x;
	}
	else {
		df_param_sne($x, 0);
		$result = null;
		try {
			$result = new X($x);
		}
		catch (\Exception $e) {
			if ($throw) {
				df_error(
					"При синтаксическом разборе документа XML произошёл сбой:\n"
					. "«%s»\n"
					. "********************\n"
					. "%s\n"
					. "********************\n"
					, df_ets($e)
					, df_trim($x)
				);
			}
		}
	}
	return $result;
}

/**
 * 2016-09-01
 * Если XML не отформатирован, то после его заголовка перенос строки идти не обязан:
 * http://stackoverflow.com/a/8384602
 * @param string|X $x
 * @return string |null
 */
function df_xml_parse_header($x) {return df_preg_match('#^<\?xml.*\?>#', df_xml_s($x), false);}

/**
 * 2016-09-01
 * @uses \Df\Xml\X::asNiceXml() не сохраняет заголовок XML.
 * @param string|X $x
 * @return string
 */
function df_xml_prettify($x) {return df_cc_n(df_xml_parse_header($x), df_xml_parse($x)->asNiceXml());}

/**
 * @param CX|MX $e
 * @return string
 */
function df_xml_report(CX $e) {
	return DF_XML_BEGIN . ($e instanceof MX ? $e->asNiceXml() : $e->asXML()) . DF_XML_END;
}

/**
 * 2016-09-01
 * @see df_xml_x()
 * @param string|X $x
 * @return string
 */
function df_xml_s($x) {return is_string($x) ? $x : $x->asXML();}

/**
 * 2015-08-24
 * @used-by df_xml_load_file()
 * @param string $message
 * @throws X
 */
function df_xml_throw_last($message) {
	/** @var LibXMLError[] LibXMLError */
	$errors = libxml_get_errors();
	/** @var string[] $messages */
	$messages = array($message);
	foreach ($errors as $error) {
		/** @var LibXMLError $error */
		$messages[]= sprintf("(%d, %d) %s", $error->line, $error->column, $error->message);
	}
	df_error($messages);
}

/**
 * 2016-09-01
 * @see df_xml_s()
 * @param string|X $x
 * @return X
 */
function df_xml_x($x) {return $x instanceof X ? $x : df_xml_parse($x);}