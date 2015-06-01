<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Migration_manager {
	protected $_error_code = NULL;
	protected $_error_message = NULL;
	public function is_error() {
		return !is_null($this->_error_code);
	}
	public function get_error_code() {
		return $this->_error_code;
	}
	public function get_error_message() {
		return $this->_error_message;
	}

	public function __construct() {
		$this->CI =& get_instance();
	}

	public function check_config_by($key = NULL) {
		if (is_null($key)) $key = 'extension';

		if (file_exists(APPPATH.'config/'.$key.'.php')) {
			$this->CI->load->config($key);

			return TRUE;
		}

		if (!in_array($this->CI->uri->uri_string(), array('migration/setup', 'migration/update'))) {
			$this->CI->_redirect(url_for('migration/setup'), TRUE);
			exit;
		}
		return FALSE;
	}
	public function save_config_by($configs, $key = NULL) {
		if (is_null($key)) $key = 'extension';

		if (count($configs) == 0) return TRUE;
		$_str = "<?php\ndefined('BASEPATH') OR exit('No direct script access allowed');\n\n";
		$_str .= get_array_config($configs);
		$_str .= "\n".'/* EOF */'."\n";

		$CI = & get_instance();
		$CI->load->helper('file');
		$_result = write_file(APPPATH.'config/'.$key.'.php', $_str);
		@chmod(APPPATH.'config/'.$key.'.php', FILE_WRITE_MODE);

		return $_result;
	}
	public function update_config_by($configs, $key = NULL) {
		if (is_null($key)) $key = 'extension';

		if (file_exists(APPPATH.'config/'.$key.'.php')) {
			include APPPATH.'config/'.$key.'.php';
			if (isset($config)) $configs = array_merge($config, $configs);
		}
		return $this->save_config_by($configs, $key);
	}
	public function remove_config_by($key = NULL) {
		if (is_null($key)) $key = 'extension';

		return @unlink(APPPATH.'config/'.$key.'.php');
	}	
}
/* EOF */