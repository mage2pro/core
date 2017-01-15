<?php
namespace Df\Core\Format;
/** @method static NounForAmounts s() */
class NounForAmounts extends \Df\Core\O {
	/**         
	 * @used-by \Df\Core\Helper\Text::getNounForm()
	 * @param int $amount
	 * @param array $forms
	 * @return string
	 */
	public function getForm($amount, array $forms) {return df_result_s(
		dfa($forms, $this->getIndex(df_param_integer($amount, 0)))
	);}

	/**
	 * Форма склонения слова.
	 * Существительное с числительным склоняется одним из трех способов:
	 * 1 миллион, 2 миллиона, 5 миллионов.
	 *
	 * @param int $amount
	 * @return int
	 */
	public function getIndex($amount) {
		/** @var int $result */
		$result = null;
		/** @var int $n100 */
		$n100 = $amount % 100;
		/** @var int $n100 */
		$n10 = $amount % 10;
		if (($n100 > 10) && ($n100 < 20)) {
			$result = self::NOUN_FORM_5;
		}
		else if ($n10 === 1) {
			$result = self::NOUN_FORM_1;
		}
		else if (($n10 >= 2) && ($n10 <= 4)) {
			$result = self::NOUN_FORM_2;
		}
		else {
			$result = self::NOUN_FORM_5;
		}
		df_result_integer($result);
		return $result;
	}
	const NOUN_FORM_1 = 0;
	const NOUN_FORM_2 = 1;
	const NOUN_FORM_5 = 2;
}