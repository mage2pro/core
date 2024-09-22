<?php
use Df\Core\Exception as E;
use SimpleXMLElement as X;

/**
 * @deprecated It is unused.
 * @throws E
 */
function df_xml_child(X $e, string $name, bool $req = false):?X { /** @var ?X  $r */
	$childNodes = df_xml_children($e, $name, $req); /** @var X[] $childNodes */
	if (is_null($childNodes)) {
		$r = null;
	}
	else {
		/**
		 * Обратите внимание, что если мы имеем структуру:
		 *	<dictionary>
		 *		<rule/>
		 *		<rule/>
		 *		<rule/>
		 *	</dictionary>
		 * то $this->e()->{'rule'} вернёт не массив, а объект (!),
		 * но при этом @see count() для этого объекта работает как для массива (!),
		 * то есть реально возвращает количество детей типа rule.
		 * Далее, оператор [] также работает, как для массива (!)
		 * http://stackoverflow.com/a/16100099
		 * Класс @see \SimpleXMLElement — вообще один из самых необычных классов PHP.
		 */
		df_assert_eq(1, count($childNodes));
		$r = df_ar($childNodes[0], X::class);
	}
	return $r;
}

/**
 * @used-by df_xml_child()
 * @throws E
 */
function df_xml_children(X $x, string $name, bool $req = false):?X { /** @var ?X $r */
	df_param_sne($name, 0);
	if (df_xml_exists_child($x, $name)) {
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
		 * Класс @see \SimpleXMLElement — вообще один из самых необычных классов PHP.
		 */
		$r = $x->{$name};
	}
	elseif (!$req) {
		$r = null;
	}
	else {
		df_error("The required node «{$name}» is absent in the XML document:\n{xml}", ['{xml}' => df_xml_s($x)]);
	}
	return $r;
}