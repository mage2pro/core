<?php
namespace Df\Framework\App\Action;
// 2015-11-29
/** @see \Df\GoogleFont\Controller\Index\Preview */
abstract class Image extends \Magento\Framework\App\Action\Action {
	/**
	 * 2015-11-29
	 * Содержимое файла картинки.
	 * Например, его можно получить посредством @see file_get_contents()
	 * @see \Df\GoogleFont\Controller\Index\Preview::contents()
	 * @used-by execute()
	 * @return string
	 */
	abstract protected function contents();
	/**
	 * 2015-11-29 E.g.: 'png'
	 * @see \Df\GoogleFont\Controller\Index\Preview::type()
	 * @used-by execute()
	 * @return string
	 */
	abstract protected function type();

	/**
	 * 2015-11-29
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @used-by \Magento\Framework\App\Action\Action::dispatch():
	 * 		$result = $this->execute();
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/App/Action/Action.php#L84-L125
	 */
	function execute() {
		/**
		 * 2015-11-29
		 * @see \Magento\Framework\App\Response\Http::setNoCacheHeaders()
		 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/App/Response/Http.php#L133-L138
		 */
		df_response_code(200);
		df_response_content_type("image/{$this->type()}");
		df_response_cache_max();
		$c = $this->contents(); /** @var string $c */
		df_response_headers(['Content-Length' => strlen($c), 'Content-Transfer-Encoding' => 'binary']);
		$this->_actionFlag->set('', self::FLAG_NO_POST_DISPATCH, true);
		df_response()->setBody($c);
	}
}