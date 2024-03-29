<?php
use Df\Core\Exception as DFE;
use Df\Qa\Method as Q;
use Throwable as Th; # 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2016-11-10
 * @used-by df_con_heir()
 * @used-by df_con_hier()
 * @used-by df_eav_update()
 * @used-by df_load()
 * @used-by df_newa()
 * @used-by df_trans()
 * @used-by dfpex_args()
 * @used-by \Df\Payment\Choice::f()
 * @used-by \Df\Payment\Operation\Source\Creditmemo::cm()
 * @used-by \Df\Payment\Operation\Source\Order::ii()
 * @used-by \Df\Payment\Operation\Source\Quote::ii()
 * @used-by \Df\Payment\W\Strategy::handle()
 * @used-by \Df\Payment\W\Strategy::m()
 * @used-by \Df\Payment\W\Strategy\Refund::_handle()
 * @param string|object $v
 * @param string|object|null $c [optional]
 * @param string|Th|null $m [optional]
 * @return string|object
 * @throws DFE
 */
function df_ar($v, $c = null, $m = null) {return dfcf(function($v, $c = null, $m = null) {
	if ($c) {
		$c = df_cts($c);
		!is_null($v) ?: df_error($m ?: "Expected class: «{$c}», given `null`.");
		is_object($v) || is_string($v) ?: df_error($m ?: "Expected class: «{$c}», given: %s.", df_type($v));
		$cv = df_assert_class_exists(df_cts($v)); /** @var string $cv */
		if (!is_a($cv, $c, true)) {
			df_error($m ?: "Expected class: «{$c}», given class: «{$cv}».");
		}
	}
	return $v;
}, func_get_args());}

/**
 * 2019-12-14
 * If you do not want the exception to be logged via @see df_bt_log(),
 * then you can pass an empty string (instead of `null`) as the second argument:
 * @see \Df\Core\Exception::__construct():
 *		if (is_null($m)) {
 *			$m = __($prev ? df_xts($prev) : 'No message');
 *			# 2017-02-20 To facilite the «No message» diagnostics.
 *			if (!$prev) {
 *				df_bt_log();
 *			}
 *		}
 * https://github.com/mage2pro/core/blob/5.5.7/Core/Exception.php#L61-L67
 * @used-by df_assert_qty_supported()
 * @used-by df_call_parent()
 * @used-by df_caller_m()
 * @used-by df_caller_mf()
 * @used-by df_catalog_locator()
 * @used-by df_config_field()
 * @used-by df_configurable_children()
 * @used-by df_date_from_timestamp_14()
 * @used-by df_dtss()
 * @used-by df_eta()
 * @used-by df_fe_fs()
 * @used-by df_float()
 * @used-by df_id()
 * @used-by df_layout_update()
 * @used-by df_module_dir()
 * @used-by df_module_file_name()
 * @used-by df_oqi_amount()
 * @used-by df_oqi_amount()
 * @used-by df_xml_child()
 * @used-by dfaf()
 * @used-by dfp_oq()
 * @used-by dfr_prop()
 * @used-by \Df\Config\A::i()
 * @used-by \Df\Core\Html\Tag::openTagWithAttributesAsText()
 * @used-by \Df\Framework\Form\Element\Fieldset::select()
 * @used-by \Df\Payment\Operation\Source\Quote::id()
 * @used-by \Df\Qa\Trace\Frame::methodParameter()
 * @used-by \Df\Qa\Trace\Frame::url()
 * @used-by \Df\StripeClone\Payer::newCard()
 * @used-by \Df\Typography\Font::validate()
 * @used-by \Dfe\AllPay\InstallmentSales\Plan\Entity::validate()
 * @used-by \Dfe\AllPay\Total\Quote::collect()
 * @used-by \Dfe\AllPay\Total\Quote::iiAdd()
 * @used-by \Dfe\AlphaCommerceHub\Method::charge()
 * @used-by \Dfe\CheckoutCom\Response::getCaptureCharge()
 * @used-by \Dfe\Color\Observer\ProductImportBunchSaveAfter::execute()
 * @used-by \Dfe\GoogleFont\Font::variant()
 * @used-by \Dfe\GoogleFont\Font\Variant\Preview::draw()
 * @used-by \Dfe\GoogleFont\Fonts::get()
 * @used-by \Dfe\GoogleFont\Fonts\Png::image()
 * @used-by \Dfe\Logo\Observer\OptionSaveAfter::execute()
 * @used-by \Dfe\TwoCheckout\Method::_refund()
 * @used-by \Dfe\TwoCheckout\Method::charge()
 * @used-by \Dfe\YandexKassa\W\Responder::error()
 * @used-by \Inkifi\Mediaclip\API\Entity\Order\Item::mProduct()
 * @used-by \Inkifi\Mediaclip\Event::oi()
 * @used-by \RWCandy\Captcha\Assert::email()
 * @used-by \RWCandy\Captcha\Assert::name()
 * @used-by \RWCandy\Captcha\Observer\CustomerAccountCreatePost::execute()
 * @used-by \RWCandy\Captcha\Observer\CustomerSaveBefore::execute()
 * @param mixed $cond
 * @param string|Th|null $m [optional]
 * @return mixed
 * @throws DFE
 */
function df_assert($cond, $m = null) {return $cond ?: df_error($m);}

/**
 * @used-by df_wishlist_item_candidates()
 * @used-by \Dfe\GoogleFont\Fonts\Sprite::datumPoints()
 * @used-by \Df\Xml\X::importArray()
 * @used-by \Dfe\FacebookLogin\Customer::responseJson()
 */
function df_assert_array(array $a):array {return $a;}

/**
 * 2017-02-18
 * @used-by df_clean_keys()
 * @return array(string => mixed)
 * @throws DFE
 */
function df_assert_assoc(array $a):array {return df_is_assoc($a) ? $a : df_error('The array should be associative.');}

/**
 * 2021-03-22
 * @used-by df_date_create()
 * @param int|float $v
 * @param int|float|null $min
 * @param int|float|null $max
 * @return int|float
 * @throws DFE
 */
function df_assert_between($v, $min, $max, bool $inclusive = true) {
	if (!df_between($v, $min, $max, $inclusive)) {
		list($o1, $o2) = !$inclusive ? ['>', '<'] : ['≥', '≤']; /** @var string $o1 */ /** @var string $o2 */
		df_error("The value «{$v}» is not allowed. An allowed value should be $o1 $min and $o2 $max.");
	}
	return $v;
}

/**
 * 2016-08-03
 * @used-by df_ar()
 * @used-by \Df\Config\Backend\Serialized::entityC()
 * @param string|Th $m [optional]
 * @throws DFE
 */
function df_assert_class_exists(string $c, $m = null):string {
	df_param_sne($c, 0);
	return df_class_exists($c) ? $c : df_error($m ?: "The required class «{$c}» does not exist.");
}

/**
 * 2017-01-14 Отныне функция возвращает $v: это позволяет нам значительно сократить код вызова функции.
 * @used-by df_date_from_timestamp_14()
 * @used-by \Dfe\Zoho\App::title()
 * @used-by \Dfe\Omise\W\Event\Charge\Complete::isPending()
 * @param string|float|int|bool|null $v
 * @param array(string|float|int|bool|null) $a
 * @param string|Th $m [optional]
 * @return string|float|int|bool|null
 * @throws DFE
 */
function df_assert_in($v, array $a, $m = null) {
	if (!in_array($v, $a, true)) {
		df_error($m ?: "The value «{$v}» is rejected" . (
			10 >= count($a)
				? sprintf(". Allowed values: «%s».", df_csv_pretty($a))
				: " because it is absent in the list of allowed values."
		));
	}
	return $v;
}

/**
 * 2017-01-14       
 * @used-by \Dfe\GoogleFont\Font\Variant\Preview::box()
 * @used-by \Dfe\GoogleFont\Fonts\Png::colorAllocateAlpha()
 * @used-by \Dfe\GoogleFont\Fonts\Png::image()
 * @used-by \Dfe\GoogleFont\Fonts\Sprite::draw()
 * @used-by \Df\Xml\X::asXMLPart()
 * @param mixed $v
 * @param string|Th $m [optional]
 * @return mixed
 * @throws DFE
 */
function df_assert_nef($v, $m = null) {return false !== $v ? $v : df_error($m ?:
	'The «false» value is rejected, any others are allowed.'
);}

/**
 * @used-by df_currency_base()
 * @used-by df_file_name()
 * @used-by df_json_decode()
 * @used-by \CanadaSatellite\Bambora\Action\Authorize::p() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by \CanadaSatellite\Bambora\Action\_Void::p() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by \Df\Payment\W\Event::pid()
 * @used-by \Df\PaypalClone\Charge::p()
 * @used-by \Df\StripeClone\Payer::newCard()
 * @used-by \Df\Xml\X::addAttributes()
 * @used-by \Dfe\Stripe\Controller\CustomerReturn\Index::isSuccess()
 * @used-by \Dfe\Stripe\Init\Action::redirectUrl()
 * @throws DFE
 */
function df_assert_sne(string $v, int $sl = 0):string {
	$sl++;
	# The previous code `if (!$v)` was wrong because it rejected the '0' string.
	return !df_es($v) ? $v : Q::raiseErrorVariable(__FUNCTION__, [Q::NES], $sl);
}

/**
 * 2016-08-09
 * @used-by dfaf()
 * @param Traversable|array $v
 * @param string|Th $m [optional]
 * @return Traversable|array
 * @throws DFE
 */
function df_assert_traversable($v, $m = null) {return is_iterable($v) ? $v : df_error($m ?:
	'A variable is expected to be a Traversable or an array, ' . 'but actually it is %s.', df_type($v)
);}

/**
 * @used-by \Df\Config\Settings::b()
 * @used-by \Df\Framework\Form\Element\Checkbox::b()
 * @used-by \Df\Payment\Comment\Description::getCommentText()
 * @used-by \Df\Payment\Comment\Description::locations()
 * @used-by \Df\Shipping\Settings::enable()
 * @used-by \Dfe\Moip\FE\Webhooks::onFormInitialized()
 * @used-by \Dfe\YandexKassa\Source\Option::map()
 * @param mixed $v
 */
function df_bool($v):bool {
	/**
	 * Unfortunately, we can not replace @uses in_array() with @see array_flip() + @see isset() to speedup the execution,
	 * because it could lead to the warning: «Warning: array_flip(): Can only flip STRING and INTEGER values!».
	 * Moreover, @see array_flip() + @see isset() fails the following test:
	 *	$a = array(null => 3, 0 => 4, false => 5);
	 *	$this->assertNotEquals($a[0], $a[false]);
	 * Though, @see array_flip() + @see isset() does not fail the tests:
	 * $this->assertNotEquals($a[null], $a[0]);
	 * $this->assertNotEquals($a[null], $a[false]);
	 */
	static $no = [0, '0', 'false', false, null, 'нет', 'no', 'off', '']; /** @var mixed[] $no */
	static $yes = [1, '1', 'true', true, 'да', 'yes', 'on']; /** @var mixed[] $yes */
	/**
	 * Passing $strict = true to the @uses in_array() call is required here,
	 * otherwise any true-compatible value (e.g., a non-empty string) will pass the check.
	 */
	return in_array($v, $no, true) ? false : (in_array($v, $yes, true) ? true : df_error(
		'A boolean value is expected, but got «%s».', df_dump($v)
	));
}

/**
 * 2015-03-04
 * Эта функция проверяет, принадлежит ли переменная $v хотя бы к одному из классов $class.
 * Обратите внимание, что т.к. алгоритм функции использует стандартный оператор instanceof,
 * то переменная $v может быть не только объектом,
 * а иметь произвольный тип: https://php.net/manual/language.operators.type.php#example-146
 * Если $variable не является объектом, то функция просто вернёт false.
 *
 * Наша функция не загружает при этом $class в память интерпретатора PHP.
 * Если $class ещё не загружен в память интерпретатора PHP, то функция вернёт false.
 * В принципе, это весьма логично!
 * Если проверяемый класс ещё не был загружен в память интерпретатора PHP,
 * то проверяемая переменная $v гарантированно не может принадлежать данному классу!
 * http://3v4l.org/KguI5
 * Наша функция отличается по сфере применения
 * как от оператора instanceof, так и от функции @see is_a() тем, что:
 * 1) Умеет проводить проверку на приналежность не только одному конкретному классу, а и хотя бы одному из нескольких.
 * 2) @is_a() приводит к предупреждению уровня E_DEPRECATED интерпретатора PHP версий ниже 5.3:
 * https://php.net/manual/function.is-a.php
 * 3) Даже при проверке на принадлежность одному классу код с @see df_is() получается короче,
 * чем при применении instanceof в том случае, когда мы не уверены, существует ли класс
 * и загружен ли уже класс интерпретатором PHP.
 * Например, нам приходилось писать так:
 *		class_exists('Df_1C_Cml2Controller', $autoload = false)
 *	&&
 *		df_state()->getController() instanceof Df_1C_Cml2Controller
 * Или так:
 *		$controllerClass = 'Df_1C_Cml2Controller';
 *		$result = df_state()->getController() instanceof $controllerClass;
 * При этом нельзя писать
 *		df_state()->getController() instanceof 'Df_1C_Cml2Controller'
 * потому что правый операнд instanceof может быть строковой переменной, но не может быть просто строкой!
 * https://php.net/manual/language.operators.type.php#example-148
 * 2021-05-31 @deprecated It is unused.
 * 2022-11-26
 * `object` as an argument type is not supported by PHP < 7.2: https://github.com/mage2pro/core/issues/174#user-content-object
 * @param object $v
 */
function df_is($v, string ...$cc):bool {return !!df_find($cc, function(string $c) use($v):bool {return $v instanceof $c;});}