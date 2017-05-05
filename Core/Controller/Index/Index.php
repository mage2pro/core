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
	function execute() {return df_response_sign(Json::i(array_map(function($c) {return
		dfa_select_ordered($c, ['type', 'description', 'homepage', 'license', 'authors'])
	;}, dfe_modules_info())));}
}