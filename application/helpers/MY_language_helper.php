<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function l() {
	$_lang_key = NULL;
	$_arguments = NULL;
	switch (func_num_args()) {
		case 0:
			return FALSE;
		case 1:
			$_lang_key = func_get_arg(0);
			if (is_array($_lang_key)) {
				$_arguments = isset($_lang_key[1]) ? $_lang_key[1] : NULL;
				$_lang_key = $_lang_key[0];
			} else
				$_arguments = NULL;
			break;
		case 2:
			$_lang_key = func_get_arg(0);
			$_arguments = func_get_arg(1);
			if (empty($_arguments)) $_arguments = NULL;
			break;
		default:
			$_arguments = func_get_args();
			$_lang_key = $_arguments[0];
			array_shift($_arguments);
	}

	$CI =& get_instance();
	$_line = $CI->lang->line($_lang_key);
	$_line = stripcslashes($_line);
	if (!$_line && is_string($_lang_key)) {
		$_line = ucfirst($_lang_key);
	}

	if (is_null($_arguments)) return $_line;
	$_arguments = is_array($_arguments) ? $_arguments : array($_arguments);
	return call_user_func_array('sprintf', array_merge(array($_line), $_arguments));
}

function le() {
	$_lang_key = NULL;
	switch (func_num_args()) {
		case 0:
			return FALSE;
		case 1:
			$_lang_key = func_get_arg(0);
			if (is_array($_lang_key)) {
				$_arguments = isset($_lang_key[1]) ? $_lang_key[1] : NULL;
				$_lang_key = $_lang_key[0];
			} else
				$_arguments = NULL;
			break;
		case 2:
			$_lang_key = func_get_arg(0);
			$_arguments = func_get_arg(1);
			if (empty($_arguments)) $_arguments = NULL;
			break;
		default:
			$_arguments = func_get_args();
			$_lang_key = $_arguments[0];
			array_shift($_arguments);
	}

	$CI =& get_instance();
	$_line = $CI->lang->line($_lang_key);
	$_line = stripcslashes($_line);
	if (!$_line && is_string($_lang_key)) {
		$_lang_key = strtoupper($_lang_key);
		$_lang_key = str_replace(' ', '_', $_lang_key);
		$_lang_key = str_replace('.', '', $_lang_key);
		$_line = $CI->lang->line($_lang_key);
	}

	if (!$_line && is_string($_lang_key)) {
		$_line = ucfirst($_lang_key);
	}

	if (is_null($_arguments)) return $_line;
	$_arguments = is_array($_arguments) ? $_arguments : array($_arguments);
	return call_user_func_array('sprintf', array_merge(array($_line), $_arguments));
}
/* EOF */
