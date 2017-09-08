// 2017-07-12 https://gist.github.com/cauerego/873308#file-jquery-validate-cpf-js
define(['df', 'df-lodash', 'Df_Ui/validator'], function(df, _, validator) {
	var f = function(v) {
		v = _.trim(v);
		/** @type {Boolean} */ var result = !/\D/.test(v);
		if (result) {
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
		}
		return result;
	};
	validator.add('cpf', f, validator.peav('CPF'));
});