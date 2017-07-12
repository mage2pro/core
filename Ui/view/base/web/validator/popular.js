// 2017-07-12
define(['Df_Ui/validator', 'jquery'], function(v, $) {$.each([
	[
		'domain'
		// 2017-07-09 https://stackoverflow.com/a/27776532
		,function(v) {return /^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9](?:\.[a-zA-Z]{2,})+$/.test(v);}
		,v.peav("<a href='https://en.wikipedia.org/wiki/Domain_name' target='_blank'>domain name</a>")
	]
], function(dummy, i) {v.add.apply(v, i);});});