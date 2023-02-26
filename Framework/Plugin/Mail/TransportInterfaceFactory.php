<?php
namespace Df\Framework\Plugin\Mail;
use Magento\Framework\DataObject as _DO;
use Magento\Framework\Mail\TransportInterface as ITransport;
use Magento\Framework\Mail\TransportInterfaceFactory as Sb;
# 2018-01-28
final class TransportInterfaceFactory {
	/**
	 * 2018-01-28
	 * The purpose of this plugin is to provide an ability to my Dfe_Mailgun and Dfe_SMTP modules
	 * to use an alternative mail transport instead of \Laminas\Mail\Transport\Sendmail
	 * @see \Magento\Email\Model\Transport::__construct():
	 * 		$this->zendTransport = new Sendmail($parameters);
	 * https://github.com/magento/magento2/blob/1a81e05b/app/code/Magento/Email/Model/Transport.php#L73
	 * https://github.com/mage2pro/mailgun
	 * https://github.com/mage2pro/smtp
	 * @see \Magento\Framework\Mail\TransportInterfaceFactory::create():
	 *		public function create(array $data = []) {
	 *			return $this->_objectManager->create($this->_instanceName, $data);
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.2/lib/internal/Magento/Framework/Mail/TransportInterfaceFactory.php#L42-L51
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Mail/TransportInterfaceFactory.php#L42-L51
	 */
	function aroundCreate(Sb $sb, \Closure $f, array $data = []):ITransport {
		$container = new _DO; /** @var _DO $container */
		/** 2018-01-28 @used-by \Df\Framework\Mail\TransportObserver::execute() */
		df_dispatch('df_mail_transport', [self::CONTAINER => $container]);
		/** @var string|null $c */
		return ($c = $container[self::K_TRANSPORT]) ? df_new_om($c, $data) : $f($data);
	}

	/**
	 * 2018-01-28
	 * @used-by self::aroundCreate()
	 * @used-by \Df\Framework\Mail\TransportObserver::execute()
	 */
	const CONTAINER = 'container';
	/**
	 * 2018-01-28
	 * @used-by self::aroundCreate()
	 * @used-by \Df\Framework\Mail\TransportObserver::execute()
	 */
	const K_TRANSPORT = 'transport';
}