<?php
namespace Df\Framework\Plugin;
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
	function beforeCatchException(Sb $sb, B $b, E $e):void {df_log_l(null, $e);}
}