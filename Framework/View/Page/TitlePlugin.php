<?php
namespace Df\Framework\View\Page;
use Magento\Framework\View\Page\Title;
class TitlePlugin {
	/**
	 * 2015-09-27
	 * Цель метода — получение информации о формировании в данный момент заголовка страницы.
	 * @uses \Magento\Framework\View\Page\Title::get()
	 * @param Title $subject
	 * @param \Closure $proceed
	 * @return string
	 */
	public function aroundGet(Title $subject, \Closure $proceed) {
		rm_state()->renderingTitle(true);
		try {
			$result = $proceed();
			/**
			 * Делаем браузерные заголовки административной части
			 * более короткими и понятными: оставляем лишь первую и последнюю части заголовка.
			 */
			if (df_is_admin()) {
				/** @var string[] $resultA */
				$resultA = explode(Title::TITLE_GLUE, $result);
				$result =
					3 > count($resultA)
					? $result
					: implode(Title::TITLE_GLUE, array(rm_first($resultA), rm_last($resultA)))
				;
			}
		}
		finally {
			rm_state()->renderingTitle(false);
		}
		return $result;
	}
}