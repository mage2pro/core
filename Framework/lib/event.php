<?php
use Magento\Framework\Event\Manager;
use Magento\Framework\Event\ManagerInterface as IManager;
/**
 * 2015-08-16
 * https://mage2.ru/t/95
 * https://mage2.pro/t/60
 * @used-by \CanadaSatellite\Bambora\Method::isAvailable() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @used-by \Df\Backend\Model\Auth::loginByEmail()
 * @used-by \Df\Framework\Log\Dispatcher::handle()
 * @used-by \Df\Framework\Plugin\App\ResponseInterface::afterSendResponse()
 * @used-by \Df\Framework\Plugin\Mail\TransportInterfaceFactory::aroundCreate()
 * @used-by \Df\Framework\Plugin\View\Element\UiComponent\DataProvider\DataProvider::afterGetSearchResult()
 * @used-by \Df\Payment\Method::isAvailable()
 * @used-by \Df\Sales\Plugin\Model\Order\Address\Renderer::aroundFormat()
 * @used-by \Df\Sso\CustomerReturn::mc()
 * @used-by \Df\Sso\CustomerReturn::register()
 * @used-by \Df\Ui\Plugin\Component\Listing\Columns\Column::beforePrepare()
 * @used-by \Df\User\Plugin\Model\User::aroundAuthenticate()
 * @param string $ev
 * @param array(string => mixed) $d
 */
function df_dispatch($ev, array $d = []):array {
	$m = df_o(IManager::class); /** @var IManager|Manager $m */
	$m->dispatch($ev, $d);
}