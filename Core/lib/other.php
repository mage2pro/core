<?php
use Magento\Framework\Event\Manager;
use Magento\Framework\Event\ManagerInterface as IManager;
/**
 * 2015-08-16
 * https://mage2.ru/t/95
 * https://mage2.pro/t/60
 * @used-by \Df\Backend\Model\Auth::loginByEmail()
 * @used-by \Df\Framework\Plugin\App\ResponseInterface::afterSendResponse()
 * @used-by \Df\Framework\Plugin\Mail\TransportInterfaceFactory::aroundCreate()
 * @used-by \Df\Framework\Plugin\View\Element\UiComponent\DataProvider\DataProvider::afterGetSearchResult()
 * @used-by \Df\Payment\Method::isAvailable()
 * @used-by \Df\Sales\Plugin\Model\Order\Address\Renderer::aroundFormat()
 * @used-by \Df\Sso\CustomerReturn::mc()
 * @used-by \Df\Sso\CustomerReturn::register()
 * @used-by \Df\Ui\Plugin\Component\Listing\Columns\Column::beforePrepare()
 * @used-by \Df\User\Plugin\Model\User::aroundAuthenticate()
 * @param string $eventName
 * @param array(string => mixed) $data
 */
function df_dispatch($eventName, array $data = []) {
	$manager = df_o(IManager::class); /** @var IManager|Manager $manager */
	$manager->dispatch($eventName, $data);
}

