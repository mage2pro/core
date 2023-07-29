<?php
use Df\Core\Exception as DFE;
use Df\Sentry\Client as Sentry;
use Exception as E;
use Magento\Framework\DataObject as _DO;
use Magento\User\Model\User;
/**
 * 2016-12-22
 * $m could be:
 * 		1) A module name: «A_B»
 * 		2) A class name: «A\B\C».
 * 		3) An object: it comes down to the case 2 via @see get_class()
 * 		4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * @used-by df_log()
 * @used-by dfe_modules_log()
 * @used-by dfp_report()
 * @used-by \Df\API\Client::_p()
 * @used-by \Df\Payment\Method::action()
 * @used-by \Df\Payment\W\Action::ignoredLog()
 * @used-by \Df\Payment\W\Handler::log()
 * @used-by \Dfe\CheckoutCom\Handler::p()
 * @used-by \Dfe\CheckoutCom\Method::leh()
 * @used-by \Dfe\Dynamics365\API\Facade::p()
 * @used-by \Dfe\TwoCheckout\Handler::p()
 * @used-by \Inkifi\Pwinty\AvailableForDownload::_p()
 * @used-by \Inkifi\Pwinty\Controller\Index\Index::execute()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\RenewMediaclipToken::execute()
 * @param string|object|null $m
 * @param _DO|mixed[]|mixed|E $v
 * @param array(string => mixed) $extra [optional]
 */
function df_sentry($m, $v, array $extra = []):void {
	/** @var string[] $domainsToSkip */
	static $domainsToSkip = ['pumpunderwear.com', 'quanticlo.com', 'sanasafinaz.com'];
	if ($v instanceof E || !in_array(df_domain_current(), $domainsToSkip)) {
        # 2020-09-09, 2023-07-25 We need `df_caller_module(1)` (I checked it) because it is nested inside `df_sentry_module()`.
		$m = df_sentry_module($m ?: df_caller_module(1));
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
			# 2016-22-22
			# https://docs.sentry.io/clients/php/usage/#optional-attributes
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
			 * df_is_cli() ? df_hash_a(df_cli_argv()) : (df_is_rest() ? df_rest_action() : df_action_name())
			 * Решил больше это не включать: пока нет в этом необходимости.
			 */
			,'fingerprint' => [
				df_core_version(), df_domain_current(), df_magento_version(), df_package_version($m), df_store_code()
			]
		];
		# 2023-07-25
		# "Change the 3rd argument of `df_sentry` from `$context` to `$extra`": https://github.com/mage2pro/core/issues/249
		$context = df_clean(['extra' => $extra]);
		# 2017-01-09
		if ($v instanceof DFE) {
			$context = dfa_merge_r($context, $v->sentryContext());
		}
		$context = dfa_merge_r($d, $context);
		if ($v instanceof E) {
			# 2016-12-22 https://docs.sentry.io/clients/php/usage/#reporting-exceptions
			df_sentry_m($m)->captureException($v, $context);
		}
		else {
			$v = df_dump($v);
			# 2016-12-22 https://docs.sentry.io/clients/php/usage/#reporting-other-errors
			df_sentry_m($m)->captureMessage($v, [
				# 2017-04-16
				# Добавляем заголовок события к «fingerprint», потому что иначе сообщения с разными заголовками
				# (например: «Robokassa: action» и «[Robokassa] request») будут сливаться вместе.
				'fingerprint' => array_merge(dfa($context, 'fingerprint', []), [$v])
				/**
				 * 2016-12-23
				 * «The record severity. Defaults to error.»
				 * https://docs.sentry.io/clientdev/attributes/#optional-attributes
				 *
				 * @used-by \Df\Sentry\Client::capture():
				 *	if (!isset($data['level'])) {
				 *		$data['level'] = self::ERROR;
				 *	}
				 * https://github.com/mage2pro/sentry/blob/1.6.4/lib/Raven/Client.php#L640-L642
				 * При использовании @see \Df\Sentry\Client::DEBUG у сообщения в списке сообщений
				 * в интерфейсе Sentry не будет никакой метки.
				 * При использовании @see \Df\Sentry\Client::INFO у сообщения в списке сообщений
				 * в интерфейсе Sentry будет синяя метка «Info».
				 */
				,'level' => Sentry::DEBUG
			] + $context);
		}
	}
}

/**
 * 2017-01-10
 * 1) It could be called as `df_sentry_extra(['a' => 'b'])` or df_sentry_extra('a', 'b').
 * 2) $m could be:
 * 2.1) A module name: «A_B»
 * 2.2) A class name: «A\B\C».
 * 2.3) An object: it comes down to the case 2 via @see get_class()
 * 2.4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * @used-by \Auctane\Api\Model\Action\Export::_getBillingInfo() (caremax.com.au, https://github.com/caremax-com-au/site/issues/1)
 * @used-by \Auctane\Api\Model\Action\Export::writeOrderXml() (caremax.com.au, https://github.com/caremax-com-au/site/issues/1)
 * @used-by \Dfe\GingerPaymentsBase\Init\Action::req()
 * @used-by \Df\Payment\W\Reader::error()
 * @used-by \Df\StripeClone\Method::charge()
 * @used-by \Df\StripeClone\Method::chargeNew()
 * @used-by \Dfe\AlphaCommerceHub\Method::charge()
 * @used-by \Dfe\Qiwi\Init\Action::req()
 * @used-by \Dfe\Stripe\Init\Action::redirectUrl()
 * @used-by \Dfe\TwoCheckout\Method::charge()
 * @used-by \Inkifi\Pwinty\Controller\Index\Index::execute()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\GetPriceEndpoint::execute()
 * @param string|object|null $m
 * @param mixed ...$v
 */
function df_sentry_extra($m, ...$v):void {df_sentry_m($m)->extra(!$v ? $v : (is_array($v[0]) ? $v[0] : [$v[0] => $v[1]]));}

/**
 * 2019-05-20
 * @used-by \Inkifi\Pwinty\AvailableForDownload::images()
 * @param mixed $v
 */
function df_sentry_extra_f($v):void {df_sentry_m(df_caller_c())->extra([df_caller_m() => $v]);}

/**
 * 2016-12-22
 * $m could be:
 * 		1) a module name: «A_B»
 * 		2) a class name: «A\B\C».
 * 		3) an object: it comes down to the case 2 via @see get_class()
 * 		4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * @used-by df_sentry()
 * @used-by df_sentry_extra()
 * @used-by df_sentry_extra_f()
 * @used-by df_sentry_m()
 * @used-by df_sentry_tags()
 * @used-by \Df\Payment\W\Handler::log()
 * @used-by \Dfe\CheckoutCom\Controller\Index\Index::webhook()
 * @param string|object|null $m
 */
function df_sentry_m($m):Sentry {return dfcf(function(string $m):Sentry {
	$r = null; /** @var Sentry $r */
	$isCore = 'Df_Core' === $m; /** @var bool $isCore */
	/** @var array(string => $r) $a */ /** @var array(string => string)|null $sa */
	if (($a = df_module_json($m, 'df', false)) && ($sa = dfa($a, 'sentry'))) {
		$r = new Sentry(intval($sa['id']), $sa['key1'], $sa['key2']);
		# 2016-12-23 https://docs.sentry.io/clientdev/interfaces/user
		/** @var User|null $u */
		$r->user((df_is_cli() ? ['username' => df_cli_user()] : (
			($u = df_backend_user()) ? [
				'email' => $u->getEmail(), 'id' => $u->getId(), 'username' => $u->getUserName()
			] : (!df_is_frontend() ? [] : (($c = df_customer())
				? ['email' => $c->getEmail(), 'id' => $c->getId(), 'username' => $c->getName()]
				: ['id' => df_customer_session_id()]
			))
		)) + ['ip_address' => df_visitor_ip()], false);
		$r->tags(
			['Core' => df_core_version(), 'Magento' => df_magento_version(), 'MySQL' => df_db_version(), 'PHP' => phpversion()]
			# 2023-07-15 "Improve diagnostic messages": https://github.com/JustunoCom/m2/issues/49
		 	+ ($isCore ? [] : ['Module' => $m, 'Module Version' => df_package_version($m)])
		);
	}
	return $r ?: (!$isCore ? df_sentry_m('Df_Core') : df_error('Sentry settings for `Df_Core` are absent.'));
# 2020-09-09, 2023-07-25 We need `df_caller_module(2)` because it is nested inside `df_sentry_module()` and `dfcf`.
}, [df_sentry_module($m ?: df_caller_module(2))]);}

/**
 * 2017-03-15
 * @used-by df_sentry()
 * @used-by df_sentry_m()
 * @param string|object|null $m [optional]
 */
function df_sentry_module($m = null):string {return !$m ? 'Df_Core' : df_module_name($m);}

/**
 * 2017-01-10
 * $m could be:
 * 1) A module name: «A_B»
 * 2) A class name: «A\B\C».
 * 3) An object: it comes down to the case 2 via @see get_class()
 * 4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * @used-by \Df\Payment\Method::action()
 * @param string|object|null $m
 * @param array(string => mixed) $a
 */
function df_sentry_tags($m, array $a):void {df_sentry_m($m)->tags($a);}