// 2020-04-30
// "Prevent error messages from being hidden in 5 seconds from the frontend checkout screen":
// https://github.com/mage2pro/core/issues/98
define([], function() {'use strict'; return function(sb) {return sb.extend({onHiddenChange: function() {}});};});