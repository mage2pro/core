// 2016-12-03
define(['df', 'jquery', 'domReady!'], function(df, $) {return (
/**
 * @param {Object} config
 * Минимальная длина пароля — 8 символов.
 */
function(config) {$(':password').val(df.s.uid(8)).closest('.field').hide();});});