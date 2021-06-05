<?php
/**
 * Этот класс позволяет добавлять дополнительную информацию в заголовок отчёта о сбое.
 * Стандартный заголовок отчёта о сбое выглядит так:
 *	URL:                http://localhost.com:831/df-1c/cml2/index/?type=catalog&mode=import&filename=offers___6dbd4e7d-7612-4427-8ebe-68cdb712e8d2.xml&
 *	Версия Magento:     2.36.8 (1.9.0.1)
 *	Версия PHP:         5.5.12
 *	Время:              2014-08-11 18:30:07 MSK
 *	***********************************
 *	Тип цен «Розничная», указанный администратором как основной, отсутствует в 1С:Управление торговлей.
 *	***********************************
 *
 * Вызов
 *		\Df\Qa\Context::s()->add('Схема CommerceML', '2.0.8');
 * добавит к заголовку отчёта о сбое версию схемы CommerceML, и заголовок будет выглядеть так:
 *	URL:                http://localhost.com:831/df-1c/cml2/index/?type=catalog&mode=import&filename=offers___6dbd4e7d-7612-4427-8ebe-68cdb712e8d2.xml&
 *	Версия Magento:     2.36.8 (1.9.0.1)
 *	Версия PHP:         5.5.12
 *	Время:              2014-08-11 18:30:07 MSK
 *	Схема CommerceML:   2.0.8
 *	***********************************
 *	Тип цен «Розничная», указанный администратором как основной, отсутствует в 1С:Управление торговлей.
 *	***********************************
 *
 * @see Df_1C_Cml2_Action_Catalog_Import::_process()
 */
namespace Df\Qa;
final class Context {
	/**
	 * @param string $k
	 * @param string $v
	 * @param int $weight [optional]
	 * @param array(array(string => string|int)) $params $params
	 */
	static function add($k, $v, $weight = 0) {self::$_items[$k] = [self::$VALUE => $v, self::$WEIGHT => $weight];}

	/**
	 * 2020-09-25
	 * @used-by df_log_l()
	 * @used-by \Df\Qa\Message\Failure\Error::preface()
	 * @return array(string => mixed)
	 */
	static function base() {return [
		['mage2pro/core' => df_core_version(), 'Magento' => df_magento_version(), 'PHP' => phpversion()]
		+ (df_is_cli()
			? ['Command' => df_cli_cmd()]
			: ([
				# 2021-04-18 "Include the visitor's IP address to Mage2.PRO reports": https://github.com/mage2pro/core/issues/151
				'IP Address' => df_visitor_ip()
				,'Referer' => df_referer()
				# 2021-06-05 "Log the request method": https://github.com/mage2pro/core/issues/154
				,'Request Method' => df_request_method()
				,'URL' => df_current_url()
				# 2021-04-18 "Include the visitor's `User-Agent` to Mage2.PRO reports":
				# https://github.com/mage2pro/core/issues/152
				,'User-Agent' => df_request_ua()
			] + (!df_request_o()->isPost() ? [] : ['Post' => $_POST]))
		)
	];}

	/**
	 * @used-by \Df\Qa\Message::report()
	 * @return string
	 */
	static function render() {/** @var string $r */
		# 2015-09-02 Warning: max(): Array must contain at least one element.
		if (!self::$_items) {
			$r = '';
		}
		else {
			uasort(self::$_items, [__CLASS__, 'sort']); /** @uses Context::sort() */
			$padSize = 2 + max(array_map('mb_strlen', array_keys(self::$_items))); /** @var int $padSize */
			$r = df_kv(df_each(self::$_items, self::$VALUE), $padSize);
		}
		return $r;
	}

	/**
	 * Этот метод может быть приватным несмотря на использование его как callable,
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