<?php
namespace Df\Core\Format;
/**
 * 2017-01-15
 * В настоящее время этот класс никем не используется.
 * В Российской сборке Magento он использовался только квитанцией Сбербанка.
 * Решил оставить здесь этот класс до времени переноса сюда квитанции Сбербанка,
 * потому что если бы мы брали этот класс из кода Российской сборки Magento
 * в момент переноса сюда квитанции Сбербанка,
 * то код нуждался бы в масштабном рефакторинге,
 * а так мы потихоньку проводим с ним маленькие рефакторинги.
 */
class NumberInWords extends \Df\Core\O {
	/** @return string */
	function getFractionalValueInWords() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = 0 === $this->getNumberFractionalPart() ? '' : df_cc_s(
				$this->getNumberFractionalPartInWords()
				,dfa($this->getFractionalPartUnits(), $this->getNumberFractionalPartForm())
			);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	function getIntegerValueInWords() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = 0 === $this->getNumberIntegerPart() ? '' : df_cc_s(
				$this->getNumberIntegerPartInWords()
				,dfa($this->getIntegerPartUnits(), $this->getNumberIntegerPartForm())
			);
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	function getNumberFractionalPart() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_round(
						pow(10, $this->getFractionalPartPrecision())
					*
						(
								$this->getNumber()
							-
								$this->getNumberIntegerPart()
						)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	function getNumberFractionalPartInWords() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getNaturalNumberInWords(
					$this->getNumberFractionalPart()
					, $this->getFractionalPartGender()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	function getNumberIntegerPartInWords() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getNaturalNumberInWords(
					$this->getNumberIntegerPart()
					,$this->getIntegerPartGender()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	function getValueInWords() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_cc_s(
				$this->getIntegerValueInWords(), $this->getFractionalValueInWords()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getFractionalPartGender() {return df_assert_in(
		$this[self::P__FRACTIONAL_PART_GENDER], [self::GENDER__MALE, self::GENDER__FEMALE]
	);}

	/** @return int */
	private function getFractionalPartPrecision() {return $this->cfg(self::P__FRACTIONAL_PART_PRECISION);}

	/** @return array */
	private function getFractionalPartUnits() {return $this->cfg(self::P__FRACTIONAL_PART_UNITS);}

	/** @return string */
	private function getIntegerPartGender() {return df_assert_in(
		$this[self::P__INTEGER_PART_GENDER], [self::GENDER__MALE, self::GENDER__FEMALE]
	);}

	/** @return array */
	private function getIntegerPartUnits() {return $this->cfg(self::P__INTEGER_PART_UNITS);}

	/**
	 * @param int $number
	 * @param string $gender
	 * @return string
	 */
	private function getNaturalNumberInWords($number, $gender) {
		df_param_integer($number, 0);
		df_param_between($number, 0, 0, self::MAX_NUMBER);
		df_param_sne($gender, 1);
		df_assert_in($gender, [self::GENDER__MALE, self::GENDER__FEMALE]);
		/** @var string $result */
		$result = 'ноль';
		if (0 !== $number) {
			$result  = preg_replace(['/s+/','/\s$/'], [' ',''], $this->getNum1E9($number, $gender));
		}
		return df_result_s($result);
	}

	/** @return float */
	private function getNumber() {return dfc($this, function() {return
		df_result_between($this[self::P__NUMBER], 0, self::MAX_NUMBER)
	;});}

	/** @return int */
	private function getNumberFractionalPartForm() {return dfc($this, function() {return
		self::getNum125($this->getNumberFractionalPart())
	;});}

	/** @return int */
	private function getNumberIntegerPart() {return df_int($this->getNumber());}

	/** @return int */
	private function getNumberIntegerPartForm() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = self::getNum125($this->getNumberIntegerPart());
		}
		return $this->{__METHOD__};
	}
	const GENDER__FEMALE = 'female';
	const GENDER__MALE = 'male';
	const MAX_NUMBER = 1e9;
	const NUMBER_FORM_1 = 0;
	const NUMBER_FORM_2 = 1;
	const NUMBER_FORM_5 = 2;
	const P__FRACTIONAL_PART_GENDER = 'fractionalPartGender';
	const P__FRACTIONAL_PART_PRECISION = 'fractionalPartPrecision';
	const P__FRACTIONAL_PART_UNITS = 'fractionalPartUnits';
	const P__INTEGER_PART_GENDER = 'integerPartGender';
	const P__INTEGER_PART_UNITS = 'integerPartUnits';
	const P__NUMBER = 'number';

	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return \Df\Core\Format\NumberInWords
	 */
	static function i(array $parameters = []) {return new self($parameters);}

	/**
	 * @static
	 * @param $number
	 * @param string $gender
	 * @return string
	 */
	private static function getNum100($number, $gender) {
		/** @var array(string => array(string => string)) $words */
		static $words = [
			self::GENDER__MALE => [
				''
				,'один','два','три','четыре','пять','шесть'
				, 'семь','восемь','девять','десять','одиннадцать'
				, 'двенадцать','тринадцать','четырнадцать','пятнадцать'
				, 'шестнадцать','семнадцать','восемнадцать','девятнадцать'
			]
			,self::GENDER__FEMALE => [
				'','одна','две','три','четыре','пять','шесть'
				, 'семь','восемь','девять','десять','одиннадцать'
				, 'двенадцать','тринадцать','четырнадцать','пятнадцать'
				, 'шестнадцать','семнадцать','восемнадцать','девятнадцать'
			]
		];
		/** @var array(int => string) $words2 */
		static $words2 = [
			''
			,'десять','двадцать','тридцать','сорок','пятьдесят'
			,'шестьдесят','семьдесят','восемьдесят','девяносто'
		];
		return
			$number < 20
			? dfa($words[$gender], $number)
			:
				dfa($words2, (int)($number / 10))
				. ($number % 10 ? ' ' . dfa($words[$gender], $number % 10) : '')
		;
	}

	/**
	 * @param $number
	 * @param string $gender
	 * @return string
	 */
	private static function getNum1000($number, $gender) {
		/** @var array(int => string) $words */
		static $words = [
			''
			,'сто','двести','триста','четыреста','пятьсот'
			,'шестьсот','семьсот','восемьсот','девятьсот'
		];
		return
			$number < 100
			? self::getNum100($number, $gender)
			:
				dfa($words, (int)($number / 100))
				. ($number % 100 ? ' ' . self::getNum100($number % 100, $gender) : '')
		;
	}

	/**
	 * Форма склонения слова.
	 * Существительное с числительным склоняется одним из трех способов:
	 * 1 миллион, 2 миллиона, 5 миллионов.
	 * @static
	 * @param int $number
	 * @return int
	 */
	private static function getNum125($number) {
		/** @var int $result */
		/** @var int $n100 */
		$n100 = $number % 100;
		/** @var int $n100 */
		$n10 = $number % 10;
		if (($n100 > 10) && ($n100 < 20)) {
			$result = self::NUMBER_FORM_5;
		}
		else if ($n10 === 1) {
			$result = self::NUMBER_FORM_1;
		}
		else if (($n10 >= 2) && ($n10 <= 4)) {
			$result = self::NUMBER_FORM_2;
		}
		else {
			$result = self::NUMBER_FORM_5;
		}
		return $result;
	}

	/**
	 * @static
	 * @param $number
	 * @param string $gender
	 * @return string
	 */
	private static function getNum1E6($number, $gender) {
		/** @var array(int => string) */
		static $words = ['тысяча', 'тысячи', 'тысяч'];
		/** @var string $result */
		$result =
			(1000 > $number)
			? self::getNum1000($number, $gender)
			: df_cc_s(
				self::getNum1000((int)($number / 1000), self::GENDER__MALE)
				,dfa($words, self::getNum125((int)($number / 1000)))
				,self::getNum1000($number % 1000, $gender)
			)
		;
		return df_result_s($result);
	}

	/**
	 * @param $number
	 * @param string $gender
	 * @return string
	 */
	private static function getNum1E9($number, $gender) {
		/** @var array(int => string) $words */
		static $words =	['миллион', 'миллиона', 'миллионов'];
		return
			$number < 1e6
			? self::getNum1E6($number, $gender)
			: df_cc_s(
				self::getNum1000((int)($number / 1e6), self::GENDER__FEMALE)
				,dfa($words, self::getNum125((int)($number / 1e6)))
				,self::getNum1E6($number % 1e6, $gender)
			)
		;
	}
}