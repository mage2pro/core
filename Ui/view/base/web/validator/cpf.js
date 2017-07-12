// 2017-07-12
// https://gist.github.com/cauerego/873308#file-jquery-validate-cpf-js
define(['Df_Ui/validator'], function(v) {
	var f = function(v) {
		v = v.replace(/\D/g,'');
		/** @type {Boolean} */ var result = true; 
		/** @type {Number} */ var add = 0; 
		for (var i = 0; i < 9; i++) {
			add += parseInt(v.charAt(i), 10) * (10 - i);
		}
		var rev = 11 - (add % 11);
		if (rev == 10 || rev == 11) {
			rev = 0;
		}
		if (rev !== parseInt(v.charAt(9), 10)) {
			result = false;
		}
		else {
			add = 0;
			for (i = 0; i < 10; i++) {
				add += parseInt(v.charAt(i), 10) * (11 - i);
			}
			rev = 11 - (add % 11);
			if (rev == 10 || rev == 11) {
				rev = 0;
			}
			if (rev != parseInt(v.charAt(10), 10)) {
				result = false;
			}
		}
		return result;
	};
	v.add('cpf', f, v.peav('CPF'));
});