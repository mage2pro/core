<?php
use Magento\Framework\Message\Manager as MM;
use Magento\Framework\Message\ManagerInterface as IMM;
use Magento\Framework\Message\MessageInterface as IM;
use Magento\Framework\Phrase as P;

/**
 * 2016-08-02
 * An arbitrary non-existent identifier allows to preserve the HTML tags in the message.
 * @see \Magento\Framework\View\Element\Message\InterpretationMediator::interpret()
 * https://github.com/magento/magento2/blob/2.1.0/lib/internal/Magento/Framework/View/Element/Message/InterpretationMediator.php#L26-L43
 * @used-by df_message_error()
 * @used-by df_message_notice()
 * @used-by df_message_success()
 * @param P|string $s
 * @param string $type
 */
function df_message_add($s, $type):void {df_message_m()->addMessage(
	df_message_m()->createMessage($type, 'non-existent')->setText(df_phrase($s)), null
);}

/**
 * 2016-08-02
 * @used-by \Df\Config\Backend::save()
 * @used-by \Df\Config\Backend\Serialized::processA()
 * @used-by \Df\OAuth\ReturnT::execute()
 * @used-by \RWCandy\Captcha\Observer\CustomerAccountCreatePost::execute()
 * @param string|P|Exception $m
 */
function df_message_error($m):void {df_message_add(df_xts($m), IM::TYPE_ERROR);}

/**
 * 2016-08-02 https://mage2.pro/t/974
 * @used-by df_message_add()
 * @return IMM|MM
 */
function df_message_m() {return df_o(IMM::class);}

/**
 * 2018-05-11
 * @used-by \MageSuper\Casat\Observer\ProductSaveBefore::execute() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/73)
 * @param P|string $m
 */
function df_message_notice($m):void {df_message_add($m, IM::TYPE_NOTICE);}

/**
 * 2016-12-04
 * @used-by \Df\Sso\CustomerReturn::_execute()
 * @param P|string $m
 */
function df_message_success($m):void {df_message_add($m, IM::TYPE_SUCCESS);}