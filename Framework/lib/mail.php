<?php
use Magento\Email\Model\Template;
use Magento\Email\Model\Transport;
use Magento\Framework\Mail\Message as Msg;
use Magento\Framework\Mail\Template\Factory;
use Magento\Framework\Mail\Template\FactoryInterface as IFactory;
use Magento\Framework\Mail\TemplateInterface as ITemplate;
use Magento\Framework\Mail\TransportInterface as ITransport;
/**
 * 2019-06-13
 * @used-by \KingPalm\B2B\Observer\AdminhtmlCustomerPrepareSave::execute()
 * @used-by \KingPalm\B2B\Observer\RegisterSuccess::execute()
 * @param string|string[] $to
 */
function df_mail($to, string $subject, string $body):void {
	$msg = df_new_om(Msg::class); /** @var Msg $msg */
	df_map(function($to) use($msg) {
		$msg->addTo($to);
	}, dfa_flatten(array_map('df_csv_parse', is_array($to) ? $to : [$to]))); /** @uses df_csv_parse() */
	$msg
		->setBodyHtml($body)
		->setFrom(df_cfg('trans_email/ident_general/email'))
		->setSubject($subject)
	;
	$t = df_new_om(ITransport::class, ['message' => $msg]); /** @var ITransport|Transport $t */
	$t->sendMessage();
}

/**
 * 2019-06-20
 * @used-by \KingPalm\B2B\Observer\AdminhtmlCustomerPrepareSave::execute()
 * @return Template|ITemplate
 */
function df_mail_t(int $id) {return df_mail_tf()->get($id);}

/**
 * 2019-06-20
 * @used-by df_mail_t()
 * @return Factory|IFactory
 */
function df_mail_tf() {return df_o(IFactory::class);}