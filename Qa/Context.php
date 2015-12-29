<?php
/**
 * Этот класс позволяет добавлять дополнительную информацию в заголовок отчёта о сбое.
 * Стандартный заголовок отчёта о сбое выглядит так:
	URL:                http://localhost.com:831/df-1c/cml2/index/?type=catalog&mode=import&filename=offers___6dbd4e7d-7612-4427-8ebe-68cdb712e8d2.xml&
	Версия Magento:     2.36.8 (1.9.0.1)
	Версия PHP:         5.5.12
	Время:              2014-08-11 18:30:07 MSK
	***********************************
	Тип цен «Розничная», указанный администратором как основной, отсутствует в 1С:Управление торговлей.
	***********************************
 *
 * Вызов
		\Df\Qa\Context::s()->addItem('Схема CommerceML', '2.0.8');
 * добавит к заголовку отчёта о сбое версию схемы CommerceML, и заголовок будет выглядеть так:
	URL:                http://localhost.com:831/df-1c/cml2/index/?type=catalog&mode=import&filename=offers___6dbd4e7d-7612-4427-8ebe-68cdb712e8d2.xml&
	Версия Magento:     2.36.8 (1.9.0.1)
	Версия PHP:         5.5.12
	Время:              2014-08-11 18:30:07 MSK
	Схема CommerceML:   2.0.8
	***********************************
	Тип цен «Розничная», указанный администратором как основной, отсутствует в 1С:Управление торговлей.
	***********************************
 *
 * @see Df_1C_Cml2_Action_Catalog_Import::_process()
 */
namespace Df\Qa;
class Context {
	/**
	 * @used-by df_context()
	 * @param string $label
	 * @param string $value
	 * @param int $weight [optional]
	 * @param array(array(string => string|int)) $params $params
	 */
	public static function add($label, $value, $weight = 0) {
		self::$_items[$label] = [self::$VALUE => $value, self::$WEIGHT => $weight];
	}

	/**
	 * @used-by \Df\Qa\Message::report()
	 * @return string
	 */
	public static function render() {
		/** @var string $result */
		// 2015-09-02
		// Warning: max(): Array must contain at least one element
		if (!self::$_items) {
			$result = '';
		}
		else {
			/** @uses \Df\Qa\Context::sort() */
			uasort(self::$_items, [__CLASS__, 'sort']);
			/** @var int $padSize */
			$padSize = 2 + max(array_map('mb_strlen', array_keys(self::$_items)));
			/** @var string[] $rows */
			$rows = [];
			foreach (self::$_items as $label => $item) {
				/** @var string $label */
				/** @var array(string => string|int) $item */
				$rows[]= df_pad($label . ':', $padSize) . $item[self::$VALUE];
			}
			$result = df_cc_n($rows);
		}
		return $result;
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @used-by render()
	 * @used-by uasort()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/khmlf
	 * @param array(string => string|int) $a
	 * @param array(string => string|int) $b
	 * @return int
	 */
	private static function sort(array $a, array $b) {return $a[self::$WEIGHT] - $b[self::$WEIGHT];}

	/** @var array(string => string) */
	private static $_items = [];

	/** @var string */
	private static $VALUE = 'value';
	/** @var string */
	private static $WEIGHT = 'weight';
}