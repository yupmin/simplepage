<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function get_plural($singular) {
	$_plurals = config_item('plurals');

	if (is_array($_plurals) && array_key_exists($singular, $_plurals))
		return $_plurals[$singular];
	return plural($singular);
}
function get_singular($plural) {
	$_plurals = config_item('plurals');

	if (is_array($_plurals)) {
		$singulars = array_flip($_plurals);
		if (is_array($singulars) && array_key_exists($plural, $singulars))
			return $singulars[$plural];
	}
	return singular($plural);
}

function get_model_name($table_name) {
	return get_singular($table_name).'_model';
}
function get_table_name($model_name) {
	return get_plural(strtolower(str_replace('_model', '', $model_name)));
}
function get_singular_ucfirst_name($table_name) {
	return ucfirst(get_singular($table_name));
}
function get_foreign_key_name($table_name) {
	return get_singular($table_name).'_id';
}
function boolean($value) {
	return $value == 0 || is_null($value) || $value === FALSE ? 0 : 1;
}