<?php
// 2016-08-27
namespace Df\Payment\Action;
use Df\Framework\Controller\Result\Text;
use Df\Framework\Request as Req;
use Df\Payment\Webhook as W;
use Df\Payment\WebhookF;
class Webhook extends \Df\Payment\Action {
	/**
	 * 2016-08-27
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @return \Df\Framework\Controller\Result\Text
	 */
	public function execute() {
		/** @var Text $result */
		try {
			/** @var WebhookF $f */
			$f = df_create(df_con_heir($this, WebhookF::class));
			/** @var W $w */
			$w = $f->i($this, Req::clean(), Req::extra());
			$this->prepare($w);
			$result = $w->handle();
		}
		catch (\Exception $e) {
			df_sentry($e);
			$result = $this->error($e);
		}
		return $result;
	}

	/**
	 * 2017-01-02
	 * @used-by execute()
	 * @param \Exception $e
	 * @return $this
	 */
	protected function error(\Exception $e) {return W::resultError($e);}

	/**
	 * 2017-01-02
	 * @used-by execute()
	 * @see \Dfe\AllPay\Controller\Offline\Index::prepare()
	 * @param W $w
	 * @return void
	 */
	protected function prepare(W $w) {}
}