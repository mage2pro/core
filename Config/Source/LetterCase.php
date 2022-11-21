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
		$vv = [self::$_DEFAULT, self::UCFIRST, self::UCWORDS, self::UPPERCASE, self::LOWERCASE]; /** @var string[] $vv */
		/** @var string[] $ll */
		$ll = ['As Is', 'Uppercase first letter', 'Uppercase Each Word\'s First Letter', 'UPPERCASE', 'lowercase'];
		/** @var string|null $s */
		return array_combine($vv, !($s = $this->f('dfSample')) ? $ll :
			array_map(function(string $v, string $l) use($s):string {return df_desc(self::apply($s, $v), $l);}, $vv, $ll)
		);
	}

	/**
	 * @used-by self::apply()
	 * @used-by self::map()
	 * @used-by \Df\Typography\Font::css()
	 */
	const LOWERCASE = 'lowercase';
	/**
	 * @used-by self::apply()
	 * @used-by self::map()
	 * @used-by \Df\Typography\Font::css()
	 */
	const UCFIRST = 'ucfirst';
	/**
	 * @used-by self::apply()
	 * @used-by self::map()
	 * @used-by \Df\Typography\Font::css()
	 */
	const UCWORDS = 'ucwords';
	/**
	 * @used-by self::apply()
	 * @used-by self::map()
	 * @used-by \Df\Typography\Font::css()
	 */
	const UPPERCASE = 'uppercase';

	/** @used-by map() */
	private static function apply(string $s, string $format):string {/** @var string $r */
		switch($format) {
			case self::LOWERCASE:
				$r = mb_strtolower($s);
				break;
			case self::UPPERCASE:
				$r = mb_strtoupper($s);
				break;
			case self::UCFIRST:
				$r = df_ucfirst(mb_strtolower(df_trim($s)));
				break;
			case self::UCWORDS:
				$r = df_ucwords($s);
				break;
			default:
				$r = $s;
		}
		# Убрал валидацию результата намеренно: сам метод безобиден,
		# и даже если он как-то неправильно будет работать — ничего страшного.
		# Пока метод дал сбой только один раз, в магазине laap.ru
		# при форматировании заголовков административной таблицы товаров
		# (видимо, сбой произошёл из-за влияния некоего стороннего модуля).
		return $r;
	}

	/**
	 * @used-by self::css()
	 * @used-by self::map()
	 */
	private static $_DEFAULT = 'default';
}