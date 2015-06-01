<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function h($string) {
	return htmlspecialchars($string);
}
function raise_flash($message, $kind = NULL) {
	if (is_null($kind)) $kind = 'info'; // success info warning danger
	$CI =& get_instance();
	$CI->session->set_flashdata($kind, $message);
	return TRUE;
}
function raise_message($message, $kind = NULL) {
	if (is_null($kind)) $kind = 'info';
	$CI =& get_instance();
	return $CI->_set_alert_message($kind, $message);
}
function get_field_id($field) {
	if (is_array($field) && isset($field['name']))
		$field = $field['name'];
	$_parts = explode('[', rtrim($field, ']'));

	$_field_ids = array();
	foreach($_parts as $_part) {
		if (strlen(trim($_part)) > 0)
			$_field_ids[] = $_part;
	}
	return count($_field_ids) ? implode('_', $_field_ids) : $field;
}
function field_attributes($attributes) {
	if (is_string($attributes) || is_null($attributes)) return $attributes;
	$_s = '';
	foreach($attributes as $k => $v) {
		if (is_array($v)) {
			$_s .= ' '.$k.'="'.implode(' ', $v).'"';
		} else if (is_null($v)) {
			$_s .= ' '.$k;
		} else {
			$_s .= ' '.$k.'="'.$v.'"';
		}
	}
	return $_s;
}
function is_error_field($field) {
	if (FALSE === ($OBJ =& _get_validation_object())) return FALSE;
	if (is_array($field) && isset($field['name']))
		$field = $field['name'];
	return $OBJ->is_error_field($field);
}
function error_exists() {
	if (FALSE === ($OBJ =& _get_validation_object())) return FALSE;
	return count($OBJ->get_error_array()) > 0;
}
function get_error_array() {
	if (FALSE === ($OBJ =& _get_validation_object())) return FALSE;
	return $OBJ->get_error_array();
}
function get_account_userid() {
	$CI =& get_instance();
	$_userid = $CI->session->userdata('account_userid');
	return empty($_userid) ? FALSE : $_userid;
}
function get_account_user_level(){
	$CI =& get_instance();
	$_user_level = $CI->session->userdata('account_user_level');
	return empty($_user_level) ? FALSE : $_user_level;
}
function reload_language($language_key) {
	if (!file_exists(APPPATH . 'language/' . $language_key)) return FALSE;

	// set language pack
	$_config = & get_config();
	$_config['language'] = $language_key;

	// reset
	$CI =& get_instance();
	$CI->lang->is_loaded = array();
	$CI->lang->language = array();

	require(APPPATH.'config/autoload.php');
	$CI->lang->load($autoload['language']);

	return TRUE;
}

function get_config_value($key, $reference = NULL, $reference_id = NULL, $default = NULL) {
	$CI =& get_instance();
	$CI->load->model('config_value_model');

	$_config_value_key = !empty($reference) && !empty($reference_id) ? $reference.'_'.$reference_id : 'default';
	if (!isset($CI->_config_values[$_config_value_key])) {
		$CI->_config_values[$_config_value_key] = (object) array();

		$config_values = $CI->config_value_model->find_all(array('reference' => $reference, 'reference_id' => $reference_id));
		foreach ($config_values as $config_value) {
			$CI->_config_values[$_config_value_key]->{$config_value->key} = $config_value->value;
		}
	}

	if (empty($CI->_config_values[$_config_value_key]->{$key})) {
		$_key = get_singular($reference).'_'.$key;
		return $CI->config->item_value($key, $default);
	}
	return $CI->_config_values[$_config_value_key]->{$key};
}
function _array_to_string($values) {
	if (count($values) == 0) return 'array()';

	$_values = array();
	foreach ($values as $key => $value) {
		if (is_array($value)) {
			$_values[] = '\''.$key.'\' => '._array_to_string($value);
		} else 
			$_values[] = '\''.$key.'\' => \''.$value.'\'';
	}
	return 'array('.implode($_values, ', ').')';
}
function get_array_config($values) {
	if (!is_array($values) || count($values) == 0) return 'array()';

	$_str = '';
	foreach ($values as $key => $value) {
		$_value = '';
		if (is_bool($value)) {
			$_value = !!($value) ? 'TRUE':'FALSE';
		} else if (is_string($value)) {
			$_value = '\''.$value.'\'';
		} else if (is_null($value)) {
			$_value = 'NULL';
		} else if (is_array($value)) {
			$_value = _array_to_string($value);
		}

		$_str .= '$config[\''.$key.'\'] = '.$_value.';'."\n";
	}
	return $_str;
}

function get_local_info_by_geoip($ip_address) {
	if ($ip_address == '127.0.0.1') return array('country_code' => NULL, 'timezone' => NULL);
	
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	$_geoip_record = geoip_record_by_name($ip_address);

	$_country_code = NULL;
	$_timezone = NULL;
	if (isset($_geoip_record['country_code'])) {
		$_country_code = strtolower($_geoip_record['country_code']);
		if (isset($_geoip_record['region'])) {
			$_timezone = geoip_time_zone_by_country_and_region($_geoip_record['country_code'], $_geoip_record['region']);
		}
	}
	error_reporting(E_ALL);
	if (defined('ENVIRONMENT')) {
		switch (ENVIRONMENT) {
			case 'development':
				error_reporting(E_ALL);
			break;
		
			case 'testing':
			case 'production':
				error_reporting(0);
			break;

			default:
				exit('The application environment is not set correctly.');
		}
	}
	$_local_info = array('country_code' => $_country_code, 'timezone' => $_timezone);

	return $_local_info;
}

function write_log($value, $additional = NULL, $use_json_encode = FALSE, $file_prefix = NULL) {
	$config =& get_config();
	if (!$config['use_write_log']) return TRUE;

	if (is_null($file_prefix)) $file_prefix = 'log-';
	$_log_path = ($config['log_path'] != '') ? $config['log_path'] : APPPATH.'logs/';
	$filepath = $_log_path.$file_prefix.date('Y-m-d').".php";
	$message  = '';

	if ( ! $fp = @fopen($filepath, FOPEN_WRITE_CREATE)) return FALSE;
	if ($use_json_encode) {
		ob_start();echo @json_encode($value);$ob = ob_get_contents();ob_end_clean();
	} else {
		$ob = $value;
	}
	$message .= gmdate($config['log_date_format']). (!is_null($additional) ? ' '.$additional. ': ' : '') . ' → '.$ob."\n";

	flock($fp, LOCK_EX);
	fwrite($fp, $message);
	flock($fp, LOCK_UN);
	fclose($fp);

	@chmod($filepath, FILE_WRITE_MODE);
	return TRUE;
}
/* EOF */