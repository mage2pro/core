<?php
use Closure as F;
use Exception as E;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * 2015-12-25
 * Этот загадочный метод призван заменить код вида: `is_array($a) ? $a : func_get_args()`.
 * Теперь можно писать так: df_args(func_get_args()).
 * @used-by df_clean()
 * @used-by df_clean_keys()
 * @used-by df_csv()
 * @used-by df_csv_pretty_quote()
 * @used-by df_format()
 * @used-by dfa_combine_self()
 * @used-by dfa_unset()
 * @see dfa_unpack()
 */
function df_args(array $a):array {return !$a || !is_array($a[0]) ? $a : $a[0];}

/**
 * 2017-02-07
 * @used-by \Df\Payment\Block\Info::extended()
 * @used-by \Df\Payment\Method::test()
 * @used-by \Dfe\Moip\FE\Webhooks::onFormInitialized()
 * @used-by \Dfe\Paymill\Settings::test3DS()
 * @param mixed[] $args
 * $args — массив либо пустой, либо из 2 элементов с целочисленными индексами 0 и 1.
 * Если массив $args пуст, то функция возвращает $r.
 * Если массив $args непуст, то функция возвращает:
 * 		$args[0] при истинности $r
 *		$args[1] при ложности $r
 * @param bool $r
 * @return mixed
 */
function df_b(array $args, $r) {return !$args ? $r : $args[intval(!$r)];}

/**
 * 2016-02-09 Осуществляет ленивое ветвление только для первой ветки.
 * @param bool $cond
 * @param mixed|callable $onTrue
 * @param mixed|null $onFalse [optional]
 * @return mixed
 */
function df_if1($cond, $onTrue, $onFalse = null) {return $cond ? df_call_if($onTrue) : $onFalse;}

/**
 * 2016-02-09 Осуществляет ленивое ветвление только для второй ветки.
 * @param bool $cond
 * @param mixed $onTrue
 * @param mixed|null|callable $onFalse [optional]
 * @return mixed
 */
function df_if2($cond, $onTrue, $onFalse = null) {return $cond ? $onTrue : df_call_if($onFalse);}

/**
 * Осуществляет ленивое ветвление.
 * @used-by df_cfg()
 * @param bool $cond
 * @param mixed|callable $onTrue
 * @param mixed|null|callable $onFalse [optional]
 * @return mixed
 */
function df_if($cond, $onTrue, $onFalse = null) {return $cond ? df_call_if($onTrue) : df_call_if($onFalse);}

/**
 * @used-by \Df\Core\Format\Html\Tag::openTagWithAttributesAsText()
 * @param mixed $v
 * @return mixed
 */
function df_nop($v) {return $v;}

/**
 * 2015-12-06
 * @param string|object $id
 * @param callable $job
 * @param float $interval [optional]
 * @return mixed
 */
function df_sync($id, callable $job, $interval = 0.1) { /** @var mixed $r */
	$intervalI = round(1000000 * $interval); /** @var int $intervalI */
	$nameShort = 'df-core-sync-' . md5(is_object($id) ? get_class($id) : $id) . '.lock'; /** @var string $nameShort */
	$name = df_path_absolute(DirectoryList::TMP, $nameShort); /** @var string $name */
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
 * @used-by df_currency_convert_safe()
 * @used-by df_customer()
 * @used-by df_customer_group_name()
 * @used-by df_date_from_db()
 * @used-by df_gd()
 * @used-by df_intl_dic_read()
 * @used-by df_layout_update()    
 * @used-by df_magento_version_remote()
 * @used-by df_phone()
 * @used-by df_product_att()
 * @used-by df_product_att_val_s()
 * @used-by df_zuri()
 * @used-by dfp_refund()
 * @used-by \Alignet\Paymecheckout\Model\Client\Classic\Order\DataGetter::userCodePayme() (innomuebles.com, https://github.com/innomuebles/m2/issues/17)
 * @used-by \Amasty\Checkout\Controller\Index\Index::isSecureRequest() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/241)
 * @used-by \CanadaSatellite\Core\Plugin\Magento\Sales\Model\Order\Item::aroundGetProductOptions(canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/62)
 * @used-by \Df\Framework\Console\Command::execute()
 * @used-by \Df\GoogleFont\Font\Variant\Preview::box()
 * @used-by \Df\GoogleFont\Fonts\Png::url()
 * @used-by \Df\Payment\BankCardNetworks::url()
 * @used-by \Df\Qa\Trace\Frame::methodR()
 * @used-by \Df\Theme\Controller\Result\MessagePlugin::getCookiesMessages()
 * @used-by \Dfe\CheckoutCom\Method::leh()
 * @used-by \Dfe\Moip\FE\Webhooks::onFormInitialized()
 * @used-by \Dfe\Moip\Facade\Customer::_get()
 * @used-by \Dfe\Square\Facade\Customer::_get()
 * @used-by \Doormall\Shipping\Partner\Entity::locationM()
 * @used-by \Inkifi\Mediaclip\API\Facade\User::projects()
 * @used-by \TFC\GoogleShopping\Controller\Index\Index::execute() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/google-shopping/issues/1)
 * @param F $try
 * @param F|bool|mixed $onE [optional]
 * @return mixed
 * @throws E
 */
function df_try(F $try, $onE = null) {
	try {return $try();} 
	catch(E $e) {return $onE instanceof F ? $onE($e) : (true === $onE ? df_error($e) : $onE);}
}