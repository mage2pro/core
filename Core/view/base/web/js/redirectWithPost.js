define(function() {
	/**
	 * 2016-07-05
	 * http://stackoverflow.com/a/133997
	 * @param {String} url
	 * @param {Object} params
	 */
	return function(url, params) {
	    // The rest of this code assumes you are not using a library.
	    // It can be made less wordy if you use one.
	    var form = document.createElement('form');
	    form.setAttribute('method', 'post');
	    form.setAttribute('action', url);
	    for(var key in params) {
	        if(params.hasOwnProperty(key)) {
	            var hiddenField = document.createElement('input');
	            hiddenField.setAttribute('type', 'hidden');
	            hiddenField.setAttribute('name', key);
	            hiddenField.setAttribute('value', params[key]);
	            form.appendChild(hiddenField);
	         }
	    }
	    document.body.appendChild(form);
	    form.submit();
	};
});