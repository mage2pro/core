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
	public function execute() {
		/**
		 * 2015-11-29
		 * @see \Magento\Framework\App\Response\Http::setNoCacheHeaders()
		 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/App/Response/Http.php#L133-L138
		 */
		df_response_code(200);
		df_response_content_type('image/' . $this->type());
		df_response_headers([
			'Cache-Control' => 'max-age=315360000'
			,'Content-Transfer-Encoding' => 'binary'
			,'Content-Length' => strlen($this->contents())
			,'Expires' => 'Thu, 31 Dec 2037 23:55:55 GMT'
			/**
			 * Если не указывать заголовок Pragma, то будет добавлено Pragma: no-cache.
			 * Так и не разобрался, кто его добавляет. Может, PHP или веб-сервер.
			 * Простое df_response()->clearHeader('pragma');
			 * не позволяет от него избавиться.
			 * http://stackoverflow.com/questions/11992946
			 */
			,'Pragma' => 'cache'
		]);
		$this->_actionFlag->set('', self::FLAG_NO_POST_DISPATCH, true);
		return df_controller_raw($this->contents());
	}
}