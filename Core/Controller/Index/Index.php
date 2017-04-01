<?php
namespace Df\Core\Controller\Index;
use Df\Framework\Controller\Result\Json;
use Magento\Framework\App\Action\Action as _P;
// 2017-04-01
class Index extends _P {
	/**    
	 * 2017-04-01
	 * @override
	 * @see _P::execute()
	 * @return Json
	 */
	function execute() {return df_response_sign(Json::i(array_map(function($name) {return
		dfa_select_ordered(df_composer_json($name), [
			'name', 'version', 'description', 'type', 'homepage', 'license', 'authors'
		])
	;}, df_modules_my())));}
}