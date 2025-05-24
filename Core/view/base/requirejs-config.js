var config = {paths: {
	'df': 'Df_Core/my/all'
	,'dfHasValue': 'Df_Core/my/hasValue'
	// 2023-08-05
	// «Refused to load the script 'https://cdn.jsdelivr.net/lodash/4.14.1/lodash.js'
	// because it violates the following Content Security Policy directive: "script-src <…>"»:
	// https://github.com/mage2pro/core/issues/321
	,'df-lodash': 'https://cdn.jsdelivr.net/lodash/4.14.1/lodash'
	,'df-lodash-local': 'Df_Core/thirdParty/lodash'
	// 2019-08-01 https://github.com/flesler/jquery.scrollTo
	,'df-scroll': 'Df_Core/thirdParty/scrollTo'
	,'df-uniform': 'Df_Core/my/uniform'
	/**
	 * 2017-10-18
	 * Note 1. «JavaScript Unicode 8.0 Normalization - NFC, NFD, NFKC, NFKD»: https://github.com/walling/unorm
	 * Note 2.
	 * `The ineligible characters should be automatically replaced by the corresponding eligible ones
	 * while prefilling the cardholder's name
	 * (if «Prefill the cardholder's name from the billing address?» option is enabled)`:
	 * https://github.com/mage2pro/core/issues/37#issuecomment-337537967
	 */
	,'df-unorm': 'Df_Core/thirdParty/unorm'
	,'Df_Core/ColorPicker': 'Df_Core/thirdParty/ColorPicker/main'
	,'Df_Core/Handsontable': 'Df_Core/thirdParty/Handsontable/main'
	,'Df_Core/HighlightJs': 'Df_Core/thirdParty/HighlightJs/main'
	,'Df_Core/Mask': 'Df_Core/thirdParty/Mask/main'
	,'Df_Core/Select2': 'Df_Core/thirdParty/Select2/main'
	,'Df_Core/TableSorter': 'Df_Core/thirdParty/TableSorter/main'
}};