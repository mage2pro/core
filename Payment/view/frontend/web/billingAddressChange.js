// 2017-04-04
define(['df', 'Magento_Checkout/js/model/quote'], function(df, quote) {return (
/**
 * 2017-04-04
 * @used-by Df_Payment/card::initialize()
 * @used-by Df_Payment/mixin::initialize()
 * @used-by Dfe_Klarna/main::initialize()
 * @param {Object} _this
 * @param {Function} f
 * @param {Boolean=} fireNow
 */
function(_this, f, fireNow) {
	/** @type {?Object} */ var prevAddress;
	quote.billingAddress.subscribe(
		/**
		 * 2017-04-04
		 * newAddress is null if a customer has just unchecked
		 * the «My billing and shipping address are the same» checkbox,
		 * but does not select another address yet.
		 * 2017-07-22
		 * An address looks like:
		 *	{
		 *		"city": "Rio de Janeiro",
		 *		"countryId": "BR",
		 *		"customerAddressId": "7",
		 *		"customerId": "1",
		 *		"firstname": "Dmitry",
		 *		"lastname": "Fedyuk",
		 *		"postcode": "22630-010",
		 *		"region": "Rio de Janeiro",
		 *		"regionCode": "RJ",
		 *		"regionId": "502",
		 *		"saveInAddressBook": null,
		 *		"street": ["Av. Lúcio Costa, 3150 - Barra da Tijuca"],
		 *		"telephone": "+55 21 3139-8000",
		 *		"vatId": "11438374798"
		 *	}
		 * @used-by Magento_Checkout/js/action/select-billing-address:
		 * 		quote.billingAddress(address);
		 * https://github.com/magento/magento2/blob/2.1.5/app/code/Magento/Checkout/view/frontend/web/js/action/select-billing-address.js#L23
		 * @param {?Object} newAddress
		 * @param {String=} newAddress.city	«Rio de Janeiro»
		 * @param {String=} newAddress.countryId	«BR»
		 * @param {Number=} newAddress.customerAddressId	«7»
		 * @param {Number=} newAddress.customerId	«1»
		 * @param {String} newAddress.firstname	«Dmitry»
		 * @param {String} newAddress.lastname	«Fedyuk»
		 * @param {String=} newAddress.postcode	«22630-010»
		 * @param {String=} newAddress.region	«Rio de Janeiro»
		 * @param {String=} newAddress.regionCode	«RJ»
		 * @param {Number=} newAddress.regionId	«502»
		 * @param {?Boolean} newAddress.saveInAddressBook	«null»
		 * @param {String[]} newAddress.street	«["Av. Lúcio Costa, 3150 - Barra da Tijuca"]»
		 * @param {String=} newAddress.telephone	«+55 21 3139-8000»
		 * @param {String=} newAddress.vatId	«11438374798»
		 */
		function(newAddress) {
			/**
			 * 2017-04-04
			 * Note #1
			 * Naive newAddress !== prevAddress does not work correctly.
			 * @uses Magento_Customer/js/model/customer/address::getKey()
			 *	getKey: function() {
			 *		return this.getType() + this.customerAddressId;
			 *	}
			 * https://github.com/magento/magento2/blob/2.1.5/app/code/Magento/Customer/view/frontend/web/js/model/customer/address.js#L48-L50
			 * It returns a string like «customer-address4».
			 * Note #2
			 * !newAddress ^ !prevAddress is for situations
			 * when newAddress or prevAddress is null or prevAddress is undefined:
			 * http://stackoverflow.com/a/4540443
			 */
			if (!newAddress ^ !prevAddress
				|| (newAddress && prevAddress && newAddress.getKey() !== prevAddress.getKey())
			) {
				prevAddress = newAddress;
				if (newAddress) {
					f.call(_this, newAddress);
				}
			}
		}
	);
	if (df.arg(fireNow, true)) {
		f.call(_this, quote.billingAddress());
	}
});});