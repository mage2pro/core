<?php
use Magento\Framework\Event\Manager;
use Magento\Framework\Event\ManagerInterface as IManager;
/**
 * 2015-08-16
 * https://mage2.ru/t/95
 * https://mage2.pro/t/60
 * @used-by \Df\Backend\Model\Auth::loginByEmail()
 * @used-by \Df\Framework\Logger\Handler::handle()
 * @used-by \Df\Framework\Plugin\App\ResponseInterface::afterSendResponse()
 * @used-by \Df\Framework\Plugin\Mail\TransportInterfaceFactory::aroundCreate()
 * @used-by \Df\Framework\Plugin\View\Element\UiComponent\DataProvider\DataProvider::afterGetSearchResult()
 * @used-by \Df\Payment\Method::isAvailable()
 * @used-by \Df\Sales\Plugin\Model\Order\Address\Renderer::aroundFormat()
 * @used-by \Df\Sso\CustomerReturn::mc()
 * @used-by \Df\Sso\CustomerReturn::register()
 * @used-by \Df\Ui\Plugin\Component\Listing\Columns\Column::beforePrepare()
 * @used-by \Df\User\Plugin\Model\User::aroundAuthenticate()
 * @used-by \Justuno\M2\Controller\Cart\Add::execute()
 * @used-by \CanadaSatellite\Bambora\Model\Beanstream::isAvailable() (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @param string $ev
 * @param array(string => mixed) $d
 */
function df_dispatch($ev, array $d = []) {
	$m = df_o(IManager::class); /** @var IManager|Manager $m */
	$m->dispatch($ev, $d);
}