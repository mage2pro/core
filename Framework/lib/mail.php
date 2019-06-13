<?php
use Magento\Email\Model\Transport as T;
use Magento\Framework\Mail\Message as Msg;
use Magento\Framework\Mail\TransportInterface as IT;
/**
 * 2019-06-13
 * @param string|string[] $to
 * @param string $subject
 * @param string $body
 */
function df_mail($to, $subject, $body) {
	$msg = df_new_om(Msg::class); /** @var Msg $msg */
	df_map(function($to) use($msg) {
		$msg->addTo($to);
	}, dfa_flatten(array_map('df_csv_parse', is_array($to) ? $to : [$to])));
	$msg
		->setBodyHtml($body)
		->setFrom(df_cfg('trans_email/ident_general/email'))
		->setSubject($subject)
	;
	$t = df_new_om(IT::class, ['message' => $msg]); /** @var IT|T $t */
	$t->sendMessage();
}