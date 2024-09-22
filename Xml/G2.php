<?php
namespace Df\Xml;
use Df\Core\Exception as E;
use Df\Core\Text\Marker;
use \SimpleXMLElement as X;
use \Throwable as T; # 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
# 2024-09-22
# 1) "Refactor `Df\Xml\X`": https://github.com/mage2pro/core/issues/436
# 2) "Refactor the `Df_Xml` module": https://github.com/mage2pro/core/issues/437
final class G2 {
	/**
	 * 2024-09-22
	 * @used-by self::addChild()
	 * @param X|string $x
	 */
	function __construct($x) {$this->_x = df_xml_x($x);}

	/**
	 * 2021-12-13
	 * @used-by self::addAttributes()
	 * @used-by self::addChildX()
	 */
	function addAttribute(string $k, string $v = '', string $ns = ''):void {$this->_x->addAttribute($this->k($k), $v, $ns);}

	/**
	 * @used-by df_xml_node()
	 * @used-by self::importArray()
	 * @param array(string => string) $atts
	 */
	function addAttributes(array $aa):void {
		foreach ($aa as $k => $v) {/** @var string $k */ /** @var mixed $v */
			df_assert_stringable(
				$v
				,sprintf("The attribute «{$k}» has a non-`Strinable` type %s.", df_type($v))
				,['attributes' => $aa]
			);
			$this->addAttribute(df_assert_sne($k), $v);
		}
	}

	/**
	 * @uses \SimpleXMLElement::addChild() создаёт и возвращает не просто SimpleXMLElement, как говорит документация,
	 * а объект класса родителя.
	 * 2022-11-15
	 * 1) `static` as a return type is not supported by PHP < 8: https://github.com/mage2pro/core/issues/168#user-content-static
	 * 2) We can not declare the $name argument type with PHP < 8: https://3v4l.org/ptpUM
	 * @used-by self::addChildText()
	 * @used-by self::addChildX()
	 * @used-by self::importArray()
	 * @used-by self::importString()
	 * @throws E
	 */
	private function addChild(string $k, string $v = '', string $ns = ''):self {/** @var self $r */
		try {$r = new self($this->_x->addChild($this->k($k), $v, $ns));}
		catch (T $t) {df_error("Tag <{$k}>. Value: «{$v}». Error: «%s».", df_xts($t));}
		return $r;
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
		$kAsString = $kIsEmpty ? $this->_x->getName() : $k; /** @var string $kAsString */
		$vIsString = is_string($v); /** @var bool $vIsString */
		$vAsString = ''; /** @var string $vAsString */
		try {$vAsString = $vIsString ? $v : df_string($v);}
		catch (T $t) {df_error("Unable to convert the value of the key «{$kAsString}» to a string.\n%s", df_xts($t));}
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
				? $this->_x[0] = $vAsString # http://stackoverflow.com/a/3153704
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
	 * 2024-09-22
	 * @used-by self::__construct()
	 * @used-by self::addAttribute()
	 * @used-by self::importString()
	 * @var X
	 */
	private $_x;

	/**
	 * 2021-12-12
	 * @used-by self::importString()
	 * @used-by self::markAsCData()
	 */
	private static function marker():Marker {static $r; return $r ?: $r = new Marker('[[', ']]');}
}