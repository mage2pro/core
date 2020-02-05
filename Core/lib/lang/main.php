<?php
use Closure as F;
use Exception as E;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * 2015-12-25
 * Этот загадочный метод призван заменить код вида: `is_array($a) ? $a : func_get_args()`.
 * Теперь можно писать так: df_args(func_get_args()).
 * @used-by df_clean()
 * @used-by dfa_combine_self()
 * @see dfa_unpack()
 * @param mixed[] $a
 * @return mixed[]
 */
function df_args(array $a) {return !$a || !is_array($a[0]) ? $a : $a[0];}

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
 * 2016-02-09
 * Осуществляет ленивое ветвление только для первой ветки.
 * @param bool $cond
 * @param mixed|callable $onTrue
 * @param mixed|null $onFalse [optional]
 * @return mixed
 */
function df_if1($cond, $onTrue, $onFalse = null) {return $cond ? df_call_if($onTrue) : $onFalse;}

/**
 * 2016-02-09
 * Осуществляет ленивое ветвление только для второй ветки.
 * @param bool $cond
 * @param mixed $onTrue
 * @param mixed|null|callable $onFalse [optional]
 * @return mixed
 */
function df_if2($cond, $onTrue, $onFalse = null) {return $cond ? $onTrue : df_call_if($onFalse);}

/**
 * Осуществляет ленивое ветвление.
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
 * @used-by df_cms_block_content()
 * @used-by df_currency_convert_safe()
 * @used-by df_customer()
 * @used-by df_gd()
 * @used-by df_intl_dic_read()
 * @used-by df_layout_update()
 * @used-by df_magento_version_remote()
 * @used-by df_phone()
 * @used-by df_product_att()
 * @used-by df_product_att_val_s()
 * @used-by df_zuri()
 * @used-by dfp_refund()  
 * @used-by \Df\Payment\BankCardNetworks::url()
 * @used-by \Dfe\Moip\Facade\Customer::_get()
 * @used-by \Dfe\Moip\FE\Webhooks::onFormInitialized()
 * @used-by \Dfe\Square\Facade\Customer::_get()
 * @used-by \Doormall\Shipping\Partner\Entity::locationM()
 * @used-by \Inkifi\Mediaclip\API\Facade\User::projects()
 * @param F $try
 * @param F|bool|mixed $onE [optional]
 * @return mixed
 * @throws E
 */
function df_try(F $try, $onE = null) {
	try {return $try();} 
	catch(E $e) {return $onE instanceof F ? $onE($e) : (true === $onE ? df_error($e) : $onE);}
}