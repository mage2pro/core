<?php
namespace Df\Core\Model\Format;
class NumberInWords extends \Df\Core\O {
	/** @return string */
	public function getFractionalValueInWords() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				(0 === $this->getNumberFractionalPart())
				? ''
				: implode(' ', array(
					$this->getNumberFractionalPartInWords()
					,df_a($this->getFractionalPartUnits(), $this->getNumberFractionalPartForm())
				))
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getIntegerValueInWords() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				(0 === $this->getNumberIntegerPart())
				? ''
				: implode(' ', array(
					$this->getNumberIntegerPartInWords()
					,df_a($this->getIntegerPartUnits(), $this->getNumberIntegerPartForm())
				))
			;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getNumberFractionalPart() {
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
	public function getNumberFractionalPartInWords() {
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
	public function getNumberIntegerPartInWords() {
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
	public function getValueInWords() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_concat_clean(' '
				,$this->getIntegerValueInWords()
				,$this->getFractionalValueInWords()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getFractionalPartGender() {
		/** @var string $result */
		$result = $this->cfg(self::P__FRACTIONAL_PART_GENDER);
		df_assert_in($result, array(self::GENDER__MALE, self::GENDER__FEMALE));
		return $result;
	}

	/** @return int */
	private function getFractionalPartPrecision() {return $this->cfg(self::P__FRACTIONAL_PART_PRECISION);}

	/** @return array */
	private function getFractionalPartUnits() {return $this->cfg(self::P__FRACTIONAL_PART_UNITS);}

	/** @return string */
	private function getIntegerPartGender() {
		/** @var string $result */
		$result = $this->cfg(self::P__INTEGER_PART_GENDER);
		df_assert_in($result, array(self::GENDER__MALE, self::GENDER__FEMALE));
		return $result;
	}

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
		df_param_string($gender, 1);
		df_assert_in($gender, array(self::GENDER__MALE, self::GENDER__FEMALE));
		/** @var string $result */
		$result = 'ноль';
		if (0 !== $number) {
			$result  =
				preg_replace(
					array('/s+/','/\s$/')
					,array(' ','')
					,$this->getNum1E9($number, $gender)
				)
			;
		}
		df_result_string($result);
		return $result;
	}

	/** @return float */
	private function getNumber() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->cfg(self::P__NUMBER);
			df_result_between($this->{__METHOD__}, 0, self::MAX_NUMBER);
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	private function getNumberFractionalPartForm() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = self::getNum125($this->getNumberFractionalPart());
		}
		return $this->{__METHOD__};
	}

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
	 * @return \Df\Core\Model\Format\NumberInWords
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}

	/**
	 * @static
	 * @param $number
	 * @param string $gender
	 * @return string
	 */
	private static function getNum100($number, $gender) {
		/** @var array(string => array(string => string)) $words */
		static $words = array(
			self::GENDER__MALE => array(
				''
				,'один','два','три','четыре','пять','шесть'
				, 'семь','восемь','девять','десять','одиннадцать'
				, 'двенадцать','тринадцать','четырнадцать','пятнадцать'
				, 'шестнадцать','семнадцать','восемнадцать','девятнадцать'
			)
			,self::GENDER__FEMALE => array(
				'','одна','две','три','четыре','пять','шесть'
				, 'семь','восемь','девять','десять','одиннадцать'
				, 'двенадцать','тринадцать','четырнадцать','пятнадцать'
				, 'шестнадцать','семнадцать','восемнадцать','девятнадцать'
			)
		);
		/** @var array(int => string) $words2 */
		static $words2 = array(
			''
			,'десять','двадцать','тридцать','сорок','пятьдесят'
			,'шестьдесят','семьдесят','восемьдесят','девяносто'
		);
		return
			$number < 20
			? df_a($words[$gender], $number)
			:
				df_a($words2, (int)($number / 10))
				. ($number % 10 ? ' ' . df_a($words[$gender], $number % 10) : '')
		;
	}

	/**
	 * @param $number
	 * @param string $gender
	 * @return string
	 */
	private static function getNum1000($number, $gender) {
		/** @var array(int => string) $words */
		static $words = array(
			''
			,'сто','двести','триста','четыреста','пятьсот'
			,'шестьсот','семьсот','восемьсот','девятьсот'
		);
		return
			$number < 100
			? self::getNum100($number, $gender)
			:
				df_a($words, (int)($number / 100))
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
		$result = null;
		/** @var int $n100 */
		$n100 = $number % 100;
		df_assert_integer($n100);
		/** @var int $n100 */
		$n10 = $number % 10;
		df_assert_integer($n10);
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
		df_result_integer($result);
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
		static $words = array( 'тысяча', 'тысячи', 'тысяч');
		/** @var string $result */
		$result =
			(1000 > $number)
			? self::getNum1000($number, $gender)
			: implode(' ', array(
				self::getNum1000((int)($number / 1000), self::GENDER__MALE)
				,df_a($words, self::getNum125((int)($number / 1000)))
				,self::getNum1000($number % 1000, $gender)
			))
		;
		df_result_string($result);
		return $result;
	}

	/**
	 * @param $number
	 * @param string $gender
	 * @return string
	 */
	private static function getNum1E9($number, $gender) {
		/** @var array(int => string) $words */
		static $words =	array('миллион', 'миллиона', 'миллионов');
		return
			$number < 1e6
			? self::getNum1E6($number, $gender)
			: implode(' ', array(
				self::getNum1000((int)($number / 1e6), self::GENDER__FEMALE)
				,df_a($words, self::getNum125((int)($number / 1e6)))
				,self::getNum1E6($number % 1e6, $gender)
			))
		;
	}
}