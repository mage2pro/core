<?php
use Closure as F;
use Magento\Framework\App\Area as A;

/**
 * 2017-04-02 «Area code is not set» on a df_area_code_is() call: https://mage2.pro/t/3581
 * 2021-03-23 It returns «crontab» for Cron jobs.
 * @used-by df_area_code_is()
 * @used-by df_area_code_set_d()
 * @used-by df_backend_user()
 * @param F|bool|mixed $onE [optional]
 * @return string|null
 * @throws Exception
 */
function df_area_code($onE = null) {return df_try(function() {return df_app_state()->getAreaCode();}, $onE);}

/**
 * 2016-09-30
 * 2016-12-23
 * Будьте осторожны: если мы обрабатываем асинхронный запрос к серверу,
 * то @uses \Magento\Framework\App\State::getAreaCode()
 * вернёт не «frontend» или «adminhtml», а, например, «webapi_rest».
 * 2017-04-02 «Area code is not set» on a df_area_code_is() call»: https://mage2.pro/t/3581
 * @used-by df_is_backend()
 * @used-by df_is_frontend()
 * @used-by df_is_rest()
 * @used-by \Frugue\Store\Plugin\Framework\App\FrontControllerInterface::aroundDispatch()
 */
function df_area_code_is(string ...$v):bool {return in_array(df_area_code(), $v);}

/**
 * 2019-06-21
 * @used-by \KingPalm\B2B\Setup\V140\MoveDataToAddress::p()
 */
function df_area_code_set_b():void {df_app_state()->setAreaCode(A::AREA_ADMINHTML);}

/**
 *
 * 2021-11-25
 * It fixes the «Area code is not set» error in @see \Magento\Framework\App\State::getAreaCode()
 * when the area code is really not set (e.g., for console commands like `bin/magento tfc:google-shopping:1`).
 * @used-by df_view_config()
 */
function df_area_code_set_d():void {df_area_code() || df_area_code_set_f();}

/**
 * 2019-06-21
 * @used-by df_area_code_set_d()
 * @used-by \Df\Core\TestCase::setUp()
 */
function df_area_code_set_f():void {df_app_state()->setAreaCode(A::AREA_FRONTEND);}

/**
 * 2015-08-14 Мы не вправе кэшировать результат работы функции: ведь текущий магазин может меняться.
 * 2015-09-20
 * В отличие от Magento 1.x мы не можем использовать код
 * Magento\Store\Model\Store::ADMIN_CODE === df_store($store)->getCode();
 * потому что при нахождении в административной части
 * он вернёт вовсе не административную витрину, а витрину, указанную в MAGE_RUN_CODE.
 * Более того, @see df_store() учитывает параметры URL
 * и даже при нахождении в административном интерфейсе
 * может вернуть вовсе не административную витрину.
 * Поэтому определяем нахождение в административном интерфейсе другим способом.
 * 2016-09-30
 * Используемые константы присутствуют уже в релизе 2.0.0, потому использовать их безопасно:
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/App/Area.php
 * 2016-12-23
 * Если мы обрабатываем асинхронный запрос к серверу, то @uses \Magento\Framework\App\State::getAreaCode()
 * вернёт не «adminhtml», а, например, «webapi_rest».
 * В то же время @uses df_backend_user() безопасно использовать даже с витрины.
 * @see df_is_ajax()
 * @see df_is_frontend()
 * @see df_is_rest()
 * @used-by df_ban()
 * @used-by df_block()
 * @used-by df_catalog_locator()
 * @used-by df_customer_id()
 * @used-by df_product_current()
 * @used-by df_session()
 * @used-by df_store()
 * @used-by \Df\Config\Settings::scope()
 * @used-by \Df\Framework\Form\Element\Fieldset::addField()
 * @used-by \Dfe\GingerPaymentsBase\Block\Info::msgUnconfirmed()
 * @used-by \Df\Payment\Block\Info::getIsSecureMode()
 * @used-by \Df\Payment\Block\Info::getTemplate()
 * @used-by \Df\Payment\Method::getFormBlockType()
 * @used-by \Df\Payment\Method::getTitle()
 * @used-by \Df\Payment\Method::isAvailable()
 * @used-by \Df\Sales\Plugin\Model\Order\Address\Renderer::aroundFormat()
 * @used-by \Dfe\Sift\Observer::f()
 */
function df_is_backend():bool {return df_area_code_is(A::AREA_ADMINHTML) || df_is_ajax() && df_backend_user();}

/**
 * 2016-06-02 By analogy with @see df_is_backend()
 * 2016-09-30
 * Today I have noticed that @uses \Magento\Framework\App\State::getAreaCode()
 * returns @see \Magento\Framework\App\Area::AREA_WEBAPI_REST during a frontend payment.
 * 2016-09-30
 * The used constant is available since Magento 2.0.0:
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/App/Area.php
 * 2021-03-23 It returns `false` for Cron jobs because @see df_area_code() returns «crontab» for Cron jobs.
 * @see df_is_ajax()
 * @see df_is_backend()
 * @see df_is_rest()
 */
function df_is_frontend():bool {return df_area_code_is(A::AREA_FRONTEND) || df_is_ajax() && df_customer_session_id();}

/**
 * 2017-03-15
 * @see df_is_ajax()
 * @see df_is_backend()
 * @see df_is_frontend()
 * @used-by df_response()
 */
function df_is_rest():bool {return df_area_code_is(A::AREA_WEBAPI_REST);}