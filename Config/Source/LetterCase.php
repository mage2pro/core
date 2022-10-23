<?php
namespace Df\Config\Source;
/** @method static LetterCase s() */
final class LetterCase extends \Df\Config\Source {
	/**
	 * 2015-11-14
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	protected function map():array {
		$vv = [self::_DEFAULT, self::$UCFIRST, self::$UCWORDS, self::$UPPERCASE, self::$LOWERCASE]; /** @var string[] $vv */
		/** @var string[] $ll */
		$ll = ['As Is', 'Uppercase first letter', 'Uppercase Each Word\'s First Letter', 'UPPERCASE', 'lowercase'];
		/** @var string|null $s */
		return array_combine($vv, !($s = $this->f('dfSample')) ? $ll : array_map(function($v, $l) use($s) {return
			df_desc(self::apply($s, $v), $l)
		;}, $vv, $ll));
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
	static function css($value) {
		return dfa([
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
	 * @param string $text
	 * @param string $format
	 * @return string
	 */
	static function apply($text, $format) {/** @var string $r */
		switch($format) {
			case self::$LOWERCASE:
				$r = mb_strtolower($text);
				break;
			case self::$UPPERCASE:
				$r = mb_strtoupper($text);
				break;
			case self::$UCFIRST:
				$r = df_ucfirst(mb_strtolower(df_trim($text)));
				break;
			case self::$UCWORDS:
				$r = df_ucwords($text);
				break;
			default:
				$r = $text;
		}
		/**
		 * Убрал валидацию результата намеренно: сам метод безобиден,
		 * и даже если он как-то неправильно будет работать — ничего страшного.
		 * Пока метод дал сбой только один раз, в магазине laap.ru
		 * при форматировании заголовков административной таблицы товаров
		 * (видимо, сбой произошёл из-за влияния некоего стороннего модуля).
		 */
		return $r;
	}

	/**
	 * @used-by Df_Admin_Config_Font::isDefault()
	 * @param bool $value
	 * @return bool
	 */
	static function isDefault($value) {return self::_DEFAULT === $value;}

	/**
	 * @used-by Df_Admin_Config_Font::isUcFirst()
	 * @param bool $value
	 * @return bool
	 */
	static function isUcFirst($value) {return self::$UCFIRST === $value;}

	/**
	 * @used-by Df_Admin_Config_Font::isUcFirst()
	 * @param bool $value
	 * @return bool
	 */
	static function isUcWords($value) {return self::$UCWORDS === $value;}

	/** @var string */
	static $LOWERCASE = 'lowercase';
	/** @var string */
	static $UCFIRST = 'ucfirst';
	/** @var string */
	static $UCWORDS = 'ucwords';
	/** @var string */
	static $UPPERCASE = 'uppercase';
}