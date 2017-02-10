<?php
use Df\Core\Exception as DFE;
use Df\Sentry\Client as Sentry;
use Exception as E;
use Magento\Customer\Model\Customer;
use Magento\Framework\DataObject;
use Magento\User\Model\User;
/**
 * 2016-12-22
 * В качестве $m можно передавать:
 * 1) Имя модуля. Например: «A_B».
 * 2) Имя класса. Например: «A\B\C».
 * 3) Объект. Сводится к случаю 2 посредством @see get_class()
 * 4) null. Это равноценно передаче модуля «Df_Core».
 * @param string|object|null $m
 * @param DataObject|mixed[]|mixed|E $v
 * @param array(string => mixed) $context [optional]
 */
function df_sentry($m, $v, array $context = []) {
	if (true || !df_my_local()) {
		/** @var array(string => mixed) $d */
		static $d;
		$d = $d ?: [
			/**
			 * 2016-12-23
			 * The name of the transaction (or culprit) which caused this exception.
			 * For example, in in a web app, this might be the route name: /welcome/
			 * https://docs.sentry.io/clientdev/attributes/#optional-attributes
			 * Мне удобно здесь видеть домен магазина.
			 */
			//'culprit' => df_domain()
			// 2016-22-22
			// https://docs.sentry.io/clients/php/usage/#optional-attributes
			'extra' => []
			// 2016-12-25
			// Чтобы события разных магазинов не группировались вместе.
			// https://docs.sentry.io/learn/rollups/#customize-grouping-with-fingerprints
			,'fingerprint' => ['{{ default }}', df_domain()]
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
				/**
				 * 2016-12-23
				 * «The record severity. Defaults to error.»
				 * https://docs.sentry.io/clientdev/attributes/#optional-attributes
				 *
				 * @used-by \\Df\Sentry\Client::capture():
					if (!isset($data['level'])) {
						$data['level'] = self::ERROR;
					}
				 * https://github.com/mage2pro/sentry/blob/1.6.4/lib/Raven/Client.php#L640-L642
				 * При использовании @see \\Df\Sentry\Client::DEBUG у сообщения в списке сообщений
				 * в интерфейсе Sentry не будет никакой метки.
				 * При использовании @see \\Df\Sentry\Client::INFO у сообщения в списке сообщений
				 * в интерфейсе Sentry будет синяя метка «Info».
				 */
				'level' => Sentry::DEBUG
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
 * @used-by \Df\Payment\Webhook::log()
 * @used-by \Dfe\CheckoutCom\Controller\Index\Index::webhook()
 * В качестве $m можно передавать:
 * 1) Имя модуля. Например: «A_B».
 * 2) Имя класса. Например: «A\B\C».
 * 3) Объект. Сводится к случаю 2 посредством @see get_class()
 * @param string|object $m
 * @return Sentry
 */
function df_sentry_m($m) {return dfcf(function($m) {
	/** @var Sentry $result */
	$result = null;
	/** @var array(string => mixed) $a */
	$a = df_module_json($m, 'df', false);
	if ($a) {
		/** @var array(string => string)|null $sa */
		$sa = dfa($a, 'sentry');
		if ($sa) {
			$result = new Sentry(
				"https://{$sa['key1']}:{$sa['key2']}@sentry.io/{$sa['id']}"
				,[
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
				]
			);
			/**
			 * 2016-12-22
			 * «The root path to your application code.»
			 * https://docs.sentry.io/clients/php/config/#available-settings
			 * У Airbrake для Ruby есть аналогичный параметр — «root_directory»:
			 * https://github.com/airbrake/airbrake-ruby/blob/v1.6.0/README.md#root_directory
			 */
			$result->setAppPath(BP);
			/**
			 * 2016-12-23
			 * https://docs.sentry.io/clientdev/interfaces/user/
			 */
			/** @var array(string => string) $specific */
			$specific = [];
			if (df_is_cli()) {
				$specific = ['username' => df_cli_user()];
			}
			else if (df_is_backend()) {
				/** @var User $u */
				$u = df_backend_user();
				$specific = [
					'email' => $u->getEmail(), 'id' => $u->getId(), 'username' => $u->getUserName()
				];
			}
			else if (df_is_frontend()) {
				/** @var Customer $c */
				$c = df_customer();
				$specific =
					!$c
					? ['id' => df_customer_session()->getSessionId()]
					: ['email' => $c->getEmail(), 'id' => $c->getId(), 'username' => $c->getName()]
				;
			}
			$result->user_context(['ip_address' => df_visitor_ip()] + $specific, false);
			$result->tags_context([
				'Core' => df_core_version()
				,'Magento' => df_magento_version()
				,'MySQL' => df_db_version()
				,'PHP' => phpversion()
			]);
		}
	}
	return $result ?: ($m !== 'Df_Core' ? df_sentry_m('Df_Core') :
		df_error('Sentry settings for Df_Core are absent.')
	);
}, [df_module_name($m)]);}

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