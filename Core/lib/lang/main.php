<?php
use Closure as F;
use Magento\Framework\App\Filesystem\DirectoryList;
use \Throwable as Th; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2017-02-07
 * @used-by \Df\Payment\Block\Info::extended()
 * @used-by \Df\Payment\Method::test()
 * @used-by \Dfe\Moip\FE\Webhooks::onFormInitialized()
 * @used-by \Dfe\Paymill\Settings::test3DS()
 * $args — массив либо пустой, либо из 2 элементов с целочисленными индексами 0 и 1.
 * Если массив $args пуст, то функция возвращает $r.
 * Если массив $args непуст, то функция возвращает:
 * 		$args[0] при истинности $r
 *		$args[1] при ложности $r
 * @return mixed
 */
function df_b(array $args, bool $r) {return !$args ? $r : $args[intval(!$r)];}

/**
 * Осуществляет ленивое ветвление.
 * @used-by df_cfg()
 * @param mixed|callable $onTrue
 * @param mixed|null|callable $onFalse [optional]
 * @return mixed
 */
function df_if(bool $cond, $onTrue, $onFalse = null) {return $cond ? df_call_if($onTrue) : df_call_if($onFalse);}

/**
 * 2016-02-09 Осуществляет ленивое ветвление только для первой ветки.
 * @used-by df_leaf()
 * @used-by df_request()
 * @param mixed|callable $onTrue
 * @param mixed|null $onFalse [optional]
 * @return mixed
 */
function df_if1(bool $cond, $onTrue, $onFalse = null) {return $cond ? df_call_if($onTrue) : $onFalse;}

/**
 * 2016-02-09 Осуществляет ленивое ветвление только для второй ветки.
 * @used-by \Df\Config\Settings::p()
 * @param mixed $onTrue
 * @param mixed|null|callable $onFalse [optional]
 * @return mixed
 */
function df_if2(bool $cond, $onTrue, $onFalse = null) {return $cond ? $onTrue : df_call_if($onFalse);}

/**
 * @used-by \Df\Core\Html\Tag::openTagWithAttributesAsText()
 * @param mixed $v
 * @return mixed
 */
function df_nop($v) {return $v;}

/**
 * 2015-12-06
 * 2022-11-23 `callable` as an argument type is supported by PHP ≥ 5.4:
 * https://github.com/mage2pro/core/issues/174#user-content-callable
 * @param string|object $id
 * @return mixed
 */
function df_sync($id, callable $job, float $interval = 0.1) { /** @var mixed $r */
	$intervalI = round(1000000 * $interval); /** @var int $intervalI */
	$nameShort = 'df-core-sync-' . md5(is_object($id) ? get_class($id) : $id) . '.lock'; /** @var string $nameShort */
	$name = df_sys_path_abs(DirectoryList::TMP, $nameShort); /** @var string $name */
	while(file_exists($name)) {
		usleep($intervalI);
	}
	try {
		df_file_write($name, '');
		$r = $job();
	}
	finally {
		df_fs_w(DirectoryList::TMP)->delete($nameShort);
	}
	return $r;
}

/**
 * 2017-04-15
 * @used-by df_area_code()
 * @used-by df_asset_url()
 * @used-by df_cms_block_content()
 * @used-by df_contents()
 * @used-by df_currency_convert_safe()
 * @used-by df_customer()
 * @used-by df_customer_group_name()
 * @used-by df_date_from_db()
 * @used-by df_gd()
 * @used-by df_intl_dic_read()
 * @used-by df_layout_update()    
 * @used-by df_magento_version_remote()
 * @used-by df_module_file_name()
 * @used-by df_phone()
 * @used-by df_product_att()
 * @used-by df_product_att_val_s()
 * @used-by df_zuri()
 * @used-by dfp_refund()
 * @used-by \Alignet\Paymecheckout\Model\Client\Classic\Order\DataGetter::userCodePayme() (innomuebles.com, https://github.com/innomuebles/m2/issues/17)
 * @used-by \Amasty\Checkout\Controller\Index\Index::isSecureRequest() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/241)
 * @used-by \CanadaSatellite\Core\Plugin\Magento\Sales\Model\Order\Item::aroundGetProductOptions(canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/62)
 * @used-by \Df\Framework\Console\Command::execute()
 * @used-by \Dfe\GoogleFont\Font\Variant\Preview::box()
 * @used-by \Dfe\GoogleFont\Fonts\Png::url()
 * @used-by \Df\Payment\BankCardNetworks::url()
 * @used-by \Df\Qa\Trace\Formatter::p()
 * @used-by \Df\Qa\Trace\Frame::methodR()
 * @used-by \Df\Theme\Controller\Result\MessagePlugin::getCookiesMessages()
 * @used-by \Dfe\CheckoutCom\Method::leh()
 * @used-by \Dfe\Moip\FE\Webhooks::onFormInitialized()
 * @used-by \Dfe\Moip\Facade\Customer::_get()
 * @used-by \Dfe\Square\Facade\Customer::_get()
 * @used-by \Doormall\Shipping\Partner\Entity::locationM()
 * @used-by \Inkifi\Mediaclip\API\Facade\User::projects()
 * @used-by \TFC\GoogleShopping\Controller\Index\Index::execute() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/google-shopping/issues/1)
 * @param F|bool|mixed $onE [optional]
 * @return mixed
 * @throws Th
 */
function df_try(F $try, $onE = null) {
	try {return $try();} 
	catch(Th $th) {return $onE instanceof F ? $onE($th) : (true === $onE ? df_error($th) : $onE);}
}