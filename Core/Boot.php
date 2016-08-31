<?php
namespace Df\Core;
class Boot {
	/**
	 * @used-by registration.php
	 * @return void
	 */
	public static function run() {
		if (!self::$_done) {
			self::init();
			array_map(['\Df\Core\Lib', 'load'], [
				'Checkout', 'Config', 'Customer', 'Directory',
				'Eav', 'Framework', 'Payment', 'Quote', 'Sales', 'Translation', 'Xml'
			]);
			self::$_done = true;
		}
	}

	/**
	 * Этот метод содержит код инициализации, который должен выполняться как можно раньше:
	 * вне зависимости, был ли уже инициализирован текущий магазин системы.
	 * Соответственно, в этом методе мы не можем работать с объектами-магазинами.
	 * В том числе, в частости, не можем прочитывать настройки текущего магазина.
	 * @used-by run()
	 * @return void
	 */
	private static function init() {
		/**
		 * На серверах Debian (в том числе официально рекомендуемых)
		 * устаревшие файлы сессий никогда не удаляются
		 * и накапливаются в виде мусора в папке var/session:
		 * http://php.net/manual/session.configuration.php#115842
		 * https://www.phpro.be/news/magento-garbage-collection
		 *
		 * Так происходит потому, что официальный дистрибутив PHP для Debian
		 * содержит для параметра session.gc_probability нестандартное значение 0
		 * вместо стандартного значения 1.
		 *
		 * Вероятность запуска очистки сессий равна session.gc_probability / session.gc_divisor.
		 * При session.gc_probability = 0 очистка сессий никогда не зппустится.
		 * Debian так делает намеренно, потому что хранит сессии в папке с очень строгим доступом,
		 * где интерпретатор PHP всё равно не имеет прав удалять файлы,
		 * и Debian удаляет их самостоятельно посредством нестандартного скрипта,
		 * запускаемого планировщиком задач.
		 * Однако этот нестандартный скрипт Debian
		 * работает только со стандартной папкой хранения сессий
		 * и ничего не знает про папку Magento var/session.
		 * По этой причине файлы сессий Magento на Debian никогда не удаляются.
		 *
		 * Чтобы устранить эту проблему, возвращаем для параметра session.gc_probability
		 * значение 1.
		 *
		 * Обратите внимание,
		 * что интерпретатор PHP, похоже, выполняет очистку сессий при вызове @see session_start().
		 * Поэтому важно, что в данном случае наш код гарантированно исполняется
		 * заведомо раньше вызова @see session_start()
		 * в методе @see Mage_Core_Model_Session_Abstract_Varien::start(),
		 * я проверял.
		 */
		ini_set('session.gc_probability', 1);
		ini_set('session.gc_divisor', 100);
		Lib::load('Core');
		/** @uses Df_Qa_Message_Failure_Error::check() */
		register_shutdown_function(['\Df\Qa\Message\Failure\Error', 'check']);
		if (!ini_get('date.timezone')) {
			/**
			 * Временно устанавливаем в качестве часового пояса московский.
			 * Часовой пояс надо установить,
			 * потому что иначе некоторые стандатные для PHP функции работы со временем
			 * могут приводить к сбою типа E_WARNING.
			 *
			 * Реальный часовой пояс мы затем установим в методе @see init2()
			 * Сразу установить реальный мы не можем,
			 * потому что это требует предварительной инициализации текущего магазина системы,
			 * а при выполнении @see init1() текущий магазин системы может быть ещё не инициализирован.
			 */
			date_default_timezone_set('Europe/Moscow');
		}
	}

	/** @var bool */
	private static $_done = false;
}