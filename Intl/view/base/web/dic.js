// 2017-06-14
define([], function() {return {
	/**
	 * 2017-06-14
	 * @used-by Df_Intl/t
	 * @param {String} key
	 * @returns {String=}
	 */
	get(key) {return this._dic[key];}
	/**
	 * 2017-06-14
	 * @used-by Df_Intl/main
	 * @param {Object} dic
	 */
	,set(dic) {this._dic = dic;}
}});