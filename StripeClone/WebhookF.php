<?php
// 2017-01-03
namespace Df\StripeClone;
use Df\Core\Exception as DFE;
use Df\StripeClone\Settings as S;
abstract class WebhookF extends \Df\Payment\WebhookF {
	/**
	 * 2017-01-04
	 * @used-by \Df\Payment\Action\Webhook::execute()
	 */
	public function __construct() {$this->ss()->init();}

	/**
	 * 2017-01-03
	 * @override
	 * @see \Df\Payment\WebhookF::_class()
	 * @used-by \Df\Payment\WebhookF::i()
	 * @param string|object $module
	 * @param array(string => mixed) $req
	 * @param array(string => mixed) $extra [optional]
	 * @return string
	 * @throws DFE
	 */
	final protected function _class($module, array $req, array $extra = []) {
		// 2016-03-18
		// https://stripe.com/docs/api#event_object-type
		// Пример события с обоими разделителями: «charge.dispute.funds_reinstated»
		/** @var string $s */
		$s = df_cc_class_uc(df_explode_multiple(['.', '_'], $req['type']));
		return $s ? df_con($module, df_cc_class('Webhook', $s)) : df_error('The request is invalid.');
	}

	/**
	 * 2017-01-04
	 * @override
	 * @see \Df\Payment\WebhookF::req()
	 * @used-by \Df\Payment\WebhookF::i()
	 * @return array(string => mixed)
	 */
	final protected function req() {return file_get_contents('php://input');}

	/**
	 * 2016-12-25
	 * @return S
	 */
	final protected function ss() {return dfc($this, function() {return S::conventionB(static::class);});}
}

