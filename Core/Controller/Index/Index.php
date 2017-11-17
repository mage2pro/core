<?php
namespace Df\Core\Controller\Index;
use Df\Framework\W\Result\Json;
use Magento\Framework\App\Action\Action as _P;
// 2017-04-01
class Index extends _P {
	/**    
	 * 2017-04-01
	 * @override
	 * @see _P::execute()    
	 * @used-by \Magento\Framework\App\Action\Action::dispatch():
	 * 		$result = $this->execute();
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/App/Action/Action.php#L84-L125
	 * @return Json
	 */
	function execute() {return df_response_sign(Json::i(array_map(function($c) {return
		dfa_select_ordered($c, ['type', 'description', 'homepage', 'license', 'authors'])
	;}, dfe_modules_info())));}
}