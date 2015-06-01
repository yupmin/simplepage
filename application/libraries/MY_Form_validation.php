<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Form_validation extends CI_Form_validation {
	public function get_error_array() {
		return $this->_error_array;
	}

	public function is_error_field($field) {
		return (isset($this->_field_data[$field]['error']) && strlen($this->_field_data[$field]['error']) > 0);
	}

	public function set_error_array($field, $message) {
		$this->_field_data[$field]['error'] = $message;
		return TRUE;
	}

	public function lower_alpha_minus($str) {
		return ( ! preg_match("/^([a-z0-9-])+$/", $str)) ? FALSE : TRUE;
	}
	public function lower_alpha_dash($str) {
		return ( ! preg_match("/^([a-z0-9_-])+$/", $str)) ? FALSE : TRUE;
	}
	public function alpha_minus($str) {
		return ( ! preg_match("/^([a-z0-9-])+$/i", $str)) ? FALSE : TRUE;
	}
	public function is_numeric_if_not_empty($str) {
		if (empty($str)) return TRUE;
		return !is_numeric($str) ? FALSE : TRUE;
	}
	public function alpha_dash_if_not_empty($str) {
		if (empty($str)) return TRUE;
		return ( ! preg_match("/^([a-z0-9_-])+$/i", $str)) ? FALSE : TRUE;
	}
	public function numeric_minus($str) {
		return ( ! preg_match("/^([0-9-])+$/", $str)) ? FALSE : TRUE;
	}

	// file
	public function required_file($file) {
		$_file = @json_decode($file);
		return isset($_file->tmp_name) && !empty($_file->tmp_name);
	}
	public function extension_if_not_empty($file, $value) {
		$_extensions = strpos($value, ',') !== FALSE ? explode(',', $value) : array($value);
		$_file = @json_decode($file);
		if (!isset($_file->name)) return TRUE;

		$_extension = NULL;
		if (($_pos = strripos($_file->name, '.')) !== FALSE) {
			$_extension = substr($_file->name, $_pos + 1, strlen($_file->name) - $_pos - 1);
			$_extension = strtolower($_extension);
		}
		return in_array($_extension, $_extensions);		
	}
	public function mime_if_not_empty($file, $value) {
		$_mimes = strpos($value, ',') !== FALSE ? explode(',', $value) : array($value);
		$_file = @json_decode($file);
		if (!isset($_file->type)) return TRUE;

		$_mime = $_file->type;
		return in_array($_mime, $_mimes);		
	}

	public function run($group = '')
	{
		// Do we even have any data to process?  Mm?
		$validation_array = empty($this->validation_data) ? $_POST : $this->validation_data;
		// customize
		$_files = $this->CI->input->file();
		if (count($validation_array) === 0 && count($_files) === 0)
		{
			return FALSE;
		}

		// Does the _field_data array containing the validation rules exist?
		// If not, we look to see if they were assigned via a config file
		if (count($this->_field_data) === 0)
		{
			// No validation rules?  We're done...
			if (count($this->_config_rules) === 0)
			{
				return FALSE;
			}

			if (empty($group))
			{
				// Is there a validation rule for the particular URI being accessed?
				$group = trim($this->CI->uri->ruri_string(), '/');
				isset($this->_config_rules[$group]) OR $group = $this->CI->router->class.'/'.$this->CI->router->method;
			}

			$this->set_rules(isset($this->_config_rules[$group]) ? $this->_config_rules[$group] : $this->_config_rules);

			// Were we able to set the rules correctly?
			if (count($this->_field_data) === 0)
			{
				log_message('debug', 'Unable to find validation rules');
				return FALSE;
			}
		}

		// Load the language file containing error messages
		$this->CI->lang->load('form_validation');

		// Cycle through the rules for each field and match the corresponding $validation_data item
		foreach ($this->_field_data as $field => $row)
		{
			// Fetch the data from the validation_data array item and cache it in the _field_data array.
			// Depending on whether the field name is an array or a string will determine where we get it from.
			if ($row['is_array'] === TRUE)
			{
				// customize
				$_file_data = $this->_reduce_array($_files, $row['keys']);
				if (isset($_file_data['error']) || (isset($_file_data['tmp_name']))) {
					$this->_field_data[$field]['postdata'] = json_encode($_file_data);
				} else {
					$this->_field_data[$field]['postdata'] = $this->_reduce_array($validation_array, $row['keys']);
				}
			}
			elseif (isset($validation_array[$field]) && $validation_array[$field] !== '')
			{
				$this->_field_data[$field]['postdata'] = $validation_array[$field];
			}
			// customize
			elseif (isset($_files[$field])) {
				$this->_field_data[$field]['postdata'] = json_encode($_files[$field]);
			}
		}

		// Execute validation rules
		// Note: A second foreach (for now) is required in order to avoid false-positives
		//	 for rules like 'matches', which correlate to other validation fields.
		foreach ($this->_field_data as $field => $row)
		{
			// Don't try to validate if we have no rules set
			if (empty($row['rules']))
			{
				continue;
			}

			$this->_execute($row, $row['rules'], $this->_field_data[$field]['postdata']);
		}

		// Did we end up with any errors?
		$total_errors = count($this->_error_array);
		if ($total_errors > 0)
		{
			$this->_safe_form_data = TRUE;
		}

		// Now we need to re-set the POST data with the new, processed data
		$this->_reset_post_array();

		return ($total_errors === 0);
	}
}
/* EOF */