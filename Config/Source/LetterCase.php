<?php
namespace Df\Config\Source;
class LetterCase extends \Df\Config\SourceT {
	/**
	 * 2015-11-14
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	protected function map() {
		/** @var string|null $sample */
		$sample = $this->f('df_sample');
		/** @var string[] $values */
		$values = [self::_DEFAULT, self::$UCFIRST, self::$UCWORDS, self::$UPPERCASE, self::$LOWERCASE];
		/** @var string[] $labels */
		$labels = [
			'As Is'
			, 'Uppercase first letter'
			, 'Uppercase Each Word\'s First Letter'
			,'UPPERCASE'
			,'lowercase'
		];
		return array_combine(
			$values
			,!$sample
			? $labels
			: array_map(function($value, $label) use ($sample) {
				return sprintf('%s (%s)', self::apply($sample, $value), $label);
			}, $values, $labels)
			//: df_map([__CLASS__, 'apply'], $keys, [], $sample)
		);
	}

	/**
	 * @used-by convertToCss()
	 * @used-by isDefault()
	 * @used-by toOptionArrayInternal()
	 * @used-by Df_Admin_Config_Font::getLetterCase()
	 */
	const _DEFAULT = 'default';

	/**
	 * @used-by Df_Admin_Config_Font::getLetterCaseCss()
	 * @param string $value
	 * @return string
	 */
	public static function css($value) {
		return df_a([
			self::_DEFAULT => 'none'
			,self::$UPPERCASE => self::$UPPERCASE
			,self::$LOWERCASE => self::$LOWERCASE
			,self::$UCFIRST => 'capitalize'
			/**
			 * 2015-11-14
			 * Одним правилом тут не сделаешь, надо так:
			 * .link { text-transform: lowercase; }
			 * .link:first-letter {text-transform: uppercase;}
			 * http://stackoverflow.com/a/10256138
			 */
			,self::$UCWORDS => 'lowercase'
		], $value);
	}

	/**
	 * @used-by Df_Admin_Config_Font::applyLetterCase()
	 * @param string $text
	 * @param string $format
	 * @return string
	 */
	public static function apply($text, $format) {
		/** @var string $result */
		switch($format) {
			case self::$LOWERCASE:
				$result = mb_strtolower($text);
				break;
			case self::$UPPERCASE:
				$result = mb_strtoupper($text);
				break;
			case self::$UCFIRST:
				$result = df_ucfirst(mb_strtolower(df_trim($text)));
				break;
			case self::$UCWORDS:
				$result = df_ucwords($text);
				break;
			default:
				$result = $text;
		}
		/**
		 * Убрал валидацию результата намеренно: сам метод безобиден,
		 * и даже если он как-то неправильно будет работать — ничего страшного.
		 * Пока метод дал сбой только один раз, в магазине laap.ru
		 * при форматировании заголовков административной таблицы товаров
		 * (видимо, сбой произошёл из-за влияния некоего стороннего модуля).
		 */
		return $result;
	}

	/**
	 * @used-by Df_Admin_Config_Font::isDefault()
	 * @param bool $value
	 * @return bool
	 */
	public static function isDefault($value) {return self::_DEFAULT === $value;}

	/**
	 * @used-by Df_Admin_Config_Font::isUcFirst()
	 * @param bool $value
	 * @return bool
	 */
	public static function isUcFirst($value) {return self::$UCFIRST === $value;}

	/**
	 * @used-by Df_Admin_Config_Font::isUcFirst()
	 * @param bool $value
	 * @return bool
	 */
	public static function isUcWords($value) {return self::$UCWORDS === $value;}

	/** @var string */
	public static $LOWERCASE = 'lowercase';
	/** @var string */
	public static $UCFIRST = 'ucfirst';
	/** @var string */
	public static $UCWORDS = 'ucwords';
	/** @var string */
	public static $UPPERCASE = 'uppercase';

	/** @return \Df\Config\Source\LetterCase */
	public static function s() {static $r; return $r ? $r : $r = df_o(__CLASS__);}
}