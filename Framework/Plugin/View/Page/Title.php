<?php
namespace Df\Framework\Plugin\View\Page;
use Magento\Framework\View\Page\Title as Sb;
class Title {
	/**
	 * 2015-09-27
	 * Цель метода — получение информации о формировании в данный момент заголовка страницы.
	 * @uses \Magento\Framework\View\Page\Title::get()
	 * @param Sb $sb
	 * @param \Closure $f
	 * @return string
	 */
	function aroundGet(Sb $sb, \Closure $f) {
		df_state()->renderingTitle(true);
		try {
			$result = $f();
			// Делаем браузерные заголовки административной части
			// более короткими и понятными: оставляем лишь первую и последнюю части заголовка.
			/** @var string[] $resultA */
			if (df_is_backend() && 2 < count($resultA = explode(Sb::TITLE_GLUE, $result))) {
				$result = implode(Sb::TITLE_GLUE, [df_first($resultA), df_last($resultA)]);
			}
		}
		finally {
			df_state()->renderingTitle(false);
		}
		return $result;
	}
}