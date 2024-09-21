<?php
use Df\Core\Exception as E;
use SimpleXMLElement as CX;

/**
 * @deprecated It is unused.
 * @throws E
 */
function df_xml_child(CX $e, string $name, bool $req = false):?CX { /** @var ?CX  $r */
	$childNodes = df_xml_children($e, $name, $req); /** @var CX[] $childNodes */
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
		$r = df_ar($childNodes[0], CX::class);
	}
	return $r;
}

/**
 * @used-by df_xml_child()
 * @return CX|null
 * @throws E
 */
function df_xml_children(CX $e, string $name, bool $req = false):?CX { /** @var ?CX $r */
	df_param_sne($name, 0);
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
		 * Класс @see \SimpleXMLElement — вообще один из самых необычных классов PHP.
		 */
		$r = $e->{$name};
	}
	elseif (!$req) {
		$r = null;
	}
	else {
		df_error("The required node «{$name}» is absent in the XML document:\n{xml}", ['{xml}' => df_xml_report($e)]);
	}
	return $r;
}