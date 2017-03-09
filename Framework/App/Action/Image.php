<?php
namespace Df\Framework\App\Action;
abstract class Image extends \Magento\Framework\App\Action\Action {
	/**
	 * 2015-11-29
	 * Содержимое файла картинки.
	 * Например, его можно получить посредством @see file_get_contents()
	 * @return string
	 */
	abstract protected function contents();
	/**
	 * 2015-11-29
	 * Например: 'png'
	 * @return string
	 */
	abstract protected function type();
	
	/**
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @return \Magento\Framework\Controller\Result\Raw
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
		df_response_headers([
			'Content-Length' => strlen($this->contents()), 'Content-Transfer-Encoding' => 'binary'
		]);
		$this->_actionFlag->set('', self::FLAG_NO_POST_DISPATCH, true);
		return df_controller_raw($this->contents());
	}
}