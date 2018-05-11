<?php
use Magento\Framework\Message\Manager as MessageManager;
use Magento\Framework\Message\ManagerInterface as IMessageManager;
use Magento\Framework\Message\MessageInterface as IMessage;
use Magento\Framework\Phrase;

/**
 * 2016-08-02
 * An arbitrary non-existent identifier allows to preserve the HTML tags in the message.
 * @see \Magento\Framework\View\Element\Message\InterpretationMediator::interpret()
 * https://github.com/magento/magento2/blob/2.1.0/lib/internal/Magento/Framework/View/Element/Message/InterpretationMediator.php#L26-L43
 * @used-by df_message_error()
 * @used-by df_message_success()
 * @param string|Phrase $text
 * @param string $type
 */
function df_message_add($text, $type) {
	/** @var IMessage $message */
	$message = df_message_m()->createMessage($type, 'non-existent');
	$message->setText(df_phrase($text));
	df_message_m()->addMessage($message, null);
}

/**
 * 2016-08-02
 * @used-by \Df\Config\Backend::save()
 * @used-by \Df\Config\Backend\Serialized::processA()
 * @used-by \Df\OAuth\ReturnT::execute()
 * @param string|Phrase|\Exception $message
 */
function df_message_error($message) {df_message_add(df_ets($message), IMessage::TYPE_ERROR);}

/**
 * https://mage2.pro/t/974
 * @return IMessageManager|MessageManager
 */
function df_message_m() {return df_o(IMessageManager::class);}

/**
 * 2016-12-04
 * @param string|Phrase $message
 */
function df_message_success($message) {df_message_add($message, IMessage::TYPE_SUCCESS);}