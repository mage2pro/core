<?php
namespace Df\Framework\App\Request;
/**
 * 2017-03-19
 * @used-by \Df\Framework\Action::module()
 * @method string getControllerModule()
 * Возвращает имя модуля в формате «Dfe_Stripe».
 * @see \Magento\Framework\App\Router\Base::matchAction():
 * 		$request->setControllerModule($currentModuleName);
 * https://github.com/magento/magento2/blob/2.1.5/lib/internal/Magento/Framework/App/Router/Base.php#L313
 */
class Http extends \Magento\Framework\App\Request\Http {}