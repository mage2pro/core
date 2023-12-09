<?php
namespace Df\Framework\Plugin;
use Df\Framework\Log\Handler\JsMap;
use Df\Framework\Log\Latest;
use Magento\Framework\AppInterface as Sb;
use Magento\Framework\App\Bootstrap as B;
use \Exception as E;
# 2021-10-03
# "The backtrace is not logged for «no class registered for scheme» errors": https://github.com/mage2pro/core/issues/160
final class AppInterface {
	/**
	 * 2021-10-03
	 * "How is `Magento\Framework\AppInterface::catchException()` implemented?" https://mage2.pro/t/6291
	 * @see \Magento\Framework\AppInterface::catchException()
	 * @see \Magento\Framework\App\Http::catchException():
	 * 		return $this->exceptionHandler->handle($bootstrap, $exception, $this->_response, $this->_request);
	 * https://github.com/magento/magento2/blob/2.4.3/lib/internal/Magento/Framework/App/Http.php#L154-L157
	 * @see \Magento\Framework\App\Cron::catchException():
	 *		return false;
	 * https://github.com/magento/magento2/blob/2.4.3/lib/internal/Magento/Framework/App/Cron.php#L94-L97
	 * @see \Magento\Indexer\App\Indexer::catchException():
	 * 		return false;
	 * https://github.com/magento/magento2/blob/2.4.3/app/code/Magento/Indexer/App/Indexer.php#L77-L80
	 * @see \Magento\Backend\App\UserConfig::catchException():
	 * 		return false;
	 * https://github.com/magento/magento2/blob/2.4.3/app/code/Magento/Backend/App/UserConfig.php#L88-L91
	 * @see \Magento\MediaStorage\App\Media::catchException():
	 *		$this->response->setHttpResponseCode(404);
	 *		if ($bootstrap->isDeveloperMode()) {
	 *			$this->response->setHeader('Content-Type', 'text/plain');
	 *			$this->response->setBody($exception->getMessage() . "\n" . $exception->getTraceAsString());
	 *		}
	 *		$this->response->sendResponse();
	 *		return true;
	 * https://github.com/magento/magento2/blob/2.4.3/app/code/Magento/MediaStorage/App/Media.php#L266-L275
	 * @see \Magento\Framework\App\StaticResource::catchException():
	 *		$this->getLogger()->critical($exception->getMessage());
	 *		if ($bootstrap->isDeveloperMode()) {
	 *			$this->response->setHttpResponseCode(404);
	 *			$this->response->setHeader('Content-Type', 'text/plain');
	 *			$this->response->setBody(
	 *				$exception->getMessage() . "\n" .
	 *				Debug::trace(
	 *					$exception->getTrace(),
	 *					true,
	 *					true,
	 *					(bool)getenv('MAGE_DEBUG_SHOW_ARGS')
	 *				)
	 *			);
	 *			$this->response->sendResponse();
	 *		} else {
	 *			require $this->getFilesystem()->getDirectoryRead(DirectoryList::PUB)->getAbsolutePath('errors/404.php');
	 *		}
	 *		return true;
	 * https://github.com/magento/magento2/blob/2.4.3/lib/internal/Magento/Framework/App/StaticResource.php#L194-L214
	 */
	function beforeCatchException(Sb $sb, B $b, E $e):void {
		/**
		 * 2023-08-25 "Prevent logging of «Requested path <…>.js.map is wrong»": https://github.com/mage2pro/core/issues/323
		 * 2023-12-09
		 * 1) Some errors are logged twice: by @see \Df\Framework\Log\Dispatcher::handle()
		 * and @see \Df\Framework\Plugin\AppInterface::beforeCatchException(): https://github.com/mage2pro/core/issues/342
		 * 2) @see \Magento\Framework\App\Bootstrap::run():
		 * 		$this->objectManager->get(LoggerInterface::class)->error($e->getMessage());
		 * 		if (!$application->catchException($this, $e)) {
		 * 			throw $e;
		 * 		}
		 * https://github.com/magento/magento2/blob/2.4.7-beta2/lib/internal/Magento/Framework/App/Bootstrap.php#L269-L272
		 * 3) Magento ≥ 2.4.6 passes the exception to loggers:
		 *        $context = $this->addExceptionToContext($message, $context);
		 *  https://github.com/magento/magento2/blob/2.4.6/lib/internal/Magento/Framework/Logger/LoggerProxy.php#L129
		 *  We can not use it because we need to support outdated Magento versions.
		 */
		if (!JsMap::is($e->getMessage()) && !Latest::registered($e)) {
			df_log($e);
		}
	}
}