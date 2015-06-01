<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Controller extends CI_Controller {

	// default

	// render
	protected $_data;
	protected $_partial;
	protected $_output_format;
	protected $_available_actions = array();
	protected $_return_gets;
	protected $_return_get_values;
	protected $_default_return_url;
	protected $_alert_messages;

	// this
	protected $_this_site_name;
	protected $_this_site_key;
	protected $_this_country_code;
	protected $_this_timezone;
	protected $_this_language_key;
	protected $_this_currency_code;
	protected $_this_domain;
	protected $_this_service_domain;
	protected $_this_container;
	protected $_this_base_uri;
	protected $_this_uri;

	public function __construct() {
		parent::__construct();

		$self = (object) array('head_title' => ''
			, 'javascript' => array()
			, 'header_javascript' => array()
			, 'footer_javascript' => array()
			, 'stylesheet' => array()
			, 'uri' => ''
			, 'this_url' => ''
			, 'return_url' => ''
			, 'form_post_values' => array());

		$this->_data = array('self' => $self);
		$this->_partial = array();

		$this->load->library('migration_manager');

		if (!$this->input->is_cli_request()) {
			if (!$this->migration_manager->check_config_by()) {
				// do setup or update
			} else {
				// do something
			}
		}

		// check_localization
		$this->_check_localization();

		if (!$this->input->is_cli_request()) {
			// load session
			$this->load->library('session');

			// check_redirect_https
			if ($this->_check_redirect_https()) {
				$this->_redirect_https();
			}

			// load library
			$this->load->library('account_manager');

			$this->_check_permission();

			// CSRF
			$_csrf_protection = $this->config->item('csrf_protection') === TRUE;
			if ($_csrf_protection) {
				$this->_data['self']->csrf_token = $this->security->get_csrf_hash();
			}

			// set_initialize_base_variables
			$this->_set_initialize_base_variables();

			// set initialize inner variables
			$this->_set_initialize_inner_variables();

			// load language pack
			if (file_exists(APPPATH.'language/'.$this->_this_language_key.'/'.$this->_this_site_key.'_lang.php'))
				$this->lang->load($this->_this_site_key, $this->_this_language_key);
		}
	}

	// common
	public function _remap($method, $params = array()) {
		list($method, $params, $this->_output_format) = $this->uri->rearrange_remap_by($method, $params);
		if (!method_exists($this, $method)) {
			array_push($params, $method);
			$method = 'view';
		}
		if (method_exists($this, $method)) {
			return call_user_func_array(array($this, $method), $params);
		}
		show_404("{$method}");
	}

	// redirect
	public function _redirect($return_url = NULL, $force = NULL) {
		if (is_null($force)) $force = FALSE;

		// order
		// 1. GET['return_url'], 2. $return_url, 3. default_return_url

		$_return_url = $this->_return_url;
		if ($force || (empty($_return_url) && !empty($return_url))) $_return_url = $return_url;
		if (empty($_return_url) && !empty($this->_default_return_url)) $_return_url = $this->_default_return_url;

		#$this->session->sess_save();
		redirect($_return_url, 'location', 302);
	}
	public function _redirect_https() {
		$_use_https = $this->config->item('use_https', FALSE);
		if (isset($_use_https) && $_use_https) {

			if ($_use_https == 'https_only') {
				if (!is_https()) {
					//$this->session->sess_save();
					redirect('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 'refresh');
				} else {
					redirect('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 'refresh');
				}
			} else {
				$_https_container = $this->config->item('https_container', '');
				$_https_containers = strlen($_https_container) == 0 ? array() : explode('|', $_https_container);

				$_first_uri_segment = $this->uri->segment(1);
				if (in_array($_first_uri_segment, $_https_containers) && !is_https()) {
					redirect('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 'refresh');
				} else if (!in_array($_first_uri_segment, $_https_containers) && is_https()) {
					redirect('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 'refresh');
				}
			}
		}
	}

	// redirect https
	protected function _check_redirect_https() {
		if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] != 'GET') return FALSE;

		$_use_https = $this->config->item('use_https');
		if (!in_array($_use_https, array('https_only', 'https_container'))) return FALSE;
		$_is_https_on = is_https();

		if ($_use_https == 'https_only') {
			if (!$_is_https_on) return TRUE;
		} else if ($_use_https == 'https_container') {
				$_https_container = $this->config->item('https_container', '');
			$_https_containers = strlen($_https_container) == 0 ? array() : explode('|', $_https_container);

			$_first_uri_segment = $this->uri->segment(1);
			if (empty($_first_uri_segment)) return $_is_https_on;

			return !((in_array($_first_uri_segment, $_https_containers) && $_is_https_on) || (!in_array($_first_uri_segment, $_https_containers) && !$_is_https_on));
		}
		return FALSE;
	}

	// localization
	protected function _check_localization() {
		$_expire = 3600 * 24; // 1 cay

		// cookie local info
		$_this_timezone = $this->input->cookie('timezone');
		$_this_language_key = $this->input->cookie('language_key');
		$_this_country_code = $this->input->cookie('country_code');
		$_this_currency_code = $this->input->cookie('currency_code');
		$_this_ip_address = $this->input->is_cli_request() ? NULL : $this->input->ip_address();

		if (empty($_this_timezone) || empty($_this_country_code)) {
			if (function_exists('geoip_record_by_name') && function_exists('geoip_time_zone_by_country_and_region')) {
				$_local_info = get_local_info_by_geoip($_this_ip_address);
			}

			if (empty($_this_timezone) && isset($_local_info['timezone']) && !empty($_local_info['timezone'])) {
				$_this_timezone = $_local_info['timezone'];

				$this->input->set_cookie(array('name'=>'timezone', 'value'=> $_this_timezone, 'expire' => $_expire));
			}
			if (empty($_this_country_code) && isset($_local_info['country_code']) && !empty($_local_info['country_code'])) {
				$_this_country_code = $_local_info['country_code'];
				$this->input->set_cookie(array('name'=>'country_code', 'value'=> $_this_country_code, 'expire' => $_expire));;
			}
		}

		if (empty($_this_country_code)) {
			$_this_country_code = $this->config->item('default_country_code');
		}
		$this->_this_country_code = $_this_country_code;
		if (empty($_this_timezone)) {
			$_this_timezone = $this->config->item('default_timezone');
			$_this_timezone = empty($_this_timezone) ? 'UTC' : $_this_timezone;
		}
		$this->_this_timezone = $_this_timezone;
		date_default_timezone_set($_this_timezone);

		if (empty($_this_language_key) && !empty($_this_country_code)) {
			$_this_language_key = $this->config->item('default_language_key');
		}
		$this->_this_language_key = $_this_language_key;

		// set language pack
		reload_language($this->_this_language_key);

		$this->_this_currency_code = $_this_currency_code;
	}

	// base_variables
	private function _set_initialize_base_variables() {
		// referer_url
		$this->_referer_url = get_referer_url();
		$this->_data['self']->referer_url = $this->_referer_url;

		// this_url
		$_get_values = $this->input->get();
		if (in_array('return_url', $_get_values)) $_get_values = array_diff_key($_get_values, array('return_url'));
		$this->_this_url = site_url($this->uri->uri_string()).($_get_values ? '?'.http_build_query($_get_values):'');
		$this->_data['self']->this_url = $this->_this_url;

		// return_url
		$this->_return_url = $this->input->get_post('return_url') ? $this->input->get_post('return_url') : NULL;#$this->_referer_url;
		$this->_data['self']->return_url = $this->_return_url;
		$_data['form_post_values'] = array();
		if (!empty($this->_return_url)) $this->_data['self']->form_post_values['return_url'] = $this->_return_url;

		// return_get_values
		$this->_return_get_values = array();
		if (isset($this->_return_gets) && !empty($this->_return_gets)) {
			$_parts = parse_url($this->_this_url);
			$_get_values = array();
			if (isset($_parts['path']))
				$this->_return_uri = trim($_parts['path'], '/');
			if (isset($_parts['query']))
				parse_str($_parts['query'], $_get_values);
			foreach($this->_return_gets as $_return_get) {
				if (count($_return_get) > 0 && count($_get_values) > 0 && array_key_exists($_return_get, $_get_values))
					$this->_return_get_values[$_return_get] = $_get_values[$_return_get];
			}
		}
		$this->_data['self']->return_get_values = $this->_return_get_values;

		// this_base_uri
		$_fetch_directory = $this->router->fetch_directory();
		$this->_this_base_uri = (empty($_fetch_directory) ? '':trim($_fetch_directory, '/').'/').$this->router->fetch_class();
		$this->_data['self']->this_base_uri = $this->_this_base_uri;

		// this_uri
		$_method = $this->router->fetch_method();
		if (($pos = strrpos($_method, '.')) !== FALSE) {
			$_suffix = substr($_method, $pos + 1, strlen($_method) - $pos);
			$_method = substr($_method, 0, $pos);
		}
		$this->_this_uri = trim($this->_this_base_uri, '/').($_method == 'index' ? '':'/'.$_method);
		$this->_data['self']->this_uri = $this->_this_uri;

		// this_container
		$this->_this_container = trim($_fetch_directory, '/');
		$this->_data['self']->this_container = $this->_this_container;

		if (!$this->input->is_cli_request()) {
			// domain
			$this->_this_domain = $this->input->server('HTTP_HOST');
			if (($pos = strpos($this->_this_domain, ':'))) {
				$this->_this_domain = substr($this->_this_domain, 0, $pos);
			}
			$_service_domain = $this->config->item('service_domain');
			$this->_this_service_domain = empty($_service_domain) ? $this->_this_domain : $_service_domain;
		}
	}

	// check_permission
	protected function _check_permission() {
		$_container = trim($this->router->fetch_directory(), '/');
		$_controller = $this->router->fetch_class();
		$_action = $this->router->fetch_method();

		$_container = empty($_container) ? NULL : $_container;
		$_controller = empty($_controller) ? NULL : $_controller;
		$_action = empty($_action) ? NULL : $_action;

		if (($pos = strpos($_action, '?')) !== FALSE)
			$_action = substr($_action, 0, $pos - 1);
		if (($pos = strpos($_action, '.')) !== FALSE)
			$_action = substr($_action, 0, $pos - 1);
		if (is_numeric($_action))
			$_action = 'view';

		$this->load->config('user_permission');
		$_user_permissions = $this->config->item('user_permissions');

		$permission = NULL;
		$_key = (is_null($_container) ? '*':$_container).'/'.(is_null($_controller) ? '*':$_controller).'/'.(is_null($_action) ? '*':$_action);

		if (isset($_user_permissions[$_key])) {
			$permission = $_user_permissions[$_key];
		} else {
			$_key = (is_null($_container) ? '*':$_container).'/'.(is_null($_controller) ? '*':$_controller).'/*';
			if (isset($_user_permissions[$_key])) {
				$permission = $_user_permissions[$_key];
			} else {
				$_key = (is_null($_container) ? '*':$_container).'/*/*';
				if (isset($_user_permissions[$_key])) {
					$permission = $_user_permissions[$_key];
				}
			}
		}

		if (is_null($permission)) show_404();
		$_anonymous_access = array_shift($permission);
		if ($_anonymous_access == boolean(TRUE)) return;

		$_account_id = get_account_id();
		if (!($_account_id = get_account_id())) {
			raise_flash(l('need to be signed in.'), 'error');
			$this->_redirect(url_for('account/signin',(empty($this->_this_url) ? NULL:array('return_url' => $this->_this_url))), TRUE);
		}

		if (!in_array(get_account_user_level(), $permission)) {
			raise_flash(l('access denied.'), 'error');
			$this->_redirect();
		}

		return TRUE;
	}

	// render
	public function _set_head_title($title) {
		$this->_data['self']->head_title = $title;
		return TRUE;
	}
	public function _set_top_logo_title($title) {
		$this->_data['self']->top_logo_title = $title;
		return TRUE;
	}
	public function _set_page_header($page_header) {
		$this->_data['self']->page_header = $page_header;
		return TRUE;
	}
	public function _set_page_nav($page_nav) {
		$this->_data['self']->page_nav = $page_nav;
		return TRUE;
	}
	public function _add_javascript($javascript) {
		if (!array_key_exists($javascript, $this->_data['self']->javascript))
			array_push($this->_data['self']->javascript, $javascript);
		return TRUE;
	}
	public function _add_header_javascript($javascript) {
		if (!array_key_exists($javascript, $this->_data['self']->header_javascript))
			array_push($this->_data['self']->header_javascript, $javascript);
		return TRUE;
	}
	public function _add_footer_javascript($javascript) {
		if (!array_key_exists($javascript, $this->_data['self']->footer_javascript))
			array_push($this->_data['self']->footer_javascript, $javascript);
		return TRUE;
	}
	public function _add_stylesheet($stylesheet) {
		if (!array_key_exists($stylesheet, $this->_data['self']->stylesheet))
			array_push($this->_data['self']->stylesheet, $stylesheet);
		return TRUE;
	}
	protected function _get_container_path() {
		$_parts = explode('/',$this->uri->uri_string());
		$_path = '';
		foreach($_parts as $_part) {
			if (file_exists(APPPATH.'controllers'.$_path.'/'.$_part)
				&& is_dir(APPPATH.'controllers'.$_path.'/'.$_part)) {
				$_path = $_path.'/'.$_part;
			} else if (file_exists(APPPATH.'controllers'.$_path.'/'.$_part.'php')) {
				break;
			}
		}
		return trim($_path,'/');;
	}
	protected function _get_partial_view($output_format, $view, $data = NULL, $path = NULL) {
		$this->load->library('Browser_manager');
		$_view_format = $this->browser_manager->get_view_format($output_format);

		$data = is_null($data) ? $this->_data : array_merge($this->_data, $data);
		$path = rtrim((empty($path) ? $this->_get_container_path() : $path),'/').'/';

		$_view_path = $path.$view.'-'.$_view_format;
		if (!file_exists(APPPATH.'views'.$_view_path.'.php')) {
			$_view_format_orders = array_diff(array('xhtml', 'html5'), array($_view_format));
			foreach($_view_format_orders as $_view_format_order) {
				$_view_path = $path.$view.'-'.$_view_format_order;
				if (file_exists(APPPATH.'views'.$_view_path.'.php')) {
					$_view_format = $_view_format_order;
					break;
				} else {
					$_view_path = $path.$view.'-'.$_view_format;
				}
			}
		}

		$_output = $this->load->view($_view_path, $data, TRUE);

		if(count($this->_partial) > 0) {
			$_compiled_partial = array();
			// replacement pattern [[##_substitution_##]]
			foreach($this->_partial as $_key => $_partial) {
				$_partial_path = is_null($_partial['path']) ? $path : $_partial['path'].'/';

				$_partial_view_path = is_null($_partial['view']) ? $_partial_path.'_'.$_key.'-'.$_view_format : $_partial_path.'_'.$_partial['view'].'-'.$_view_format;
				if (!file_exists(APPPATH.'views/'.$_partial_view_path.'.php'))
					$_partial_view_path = is_null($_partial['view']) ? $_partial_path.'_'.$_key.'-'.$_view_format : $_partial_path.'_'.$_partial['view'].'-'.$_view_format;

				$_compiled_partial['[[##_'.$_key.'_##]]'] = $this->load->view($_partial_view_path, $_partial['data'], TRUE);

				if ((strpos($_compiled_partial['[[##_'.$_key.'_##]]'], '[[##_') !== FALSE)
					&& preg_match_all('/(\[\[##_[0-9a-zA-Z_]+_##\]\])/i', $_compiled_partial['[[##_'.$_key.'_##]]'], $_matches)
					&& isset($_matches[1])) {
					foreach($_matches[1] as $_match) {
						$_compiled_partial['[[##_'.$_key.'_##]]'] = str_replace($_match, (isset($_compiled_partial[$_match])
							? $_compiled_partial[$_match] : ''), $_compiled_partial['[[##_'.$_key.'_##]]']);
					}
				}
			}
			$_output = str_replace(array_keys($_compiled_partial), array_values($_compiled_partial), $_output);
		}
		$_output = preg_replace('/(\[\[##_[0-9a-zA-Z_]+_##\]\])/i', '', $_output);

		return $_output;
	}
	protected function _render($view = NULL, $data = NULL, $path = NULL, $output_format = NULL, $set_output = NULL) {
		if ($view === FALSE) show_404();
		if (is_null($view)) $view = 'layout';
		if (is_null($output_format)) $output_format = $this->_output_format;

		$_output = $this->_get_partial_view($output_format, $view, $data, $path);
		if (is_null($set_output)) {
			$this->output->set_output($_output);
			return TRUE;
		}
		return $_output;

	}
	protected function _render_partial($view = NULL, $data = NULL, $path = NULL, $output_format = NULL) {
		if ($view === FALSE || empty($view)) return '';
		if (is_null($output_format)) $output_format = $this->_output_format;

		$_output = $this->_get_partial_view($output_format, '_'.$view, $data, $path);
		return $_output;
	}
	protected function _render_response($error = NULL, $message = NULL, $result = NULL, $output_format = NULL, $cached_at = NULL) {
		if (is_null($output_format)) $output_format = 'json';

		$_response = array('error' => boolean($error));
		if (!is_null($message) || $result === FALSE) $_response['message'] = $message;
		if (!is_null($result) || $result === FALSE) $_response['result'] = $result;
		if ($cached_at) $_response['cached_at'] = $cached_at;

		switch($output_format) {
			case 'json':
				$_content = json_encode($_response);
				$_content_length = strlen($_content);
				$this->load->library('user_agent');
				if($this->agent->is_browser('Internet Explorer')) {
					$this->output->set_content_type('text/plain;charset=utf-8');
				} else {
					$this->output->set_content_type('application/json;charset=utf-8');
				}
				$this->output->set_header('Content-Length:'.$_content_length);
				$this->output->set_output($_content);
				break;
			default:
				return;
				$this->_render(FALSE);
		}
	}
	protected function _partial($keyword, $view = NULL, $data = NULL, $path = NULL) {
		$data = is_null($data) ? $this->_data : array_merge($this->_data, $data);
		$this->_partial[$keyword] = array('view' => $view, 'data' => $data, 'path' => $path);
	}
	protected function _render_error_response($method = NULL, $message = NULL, $redirect_url = NULL, $level = NULL) {
		if (is_null($method)) $method = 'page';
		if (is_null($level)) $level = 'error';

		if ($this->input->is_ajax_request() && in_array($this->_output_format, array('json', 'xml'))) {
			$this->_render_response(TRUE, $message, NULL, $this->_output_format);
			return;
		}
		if ($method == 'page')
			show_error($message, 404, l('an error was encountered.'));

		raise_flash($message, $level);
		if (empty($redirect_url)) $this->_redirect();
		$this->_redirect($redirect_url, TRUE);
	}

	// this
	public function _get_timezone() {
		return $this->_this_timezone;
	}
	public function _set_timezone($key) {
		$this->_timezone = $key;
		date_default_timezone_set($this->_timezone);
		return TRUE;
	}
	public function _get_language_key() {
		return $this->_this_language_key;
	}
	public function _set_language_key($key) {
		$this->_language_key = $key;
		return $this->_set_language_from_constructor();
	}
	public function _get_this_domain() {
		return $this->_this_domain;
	}
	public function _get_output_format() {
		return $this->_output_format;
	}
	public function _get_csrf_token() {
		return isset($this->_data['self']->csrf_token) ? $this->_data['self']->csrf_token : NULL;
	}
	public function _set_alert_message($kind, $message) {
		$this->_alert_messages[$kind] = $message;
		return TRUE;
	}
	public function _get_alert_messages() {
		return empty($this->_alert_messages) ? array():$this->_alert_messages;
	}

	public function _set_initialize_inner_variables() {
		$this->_project_key = $this->config->item('project_key');
		$this->_project_name = $this->config->item('project_name');

		$this->_allow_signin = $this->config->item('allow_signin');

		$this->_this_account_userid = $this->account_manager->get_account_userid();
		$this->_this_account_name = $this->account_manager->get_account_name();
		$this->_this_account_user_level = $this->account_manager->get_account_user_level();

		$this->_this_site_name = $this->config->item('site_name');
		$this->_this_site_key = $this->config->item('site_key');

		$this->_data['self']->site_key = $this->_this_site_key;
		$this->_data['self']->site_name = $this->_this_site_name;
		$this->_data['self']->allow_signin = $this->_allow_signin;

		$this->_data['self']->this_account_userid = $this->_this_account_userid;
		$this->_data['self']->this_account_name = $this->_this_account_name;
		$this->_data['self']->this_account_user_level = $this->_this_account_user_level;
	}
}
/* EOF */