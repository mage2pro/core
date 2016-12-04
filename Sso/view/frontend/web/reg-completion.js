/**
 * 2016-12-03
 * Этот код вызывается в том случае, когда данных от провайдера SSO недостаточно
 * для автоматической регистрации покупателя в Magento,
 * и поэтому от покупателя требуется ручное дозаполнение недостающих данных
 * (случай Blackbaud NetCommunity).
 *
 * В этом случае запрашивать у покупателя пароль не нужно,
 * потому что покупатель будет аутентифицироваться в магазине посредством провайдера SSO.
 * При этом у покупателя всё-таки будет возможность установить себе пароль
 * будет сброса автоматически созданного здесь пароля,
 * следуя стандартной для Magento процедуре восстановления пароля.
 *
 * Остальные поля (за исключение полей для пароля)
 * мы автозаполняем в методе @see \Df\Customer\Plugin\Block\Form\Register::afterGetFormData()
 * https://github.com/mage2pro/core/blob/0d41e0/Customer/Plugin/Block/Form/Register.php?ts=4#L31
 */
define(['df', 'jquery', 'domReady!'], function(df, $) {return (
/**
 * @param {Object} config
 * Минимальная длина пароля — 8 символов.
 * Также нужны спецсимволы:
 * «Minimum of different classes of characters in password is 3.
 * Classes of characters: Lower Case, Upper Case, Digits, Special Characters.»
 */
function(config) {$(':password').val(df.s.uid(6, 'aA0%')).closest('.field').hide();});});