<?php
// 2016-08-27
namespace Df\Payment\Action;
use Df\Framework\Controller\Result\Text;
use Df\Payment\Exception\Webhook\Factory as EFactory;
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
			/**
			 * 2017-01-11
			 * Не полагайтесь здесь особенно на @uses df_con_hier():
			 * чтобы эта функция была Вам полезна,
			 * Вам придётся строить иерархию наследования для Ваших Action.
			 * Например, наследовать @see \Dfe\Omise\Controller\Index\Index не от
			 * @see \Df\Payment\Action\Webhook, а создавать пустой класс \Df\StripeClone\Action\Webhook,
			 * и от него уже наследоваться.
			 * И вот тогда уже @uses df_con_hier() будет проходить по всей иерархии:
			 * Omise => StripeClone => Payment, и на каждом уровне искать фабрику (WebhookF),
			 * если она отсутствует на предыдущем.
			 * Если же такой иерархии Action у Вас нет,
			 * и Ваш Action прямо унаследован от @see \Df\Payment\Action\Webhook,
			 * то и @uses df_con_hier() будет искать фабрику всего в 2-х местах:
			 * сначала на уровне модуля Action, а затем сразу на уровне модуля Df_Payment.
			 *
			 * Для упрощения диагностики подобных ситуаций
			 * добавил проверку класса Webhook на абстрактность в методе @see \Df\Payment\WebhookF::i().
			 */
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
			df_log($e);
			if ($e instanceof EFactory && $e->req()) {
				dfp_log_l($this, $e->req());
			}
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