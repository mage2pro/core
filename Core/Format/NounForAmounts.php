<?php
namespace Df\Core\Format;
/** @method static NounForAmounts s() */
class NounForAmounts extends \Df\Core\O {
	/**         
	 * @used-by \Df\Core\Helper\Text::getNounForm()
	 * @param int $a
	 * @param array $forms
	 * @return string
	 */
	function getForm($a, array $forms) {return df_result_s(dfa(
		$forms, $this->getIndex(df_param_integer($a, 0))
	));}

	/**
	 * Форма склонения слова.
	 * Существительное с числительным склоняется одним из трех способов:
	 * 1 миллион, 2 миллиона, 5 миллионов.
	 *
	 * @param int $a
	 * @return int
	 */
	function getIndex($a) {
		/** @var int $result */
		/** @var int $n100 */
		$n100 = $a % 100;
		/** @var int $n100 */
		$n10 = $a % 10;
		if (($n100 > 10) && ($n100 < 20)) {
			$result = self::NOUN_FORM_5;
		}
		elseif ($n10 === 1) {
			$result = self::NOUN_FORM_1;
		}
		elseif (($n10 >= 2) && ($n10 <= 4)) {
			$result = self::NOUN_FORM_2;
		}
		else {
			$result = self::NOUN_FORM_5;
		}
		return $result;
	}
	const NOUN_FORM_1 = 0;
	const NOUN_FORM_2 = 1;
	const NOUN_FORM_5 = 2;
}