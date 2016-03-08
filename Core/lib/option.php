<?php
/**
 * 2015-12-28
 * Преобразует при необходимости простой одномерный массив в список опций.
 * @param string[] $values
 * @return array(array(string => string|int))
 */
function df_a_to_options(array $values) {
	/** @var mixed $first */
	$first = df_first($values);
	return is_null($first) || isset($first['value'])
		? $values
		: df_map_to_options(array_combine($values, $values))
	;
}

/**
 * 2015-02-11
 * Превращает массив вида array('value' => 'label')
 * в массив вида array(array('value' => '', 'label' => ''))
 * Обратная операция: @see df_options_to_map()
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
function df_option($value, $label) {return ['label' => $label, 'value' => $value];}

/**
 * @param array(string => string) $option
 * @param string|null|callable $default [optional]
 * @return string|null
 */
function df_option_v(array $option, $default = null) {return dfa($option, 'value', $default);}

/**
 * Превращает массив вида array(array('value' => '', 'label' => ''))
 * в массив вида array('value').
 * @param array(string => string) $option
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

/**
 * 2015-11-17
 * @return array(array(string => string|int))
 */
function df_yes_no() {
	/** @var \Magento\Config\Model\Config\Source\Yesno $o */
	$o = df_o(\Magento\Config\Model\Config\Source\Yesno::class);
	return $o->toOptionArray();
}




