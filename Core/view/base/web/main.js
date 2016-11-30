// 2016-11-30
// «How to get the current design package and theme names programmatically
// on the browser side (with JavaScript)?» https://mage2.pro/t/2350
/** @type {String} */
var a = require.toUrl('').split('/').slice(-4);
// 2016-11-30
// The DOM Ready event is intentionally ignored, because we need only the root <html> tag.
// document.documentElement is the root <html> tag: http://stackoverflow.com/a/22873490
// A possible result class: « df-package-Magento df-theme-luma»
document.documentElement.className += ' df-package-' + a[0] + ' df-theme-' + a[1];