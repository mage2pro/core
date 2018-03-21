<?php
namespace Df\Framework\Mail;
use Magento\Framework\Mail\Message;
use Magento\Framework\Mail\MessageInterface as IMessage;
use Magento\Framework\Mail\TransportInterface as ITransport;
/**
 * 2018-01-28
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * @see \Dfe\Mailgun\Transport
 * @see \Dfe\SMTP\Transport
 */
abstract class Transport implements ITransport {
	/**
	 * 2018-01-28
	 * I implemented it by analogy with @see \Magento\Framework\Mail\Transport::__construct()
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @used-by \Df\Framework\Plugin\Mail\TransportInterfaceFactory::aroundCreate()
	 * @see \Magento\Framework\Mail\Template\TransportBuilder::getTransport():
	 * 		$mailTransport = $this->mailTransportFactory->create(['message' => clone $this->message]);
	 * @param IMessage|Message $message
	 */
    function __construct(IMessage $message) {$this->_message = $message;}

	/**
	 * 2018-01-28
	 * I implemented it by analogy with @see \Magento\Framework\Mail\Transport::getMessage()
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see ITransport::getMessage()
	 * @used-by \Dfe\SMTP\Transport::sendMessage()
	 * @return IMessage|Message
	 */
	function getMessage() {return $this->_message;}

	/**
	 * 2018-01-28
	 * @used-by __construct()
	 * @used-by getMessage()
	 * @var IMessage|Message
	 */
    private $_message;
}