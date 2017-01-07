<?php
// 2016-08-27
namespace Df\Payment\Action;
use Df\Framework\Controller\Result\Text;
use Df\Payment\Exception\Webhook\NotImplemented;
use Df\Payment\Webhook as W;
use Df\Payment\WebhookF;
class Webhook extends \Df\Payment\Action {
	/**
	 * 2016-08-27
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @return Text
	 */
	public function execute() {
		/** @var Text $result */
		try {
			// 2017-01-04
			// Объединять это выражение с new нельзя: https://3v4l.org/U6TJR
			/** @var string $fc */
			$fc = df_con_hier($this, WebhookF::class);
			/** @var WebhookF $f */
			$f = new $fc($this);
			/** @var W $w */
			$w = $f->i();
			$this->prepare($w);
			$result = $w->handle();
		}
		catch (NotImplemented $e) {
			$result = Text::i($e->getMessage());
		}
		catch (\Exception $e) {
			df_sentry($e);
			$result = $this->error($e);
		}
		/**
		 * 2017-01-07
		 * Иначе мы можем получить сложнодиагностируемый сбой «Invalid return type».
		 * @see \Magento\Framework\App\Http::launch()
		 * https://github.com/magento/magento2/blob/2.1.3/lib/internal/Magento/Framework/App/Http.php#L137-L145
		 */
		return df_ar($result, Text::class);
	}

	/**
	 * 2017-01-02
	 * @used-by execute()
	 * @see \Dfe\AllPay\Controller\Confirm\Index::error()
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