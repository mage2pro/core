// 2016-08-04
define (['./mixin', 'underscore'], function(mixin, _) {return _.extend(mixin, {
	df: {
		card: {
			expirationMonth: 'expirationMonth'
			,expirationYear: 'expirationYear'
			,number: 'number'
			,verification: 'verification'
		}
	},
	dfCardExpirationMonth: function() {return this.dfForm(this.df.card.expirationMonth);},
	dfCardExpirationYear: function() {return this.dfForm(this.df.card.expirationYear);},
	dfCardNumber: function() {return this.dfForm(this.df.card.number);},
	dfCardVerification: function() {return this.dfForm(this.df.card.verification);}
});});
