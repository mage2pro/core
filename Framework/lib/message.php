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
 * @used-by df_message_notice()
 * @used-by df_message_success()
 * @param string|Phrase $text
 * @param string $type
 */
function df_message_add($text, $type) {
	$m = df_message_m()->createMessage($type, 'non-existent'); /** @var IMessage $message */
	$m->setText(df_phrase($text));
	df_message_m()->addMessage($m, null);
}

/**
 * 2016-08-02
 * @used-by \Df\Config\Backend::save()
 * @used-by \Df\Config\Backend\Serialized::processA()
 * @used-by \Df\OAuth\ReturnT::execute()
 * @param string|Phrase|\Exception $m
 */
function df_message_error($m) {df_message_add(df_ets($m), IMessage::TYPE_ERROR);}

/**
 * https://mage2.pro/t/974
 * @return IMessageManager|MessageManager
 */
function df_message_m() {return df_o(IMessageManager::class);}

/**
 * 2018-05-11
 * @param string|Phrase $m
 */
function df_message_notice($m) {df_message_add($m, IMessage::TYPE_NOTICE);}

/**
 * 2016-12-04
 * @param string|Phrase $m
 */
function df_message_success($m) {df_message_add($m, IMessage::TYPE_SUCCESS);}