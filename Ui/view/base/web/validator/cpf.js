// 2017-07-12
// https://gist.github.com/cauerego/873308#file-jquery-validate-cpf-js
define(['df', 'Df_Ui/validator'], function(df, v) {
	var f = function(v) {
		v = v.replace(/\D/g,'');
		/** @type {Boolean} */ var result = true; 
		/** @type {Number} */ var add = 0; 
		for (var i = 0; i < 9; i++) {
			add += df.int(v.charAt(i)) * (10 - i);
		}
		var rev = 11 - (add % 11);
		if (rev == 10 || rev == 11) {
			rev = 0;
		}
		if (rev !== df.int(v.charAt(9))) {
			result = false;
		}
		else {
			add = 0;
			for (i = 0; i < 10; i++) {
				add += df.int(v.charAt(i)) * (11 - i);
			}
			rev = 11 - (add % 11);
			if (rev == 10 || rev == 11) {
				rev = 0;
			}
			if (rev != df.int(v.charAt(10))) {
				result = false;
			}
		}
		return result;
	};
	v.add('cpf', f, v.peav('CPF'));
});