<?php
/**
 * 2015-02-11
 * Превращает массив вида array('value' => 'label')
 * в массив вида array(array('value' => '', 'label' => ''))
 * Обратная операция: @see df_options_to_map()
 *
 * @param array(string|int => string) $map
 * @param object|string|null $module [optional]
 * @return array(array(string => string|int))
 */
function df_map_to_options(array $map) {return array_map('df_option', array_keys($map), $map);}

/**
 * 2015-11-13
 * Делает то же, что и @see df_map_to_options(), но дополнительно локализует значения label'.
 * @param array(string|int => string) $map
 * @param object|string|null $module [optional]
 * @return array(array(string => string|int))
 */
function df_map_to_options_t(array $map) {
	return array_map('df_option', array_keys($map), df_translate_a($map));
}

/**
 * 2015-02-11
 * Эта функция равноценна вызову df_map_to_options(array_flip($map))
 * Превращает массив вида array('label' => 'value')
 * в массив вида array(array('value' => '', 'label' => ''))
 * @param array(string|int => string) $map
 * @return array(array(string => string|int))
 */
function df_map_to_options_reverse(array $map) {return array_map('df_option', $map, array_keys($map));}

/**
 * @param string|int $value
 * @param string $label
 * @return array(string => string|int)
 */
function df_option($value, $label) {return array('label' => $label, 'value' => $value);}

/**
 * @param array(string => string) $option
 * @param string|null $default [optional]
 * @return string|null
 */
function df_option_v(array $option, $default = null) {return df_a($option, 'value', $default);}

/**
 * Превращает массив вида array(array('value' => '', 'label' => ''))
 * в массив вида array('value').
 * @param array(string => string) $option
 * @param string|null $default [optional]
 * @return string|null
 */
function df_option_values(array $options) {return array_column($options, 'value');}

/**
 * Превращает массив вида array(array('value' => '', 'label' => ''))
 * в массив вида array('value' => 'label')
 * Обратная операция: @see df_map_to_options()
 * @param array(array(string => string|int)) $options
 * @return array(string|int => string)
 */
function df_options_to_map(array $options) {return array_column($options, 'label', 'value');}




