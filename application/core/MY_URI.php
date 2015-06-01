<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_URI extends CI_URI {

	public function set_uri_string($str) {
		$this->_set_uri_string($str);
	}

	public function reset_uri_string() {
		$this->uri_string = '';
		$this->segments = array();
		$this->rsegments = array();
	}

	public function rearrange_remap_by($method, $params = NULL, $default_suffix = NULL) {
		if (is_null($params)) $params = array();
		if (empty($default_suffix)) $default_suffix = 'html';

		if (strpos($method, '?') > 0) {
			$_parts = explode('?', $method);
			if (count($_parts) > 1) {
				$method = $_parts[0];
				// function parse_querystring
				$_get_params = array();
				if (isset($_parts[1])) {
				    $_queries = explode('&', $_parts[1]);
					foreach ($_queries as $_query) {
						list($k, $v) = array_map('urldecode', explode('=', $_query));
						$_get_params[$k] = $v;
					}
				}
				if (isset($_get_params['key']) && strpos($_get_params['key'], '.') > 0) {
					$_parts = explode('.', $_get_params['key']);
					$_get_params['key'] = $_parts[0];
					$method = $method .'.'.$_parts[1];
				}
				$_GET = array_merge($_GET, $_get_params);
			}
		}

		$_last_param = array_pop($params);
		$_suffix = NULL;
		if (strpos($method, '.') > 0) {
			$_parts = explode('.', $method);
			if (count($_parts) > 1) {
				$_suffix = array_pop($_parts);
				$method = $_parts[0];
			}
		} else if (strpos($_last_param, '.') > 0) {
			$_parts = explode('.', $_last_param);
			if (count($_parts) > 1) {
				$_suffix = array_pop($_parts);
				$_last_param = $_parts[0];
			}
			array_push($params, $_last_param);
		} else if (!is_null($_last_param)) {
			array_push($params, $_last_param);
		}

		if (is_null($_suffix)) {
			$_uri = $this->uri_string();
			if (($_pos = strrpos($_uri, '.')) != FALSE) {

				$_suffix = substr($_uri, $_pos + 1, strlen($_uri) - $_pos - 1);
			} else {
				$_suffix = $default_suffix;
			}
		}

		if (is_numeric($method)) {
			array_unshift($params, $method);
			$method = 'view';
		}

		return array($method, $params, $_suffix);
	}
}