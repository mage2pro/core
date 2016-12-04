<?php
use Magento\Framework\Message\MessageInterface as IMessage;
use Magento\Framework\Phrase;

/**
 * 2016-08-02
 * An arbitrary non-existent identifier allows to preserve the HTML tags in the message.
 * @see \Magento\Framework\View\Element\Message\InterpretationMediator::interpret()
 * https://github.com/magento/magento2/blob/2.1.0/lib/internal/Magento/Framework/View/Element/Message/InterpretationMediator.php#L26-L43
 * @param string|Phrase $text
 * @param string $type
 * @return void
 */
function df_message_add($text, $type) {
	/** @var IMessage $message */
	$message = df_message_m()->createMessage($type, 'non-existent');
	$message->setText(df_phrase($text));
	df_message_m()->addMessage($message, null);
}

/**
 * 2016-08-02
 * @param string|Phrase|\Exception $message
 */
function df_message_error($message) {df_message_add(df_ets($message), IMessage::TYPE_ERROR);}

/**
 * https://mage2.pro/t/974
 * @return \Magento\Framework\Message\ManagerInterface|\Magento\Framework\Message\Manager
 */
function df_message_m() {return df_o(\Magento\Framework\Message\ManagerInterface::class);}

/**
 * 2016-12-04
 * @param string|Phrase $message
 */
function df_message_success($message) {df_message_add($message, IMessage::TYPE_SUCCESS);}