<?php
namespace Df\Xml;
use \SimpleXMLElement as X;
# 2024-09-23 "Refactor the `Df_Xml` module": https://github.com/mage2pro/core/issues/437
final class A {
	/**
	 * 2024-09-23
	 * @used-by df_xml2a()
	 * @used-by self::p()
	 * @param X|string $x
	 * @return string|array(string => mixed)
	 */
	static function p($x) {
		$aa = df_xml_atts($x = df_xml_x($x)); /** @var array(string => string) $aa */
		$cc = !$x->hasChildren() ? [] : df_map(__METHOD__, $x->children()); /** @var array(string => mixed) $cc */
		# 2024-09-24
		# 1.1) We can not use `empty($x)` instead of `!$aa && !$cc`.
		# 1.2) «A variable is considered empty if it does not exist or if its value equals `false`.»
		# https://www.php.net/manual/en/function.empty.php
		# https://archive.is/8HRC5#selection-995.60-1001.5
		# 1.2) The official PHP documentation contains a wrong statement:
		#	«When converting to `bool`, the following values are considered `false`:
		# 		*) Internal objects that overload their casting behaviour to `bool`.
		#		For example: `SimpleXML` objects created from empty elements without attributes.»
		# https://www.php.net/manual/en/language.types.boolean.php#language.types.boolean.casting
		# https://archive.is/FcCfj#selection-1353.0-1355.60
		# 1.3) Even if a node has attributes, but does not have a content, `empty($x)` returns `true` for it:
		# 1.3.1) https://3v4l.org/h7hRH
		# 1.3.2) https://3v4l.org/YM3I8
		# 1.3.3) https://3v4l.org/2vaHf
		# 1.3.4) https://stackoverflow.com/questions/1560827#comment74422321_5344560
		return !$aa && !$cc ? (string)$x : ((!$aa ? [] : ['@' => $aa]) + ($cc ?: [0 => (string)$x]));
	}
}