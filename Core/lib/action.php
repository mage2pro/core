<?php
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface as IAction;
/**
 * 2015-12-21
 * @return bool
 */
function df_action_catalog_product_view() {return df_action_is('catalog_product_view');}

/**
 * 2017-05-04
 * @param string $c
 * @return IAction
 */
function df_action_create($c) {
	/** @var ActionFactory $f */
	$f = df_o(ActionFactory::class);
	return $f->create($c);
}

/**
 * 2017-03-16
 * @see df_url_path_contains()
 * @used-by \Dfe\AllPay\W\Event\Offline::ttCurrent()
 * @param string $s
 * @return bool
 */
function df_action_has($s) {return df_contains(df_action_name(), $s);}

/**
 * 2016-01-07
 * @see df_url_path_contains()
 * @param string[] ...$names
 * @return bool
 */
function df_action_is(...$names) {return ($a = df_action_name()) && in_array($a, dfa_flatten($names));}

/**
 * 2015-09-02
 * 2017-03-15
 * Случай запуска Magento с командной строки надо обрабатывать отдельно, потому что иначе
 * @uses \Magento\Framework\App\Request\Http::getFullActionName() вернёт строку «__».
 * @used-by df_action_has()
 * @used-by df_action_is()
 * @used-by df_sentry()
 * @used-by \Dfe\Markdown\CatalogAction::entityType()
 * @used-by \Dfe\Markdown\FormElement::config()
 * @return string|null
 */
function df_action_name() {return df_is_cli() ? null : df_request_o()->getFullActionName();}

/**
 * 2017-08-28
 * @used-by df_is_checkout()
 * @used-by df_is_checkout_multishipping()
 * @used-by df_is_system_config()
 * @param string $p
 * @return bool
 */
function df_action_prefix($p) {return df_starts_with(df_action_name(), $p);}