<?php
use Df\Core\Exception as DFE;
use Df\Sentry\Client as Sentry;
use Exception as E;
use Magento\Framework\DataObject;
use Magento\User\Model\User;
/**
 * 2016-12-22
 * В качестве $m можно передавать:
 * 1) Имя модуля. Например: «A_B».
 * 2) Имя класса. Например: «A\B\C».
 * 3) Объект. Сводится к случаю 2 посредством @see get_class()
 * 4) null. Это равноценно передаче модуля «Df_Core».
 * @used-by df_log()
 * @used-by dfe_modules_log()
 * @used-by dfp_report()
 * @used-by \Df\API\Client::_p()
 * @used-by \Df\Payment\Method::action()
 * @used-by \Df\Payment\W\Action::execute()
 * @used-by \Df\Payment\W\Action::ignoredLog()
 * @used-by \Df\Payment\W\Handler::log()
 * @used-by \Dfe\CheckoutCom\Handler::p()
 * @used-by \Dfe\CheckoutCom\Method::leh()
 * @used-by \Dfe\Dynamics365\API\Facade::p()
 * @used-by \Dfe\TwoCheckout\Handler::p()
 * @param string|object|null $m
 * @param DataObject|mixed[]|mixed|E $v
 * @param array(string => mixed) $context [optional]
 */
function df_sentry($m, $v, array $context = []) {
	/** @var string[] $domainsToSkip */ static $domainsToSkip = ['pumpunderwear.com', 'sanasafinaz.com'];
	if ($v instanceof E || !in_array(df_domain_current(), $domainsToSkip)) {
		$m = df_sentry_module($m);
		static $d; /** @var array(string => mixed) $d */
		$d = $d ?: [
			/**
			 * 2016-12-23
			 * The name of the transaction (or culprit) which caused this exception.
			 * For example, in in a web app, this might be the route name: /welcome/
			 * https://docs.sentry.io/clientdev/attributes/#optional-attributes
			 * Мне удобно здесь видеть домен магазина.
			 */
			//'culprit' => df_domain_current()
			// 2016-22-22
			// https://docs.sentry.io/clients/php/usage/#optional-attributes
			'extra' => []
			/**
			 * 2016-12-25
			 * Чтобы события разных магазинов не группировались вместе.
			 * https://docs.sentry.io/learn/rollups/#customize-grouping-with-fingerprints
			 * 2017-03-15
			 * Раньше здесь стоял код: 'fingerprint' => ['{{ default }}', df_domain_current()]
			 * https://github.com/mage2pro/core/blob/2.2.0/Sentry/lib/main.php#L38
			 * При этом коде уже игнорируемые события появлялись в журнале снова и не снова.
			 * Поэтому я решил отныне не использовать {{ default }},
			 * а строить fingerprint полностью самостоятельно.
			 *
			 * Осознанно не включаю в fingerprint текещий адрес запроса HTTP,
			 * потому что он может содержать всякие уникальные параметры в конце, например:
			 * https://<domain>/us/rest/us/V1/dfe-stripe/fab9c9a3bb3e745ca94eaeb7128692c9/place-order
			 *
			 * 2017-04-03
			 * Раньше в fingerprint включалось ещё:
			 * df_is_cli() ? dfa_hash(df_cli_argv()) : (df_is_rest() ? df_rest_action() : df_action_name())
			 * Решил больше это не включать: пока нет в этом необходимости.
			 */
			,'fingerprint' => [
				df_core_version(),df_domain_current(),df_magento_version(),df_package_version($m),df_store_code()
			]
		];
		// 2017-01-09
		if ($v instanceof DFE) {
			$context = df_extend($context, $v->sentryContext());
		}
		$context = df_extend($d, $context);
		if ($v instanceof E) {
			// 2016-12-22
			// https://docs.sentry.io/clients/php/usage/#reporting-exceptions
			df_sentry_m($m)->captureException($v, $context);
		}
		else {
			$v = df_dump($v);
			// 2016-12-22
			// https://docs.sentry.io/clients/php/usage/#reporting-other-errors
			df_sentry_m($m)->captureMessage($v, [], [
				// 2017-04-16
				// Добавляем заголовок события к «fingerprint», потому что иначе сообщения с разными заголовками
				// (например: «Robokassa: action» и «[Robokassa] request») будут сливаться вместе.
				'fingerprint' => array_merge(dfa($context, 'fingerprint', []), [$v])
				/**
				 * 2016-12-23
				 * «The record severity. Defaults to error.»
				 * https://docs.sentry.io/clientdev/attributes/#optional-attributes
				 *
				 * @used-by \\Df\Sentry\Client::capture():
				 *	if (!isset($data['level'])) {
				 *		$data['level'] = self::ERROR;
				 *	}
				 * https://github.com/mage2pro/sentry/blob/1.6.4/lib/Raven/Client.php#L640-L642
				 * При использовании @see \\Df\Sentry\Client::DEBUG у сообщения в списке сообщений
				 * в интерфейсе Sentry не будет никакой метки.
				 * При использовании @see \\Df\Sentry\Client::INFO у сообщения в списке сообщений
				 * в интерфейсе Sentry будет синяя метка «Info».
				 */
				,'level' => Sentry::DEBUG
			] + $context);
		}
	}
}

/**
 * 2017-01-10
 * Поддерживаем 2 синтаксиса: df_sentry_extra(['a' => 'b']) и df_sentry_extra('a', 'b').
 * В качестве $m можно передавать:
 * 1) Имя модуля. Например: «A_B».
 * 2) Имя класса. Например: «A\B\C».
 * 3) Объект. Сводится к случаю 2 посредством @see get_class()
 * 4) null. Это равноценно передаче модуля «Df_Core».
 * @used-by \Df\GingerPaymentsBase\Init\Action::req()
 * @used-by \Df\Payment\W\Reader::error()
 * @used-by \Df\StripeClone\Method::charge()
 * @used-by \Df\StripeClone\Method::chargeNew()
 * @used-by \Dfe\AlphaCommerceHub\Method::charge()
 * @used-by \Dfe\Qiwi\Init\Action::req()
 * @used-by \Dfe\Stripe\Init\Action::redirectUrl()
 * @used-by \Dfe\TwoCheckout\Method::charge()
 * @param string|object|null $m
 * @param array ...$a
 */
function df_sentry_extra($m, ...$a) {df_sentry_m($m)->extra_context(
	!$a ? $a : (is_array($a[0]) ? $a[0] : [$a[0] => $a[1]])
);}

/**
 * 2016-12-22
 * @used-by df_sentry()
 * @used-by df_sentry_extra()
 * @used-by df_sentry_tags()
 * @used-by \Df\Payment\W\Handler::log()
 * @used-by \Dfe\CheckoutCom\Controller\Index\Index::webhook()
 * В качестве $m можно передавать:
 * 1) Имя модуля. Например: «A_B».
 * 2) Имя класса. Например: «A\B\C».
 * 3) Объект. Сводится к случаю 2 посредством @see get_class()
 * @param string|object|null $m
 * @return Sentry
 */
function df_sentry_m($m) {return dfcf(function($m) {
	$result = null; /** @var Sentry $result */
	/** @var array(string => mixed) $a */
	/** @var array(string => string)|null $sa */
	if (($a = df_module_json($m, 'df', false)) && ($sa = dfa($a, 'sentry'))) {
		$result = new Sentry("https://{$sa['key1']}:{$sa['key2']}@sentry.io/{$sa['id']}", [
			/**
			 * 2016-12-22
			 * Не используем стандартные префиксы: @see \\Df\Sentry\Client::getDefaultPrefixes()
			 * потому что они включают себя весь @see get_include_path()
			 * в том числе и папки внутри Magento (например: lib\internal),
			 * и тогда, например, файл типа
			 * C:\work\mage2.pro\store\lib\internal\Magento\Framework\App\ErrorHandler.php
			 * будет обрезан как Magento\Framework\App\ErrorHandler.php
			 */
			'prefixes' => [BP . DIRECTORY_SEPARATOR]
			/**
			 * 2016-12-25
			 * Чтобы не применялся @see \Df\Sentry\SanitizeDataProcessor
			 */
			,'processors' => []
		]);
		/**
		 * 2016-12-22
		 * «The root path to your application code.»
		 * https://docs.sentry.io/clients/php/config/#available-settings
		 * У Airbrake для Ruby есть аналогичный параметр — «root_directory»:
		 * https://github.com/airbrake/airbrake-ruby/blob/v1.6.0/README.md#root_directory
		 */
		$result->setAppPath(BP);
		// 2016-12-23
		// https://docs.sentry.io/clientdev/interfaces/user/
		/** @var User|null $u */
		$result->user_context((df_is_cli() ? ['username' => df_cli_user()] : (
			($u = df_backend_user()) ? [
				'email' => $u->getEmail(), 'id' => $u->getId(), 'username' => $u->getUserName()
			] : (!df_is_frontend() ? [] : (($c = df_customer())
				? ['email' => $c->getEmail(), 'id' => $c->getId(), 'username' => $c->getName()]
				: ['id' => df_customer_session()->getSessionId()]
			))
		)) + ['ip_address' => df_visitor_ip()], false);
		$result->tags_context([
			'Core' => df_core_version()
			,'Magento' => df_magento_version()
			,'MySQL' => df_db_version()
			,'PHP' => phpversion()
		]);
	}
	return $result ?: ($m !== 'Df_Core' ? df_sentry_m('Df_Core') : 
		df_error('Sentry settings for Df_Core are absent.')
	);
}, [df_sentry_module($m)]);}

/**
 * 2017-03-15
 * @used-by df_sentry()
 * @used-by df_sentry_m()
 * @param string|object|null $m [optional]
 * @return string
 */
function df_sentry_module($m = null) {return !$m ? 'Df_Core' : df_module_name($m);}

/**
 * 2017-01-10
 * В качестве $m можно передавать:
 * 1) Имя модуля. Например: «A_B».
 * 2) Имя класса. Например: «A\B\C».
 * 3) Объект. Сводится к случаю 2 посредством @see get_class()
 * 4) null. Это равноценно передаче модуля «Df_Core».  
 * @used-by \Df\Payment\Method::action()
 * @param string|object|null $m
 * @param array(string => mixed) $a
 */
function df_sentry_tags($m, array $a) {df_sentry_m($m)->tags_context($a);}