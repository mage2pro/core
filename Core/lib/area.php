<?php
use Magento\Framework\App\Area as A;

/**
 * 2017-04-02 «Area code is not set» on a df_area_code_is() call: https://mage2.pro/t/3581
 * @param bool $throw [optional]
 * @return string|null
 * @throws \Exception
 */
function df_area_code($throw = true) {
	try {return df_app_state()->getAreaCode();}
	catch (\Exception $e) {
		if ($throw) {
			throw $e;
		}
		return null;
	}
}

/**
 * 2016-09-30
 * 2016-12-23
 * Будьте осторожны: если мы обрабатываем асинхронный запрос к серверу,
 * то @uses \Magento\Framework\App\State::getAreaCode()
 * вернёт не «frontend» или «adminhtml», а, например, «webapi_rest».
 * 2017-04-02 «Area code is not set» on a df_area_code_is() call: https://mage2.pro/t/3581
 * @used-by df_is_backend()
 * @used-by df_is_frontend()
 * @used-by df_is_rest()
 * @used-by \Frugue\Core\Plugin\Framework\App\FrontControllerInterface::aroundDispatch()
 * @param string[] ...$values
 * @return bool
 */
function df_area_code_is(...$values) {return ($a = df_area_code(false)) && in_array($a, $values);}

/**
 * 2015-08-14
 * Мы не вправе кэшировать результат работы функции: ведь текущий магазин может меняться.
 *
 * 2015-09-20
 * В отличие от Magento 1.x мы не можем использовать код
 * Magento\Store\Model\Store::ADMIN_CODE === df_store($store)->getCode();
 * потому что при нахождении в административной части
 * он вернёт вовсе не административную витрину, а витрину, указанную в MAGE_RUN_CODE.
 * Более того, @see df_store() учитывает параметры URL
 * и даже при нахождении в административном интерфейсе
 * может вернуть вовсе не административную витрину.
 * Поэтому определяем нахождение в административном интерфейсе другим способом.
 *
 * 2016-09-30
 * Используемые константы присутствуют уже в релизе 2.0.0, потому использовать их безопасно:
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/App/Area.php
 *
 * 2016-12-23
 * Если мы обрабатываем асинхронный запрос к серверу,
 * то @uses \Magento\Framework\App\State::getAreaCode()
 * вернёт не «adminhtml», а, например, «webapi_rest».
 * В то же время @uses df_backend_user() безопасно использовать даже с витрины.
 * @used-by df_block()
 * @used-by df_store()
 * @used-by \Df\Config\Settings::scope()
 * @used-by \Df\Framework\Form\Element\Fieldset::addField()
 * @used-by \Df\Framework\Plugin\View\Page\Title::aroundGet()
 * @used-by \Df\Payment\Block\Info::getIsSecureMode()
 * @used-by \Df\Payment\Block\Info::getTemplate()
 * @used-by \Df\Payment\Method::getFormBlockType()
 * @used-by \Df\Payment\Method::getTitle()
 * @used-by \Df\Payment\Method::isAvailable()
 * @used-by \Df\Sales\Plugin\Model\Order\Address\Renderer::aroundFormat()
 * @return bool
 */
function df_is_backend() {return df_area_code_is(A::AREA_ADMINHTML) || df_is_ajax() && df_backend_user();}

/**
 * 2016-06-02
 * Сделал по аналогии с @see df_is_backend()
 *
 * 2016-09-30
 * Сегодня заметил, что метод @uses \Magento\Framework\App\State::getAreaCode()
 * стал возвращать значение @see \Magento\Framework\App\Area::AREA_WEBAPI_REST
 * при выполнении платежа на витрине.
 *
 * 2016-09-30
 * Используемые константы присутствуют уже в релизе 2.0.0, потому использовать их безопасно:
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/App/Area.php
 *
 * @return bool
 */
function df_is_frontend() {return
	df_area_code_is(A::AREA_FRONTEND) || df_is_ajax() && df_customer_session()->getSessionId()
;}

/**
 * 2017-03-15
 * @used-by df_sentry()
 * @return bool
 */
function df_is_rest() {return df_area_code_is(A::AREA_WEBAPI_REST);}