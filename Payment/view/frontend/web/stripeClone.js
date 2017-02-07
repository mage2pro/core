// 2017-02-07
// 2017-02-05
define([
	'Df_Payment/card', 'https://bridge.paymill.com/'
], function(parent) {'use strict'; return parent.extend({
   /**
	* 2017-02-07
	* @returns {String}
 	*/
	publicKey: function() {return this.config('publicKey');},
});});