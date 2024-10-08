<?php
use Df\Core\RAM;
/**
 * 2016-09-04
 * Не используем решения типа такого: http://stackoverflow.com/a/34711505
 * потому что они возвращают @see Closure, и тогда кэшируемая функция становится переменной,
 * что неудобно (неунифицировано и засоряет глобальную область видимости переменными).
 * @param Closure $f
 * Используем именно array $a = [], а не ...$a,
 * чтобы кэшируемая функция не перечисляла свои аргументы при передачи их сюда,
 * а просто вызывала @see func_get_args()
 * 2016-11-01
 * Будьте осторожны при передаче в функцию $f параметров посредством use:
 * эти параметры не будут участвовать в расчёте ключа кэша.
 * 2017-01-01
 * Мы не можем кэшировать Closure самодостаточно, в отрыве от класса,
 * потому что Closure может обращаться к полям и методам класса через self и static.
 * 2017-01-01
 * При $unique = false Closure $f будет участвовать в расчёте ключа кэширования.
 * Это нужно в 2 ситуациях:
 * 1) Если Ваша функция содержит несколько вызовов dfc() для разных Closure.
 * 2) В случаях, подобных @see dfac(), когда Closure передаётся в функцию в качестве параметра,
 * и поэтому Closure не уникальна.
 * 2017-08-11 The cache tags. A usage example: @see df_cache_get_simple()
 * 2017-01-02 Задавайте параметр $offset в том случае, когда dfc() вызывается опосредованно. Например, так делает @see dfac().
 * @see df_no_rec()
 * @see dfac()
 * @used-by df_ar()
 * @used-by df_are_customers_global()
 * @used-by df_cache_get_simple()
 * @used-by df_category_children_map()
 * @used-by df_cdata_m()
 * @used-by df_cli_user()
 * @used-by df_con_s()
 * @used-by df_core_version()
 * @used-by df_countries_allowed()
 * @used-by df_countries_options()
 * @used-by df_country()
 * @used-by df_currency()
 * @used-by df_customer_is_new()
 * @used-by df_currencies_ctn()
 * @used-by df_currencies_options()
 * @used-by df_currency_by_country_c()
 * @used-by df_currency_nums()
 * @used-by df_days_off()
 * @used-by df_db_version()
 * @used-by df_domain_current()
 * @used-by df_geo()
 * @used-by df_google_init_service_account()
 * @used-by df_is_windows()
 * @used-by df_locale()
 * @used-by df_magento_version()
 * @used-by df_magento_version_remote()
 * @used-by df_module_file_read()
 * @used-by df_module_name()
 * @used-by df_modules_my()
 * @used-by df_modules_p()
 * @used-by df_msi_website2stockId()
 * @used-by df_mvars()
 * @used-by df_my_local()
 * @used-by df_o()
 * @used-by df_oq_customer_name()
 * @used-by df_order_by_payment()
 * @used-by df_primary_key()
 * @used-by df_product_att_options()
 * @used-by df_product_images_path_rel()
 * @used-by df_sales_seq_meta()
 * @used-by df_sentry_m()
 * @used-by df_store_codes()
 * @used-by df_table()
 * @used-by df_trans_by_payment()
 * @used-by df_transx()
 * @used-by df_webserver()
 * @used-by dfac()
 * @used-by dfe_modules_info()
 * @used-by dfe_moip_phone()
 * @used-by dfe_packages()
 * @used-by dfe_portal_plugins()
 * @used-by dfe_stripe_source()
 * @used-by dfp_due()
 * @used-by dfp_methods()
 * @used-by dfpm()
 * @used-by dfpm_c()
 * @used-by dfpm_code()
 * @used-by dfs_con()
 * @used-by Df\API\Facade::s()
 * @used-by Df\Config\Settings::_a()
 * @used-by Df\Config\Settings::convention()
 * @used-by Df\Config\Settings::s()
 * @used-by Df\Config\Source::s()
 * @used-by Df\Config\Source\WaitPeriodType::calculate()
 * @used-by Df\Core\R\ConT::generic()
 * @used-by Df\Core\Session::s()
 * @used-by Df\Core\Text\Regex::getErrorCodeMap()
 * @used-by Df\Core\Visitor::sp()
 * @used-by Df\Directory\FE\Currency::map()
 * @used-by Df\Framework\Log\Handler\Info::lb()
 * @used-by Df\Framework\Request::extraKeysRaw()
 * @used-by Df\OAuth\App::state()
 * @used-by Df\Payment\Choice::f()
 * @used-by Df\Payment\Currency::f()
 * @used-by Df\Payment\Facade::s()
 * @used-by Df\Payment\Init\Action::sg()
 * @used-by Df\Payment\Method::codeS()
 * @used-by Df\Payment\Method::sg()
 * @used-by Df\Payment\TID::s()
 * @used-by Df\Payment\TM::s()
 * @used-by Df\Payment\Url::f()
 * @used-by Df\Payment\W\F::s()
 * @used-by Df\Qa\Trace\Formatter::p()
 * @used-by Df\Shipping\Method::codeS()
 * @used-by Df\Shipping\Method::sg()
 * @used-by Df\Sso\Button::sModule()
 * @used-by Df\Sso\Css::isAccConfirmation()
 * @used-by Df\Sso\Css::isRegCompletion()
 * @used-by Df\StripeClone\CardFormatter::s()
 * @used-by Df\StripeClone\P\Charge::sn()
 * @used-by Dfe\AllPay\W\Event::time()
 * @used-by Dfe\Color\Image::optsM()
 * @used-by Dfe\Color\Image::palette()
 * @used-by Dfe\Facebook\I::init()
 * @used-by Dfe\Robokassa\Api\Options::map()
 * @used-by Dfe\Robokassa\Api\Options::p()
 * @used-by Dfe\Sift\Payload\Promotion\Discount::desc()
 * @used-by Dfe\Stripe\Facade\Token::trimmed()
 * @return mixed
 */
function dfcf(Closure $f, array $a = [], array $tags = [], bool $unique = true, int $offset = 0) {
	/**
	 * 2021-10-05
	 * I do not use @see df_bt() to make the implementation faster. An implementation via df_bt() is:
	 * 		$b = df_bt(0, 2 + $offset)[1 + $offset];
	 */
	$b = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2 + $offset)[1 + $offset]; /** @var array(string => string) $b */
	/**
	 * 2016-09-04
	 * Когда мы кэшируем статический метод, то ключ «class» присутствует,
	 * а когда функцию — то отсутствует: https://3v4l.org/ehu4O
	 * Ради ускорения не используем свои функции dfa() и df_cc().
	 * 2016-11-24
	 * Когда мы кэшируем статический метод, то значением ключа «class» является не вызванный класс,
	 * а тот класс, где определён кэшируемый метод: https://3v4l.org/OM5sD
	 * Поэтому все потомки класса с кэшированным методом будут разделять общий кэш.
	 * Поэтому если Вы хотите, чтобы потомки имели индивидуальный кэш,
	 * то учитывайте это при вызове dfcf.
	 * Например, пишите не так:
	 *		private static function sModule() {return dfcf(function() {return
	 *			S::convention(static::class)
	 *		;});}
	 * а так:
	 *		private static function sModule() {return dfcf(function($c) {return
	 *			S::convention($c)
	 *		;}, [static::class]);}
	 *
	 * У нас нет возможности вычислять имя вызвавшего нас класса автоматически:
	 * как уже было сказано выше, debug_backtrace() возвращает только имя класса, где метод был объявлен,
	 * а не вызванного класса.
	 * А get_called_class() мы здесь не можем вызывать вовсе:
	 * «Warning: get_called_class() called from outside a class»
	 * https://3v4l.org/ioT7c
	 * 2022-11-17 @see df_cc_method()
	 */
	$k = (!isset($b['class']) ? null : $b['class'] . '::') . $b['function']
		. (!$a ? null : '--' . df_hash_a($a))
		. ($unique ? null : '--' . spl_object_hash($f))
	; /** @var string $k */
	$r = df_ram(); /** @var RAM $r */
	# 2017-01-12
	# The following code will return `3`:
	# 		$a = function($a, $b) {return $a + $b;};
	# 		$b = [1, 2];
	# 		echo $a(...$b);
	# https://3v4l.org/0shto
	return $r->exists($k) ? $r->get($k) : $r->set($k, $f(...$a), $tags);
}