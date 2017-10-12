<?php
namespace Df\Framework\Plugin\View;
use Magento\Framework\View\Layout as Sb;
use Magento\Ui\Component\Wrapper\UiComponent;
/**
 * 2016-01-01
 * Наследуемся от класса @uses \Magento\Ui\Component\Wrapper\UiComponent,
 * чтобы получить доступ к его свойству @uses \Magento\Ui\Component\Wrapper\UiComponent::$component
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Ui/Component/Wrapper/UiComponent.php#L25
 */
class Layout extends UiComponent {
	/** 2016-01-01 Потрясающая техника, которую я изобрёл только что. */
	function __construct() {}

	/**
	 * 2016-06-06
	 * Цель плагина — устранение дефекта ядра, который проявляется в том,
	 * что непосредственно после авторизации посетителя через какой-либо сторонний сервис
	 * (в моих случаях: Facebook, Amazon) имя покупателя не отображается в шапке.
	 * Это блок @see \Magento\Customer\Block\Account\Customer не имеет атрибута «cacheable»:
	 * https://github.com/magento/magento2/blob/2.1.0-rc1/app/design/frontend/Magento/luma/Magento_Customer/layout/default.xml#L11
	 *
	 * Это разумно, потому что этот блок отображается на всех страницах витрины,
	 * и он используется не только для авторизованных посетителей, но и для анонимных,
	 * и если он будет «cacheable», то тогда полностраничное кэширование не будет работать вовсе.
	 *
	 * Однако из-за отсутствия атрибута «cacheable» система считает,
	 * что она может кэшировать страницу целиком:
	 * @see \Magento\Framework\View\Layout::isCacheable()
	 * https://github.com/magento/magento2/blob/2.1.0-rc1/lib/internal/Magento/Framework/View/Layout.php#L1073-L1083
	 *	function isCacheable()
	 *	{
	 *		$this->build();
	 *		$cacheableXml = !(bool)count($this->getXml()->xpath('//' . Element::TYPE_BLOCK . '[@cacheable="false"]'));
	 *		return $this->cacheable && $cacheableXml;
	 *	}
	 * Это, в принципе, ещё тоже само по себе не смертельно, ведь блок работает через AJAX,
	 * и по хорошему вполне бы мог корректно подгружать имя посетителя асинхронно
	 * даже при полностью закэшированной странице.
	 *
	 * Однако коварный метод @see \Magento\PageCache\Model\Layout\LayoutPlugin::afterGenerateXml()
	 * https://github.com/magento/magento2/blob/2.1.0-rc1/app/code/Magento/PageCache/Model/Layout/LayoutPlugin.php#L37-L51
	 * видит, что isCacheable() вернуло true, и устанавливает заголовок «Сache-Сontrol: public»:
	 *«Set appropriate Cache-Control headers. We have to set public headers in order to tell Varnish and Builtin app that page should be cached»:
	 *	 function afterGenerateXml(\Magento\Framework\View\Layout $subject, $result)
	 *	 {
	 *		 if ($subject->isCacheable() && $this->config->isEnabled()) {
	 *			 $this->response->setPublicHeaders($this->config->getTtl());
	 *		 }
	 *		 return $result;
	 *	 }
	 *
	 * Непосвящённому программисту может быть ещё неочевидно, что здесь такого особенного.
	 * Однако затем в дело вступает метод @see \Magento\Framework\App\PageCache\Kernel::process():
	 * https://github.com/magento/magento2/blob/2.1.0-rc1/lib/internal/Magento/Framework/App/PageCache/Kernel.php#L65-L90
	 * Он видит, что заголовок «Сache-Сontrol» начинается с «Сache-Сontrol: public»:
	 * if (preg_match('/public.*s-maxage=(\d+)/', $response->getHeader('Cache-Control')->getFieldValue(), $matches))
	 * ... и грохает все куки вызовом функции @see header_remove()
	 *	$response->clearHeader('Set-Cookie');
	 *	if (!headers_sent()) {
	 *		header_remove('Set-Cookie');
	 *	}
	 * Тут уже ясно, что наступает пипец, но может быть ещё неочевидно, какой именно.
	 * Пипец же в том, что в числе прочих грохается кука
	 * @see \Magento\Customer\Model\Customer\NotificationStorage::UPDATE_CUSTOMER_SESSION
	 * https://github.com/magento/magento2/blob/2.1.0-rc1/app/code/Magento/Customer/Model/Customer/NotificationStorage.php#L12
	 *
	 * Эта кука ранее была установлена методом
	 * @see \Magento\Customer\Model\Plugin\CustomerNotification::beforeDispatch():
	 * https://github.com/magento/magento2/blob/2.1.0-rc1/app/code/Magento/Customer/Model/Plugin/CustomerNotification.php#L70-L97
	 *
	 *	if ($this->state->getAreaCode() == Area::AREA_FRONTEND
	 *		&& $this->notificationStorage->isExists(
	 *		NotificationStorage::UPDATE_CUSTOMER_SESSION,
	 *		$this->session->getCustomerId()
	 *	)) {
	 *		...
	 *		$publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
	 *		$publicCookieMetadata->setDurationOneYear();
	 *		$publicCookieMetadata->setPath('/');
	 *		$publicCookieMetadata->setHttpOnly(false);
	 *		$this->cookieManager->setPublicCookie(
	 *			NotificationStorage::UPDATE_CUSTOMER_SESSION,
	 *			$this->session->getCustomerId(),
	 *			$publicCookieMetadata
	 *		);
	 * В свою очередь, в notification storage ключ UPDATE_CUSTOMER_SESSION устанавливается
	 * при сохранении покупателя:
	 * @see \Magento\Customer\Model\ResourceModel\Customer::_afterSave()
	 *	protected function _afterSave(\Magento\Framework\DataObject $customer)
	 *	{
	 *		$this->getNotificationStorage()->add(
	 *			NotificationStorage::UPDATE_CUSTOMER_SESSION,
	 *			$customer->getId()
	 *		);
	 *		return parent::_afterSave($customer);
	 *	}
	 * При авторизации покупателя через внешнюю систему мы как раз и делаем сохранение покупателя:
	 * ведь мы получаем данные покупателя из внешней системы и их надо сохранить в Magento:
	 * @see \Df\Sso\CustomerReturn::customer()
	 * https://github.com/mage2pro/core/blob/4cd771d1/Customer/External/ReturnT.php?ts=4#L191
	 *
	 * Итак, куки грохаются, ключ «update_customer_session» из кук пропадает.
	 * Что теперь происходит в браузере? Смотрим:
	 *	updateSession = $.cookieStorage.get('update_customer_session');
	 *	if (updateSession) {
	 *		mageStorage.post(
	 *			options.updateSessionUrl,
	 *			JSON.stringify({
	 *				'customer_id': updateSession,
	 *				'form_key': window.FORM_KEY
	 *			})
	 *		).done(
	 *			function() {
	 *				$.cookieStorage
	 *					.setConf({path: '/', expires: -1})
	 *					.set('update_customer_session', null)
	 *				;
	 *			}
	 *		);
	 *	}
	 * Вот именно здесь браузер должен поддягивать свежую информацию о покупателе.
	 * Но мы этого удовольствия лишены, потому что куки-то грохнуты.
	 * Вот для исправления этой ситуации и предназначен мой метод.
	 * @see \Magento\Framework\View\Layout::isCacheable()
	 *
	 * 2016-06-06
	 * df_cookie_m()->getCookie(NotificationStorage::UPDATE_CUSTOMER_SESSION)
	 * здесь нихуя не работает, потому что
	 * @see \Magento\Framework\Stdlib\Cookie\PhpCookieReader::getCookie()
	 * тупо смотрит в $_COOKIE (куки прошлого сеанса),
	 * но не смотрит те новые куки, которые мы установили в этом сеансе.
	 *
	 * @param Sb $sb
	 * @param bool $result
	 * @return bool
	 */
	function afterIsCacheable(Sb $sb, $result) {return $result && !dfc($this, function() {return
		!!df_find(function($h) {return
			/**
			 * 2016-11-21
			 * Константа @see \Magento\Customer\Model\Customer\NotificationStorage::UPDATE_CUSTOMER_SESSION
			 * и сам этот класс появились только в Magento 2.1.0-rc1:
			 * https://github.com/magento/magento2/commit/a73af29
			 */
			df_starts_with($h, 'Set-Cookie: update_customer_session')
			|| df_starts_with($h, 'Set-Cookie: ' . self::NEED_UPDATE_CUSTOMER_DATA)
		;}, headers_list())
	;});}

	/**
	 * 2015-09-19
	 * Цель метода — получение информации о формируемом в данный момент компоненте
	 * с целью индивидуального языкового перевода его интерфейса.
	 * @see \Magento\Framework\View\Layout::renderNonCachedElement()
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param string $name
	 * @return string
	 */
	function aroundRenderNonCachedElement(Sb $sb, \Closure $f, $name) {
		/** @var UiComponent|null $wrapper */
		$wrapper = $sb->isUiComponent($name) ? $sb->getUiComponent($name) : null;
		/** @var string $result */
		$wrapper ? df_state()->componentSet($wrapper->component) : null;
		try {$result = $f($name);}
		finally {$wrapper ? df_state()->componentSetPrev() : null;}
		return $result;
	}
	
	/**
	 * 2016-11-21
	 * @used-by afterIsCacheable()
	 * @used-by \Dfe\AmazonLogin\Controller\Index\Index::postProcess()
	 * @used-by https://github.com/mage2pro/amazon/blob/654bbbbc/view/frontend/web/invalidate.js#L10
	 */
	const NEED_UPDATE_CUSTOMER_DATA = 'df_need_update_customer_data';	
}