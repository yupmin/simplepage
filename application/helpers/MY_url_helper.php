<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function get_uri_array($str) {
	$_result = array('path' => array(), 'query_string' => array(), 'suffix' => 'html');
	$_query_string = NULL;

	if (preg_match('!^\w+://! i', $str)) {
		$_parts = parse_url($str);
		$_path = isset($_parts['path']) ? $_parts['path'] : NULL;
		$_query_string = isset($_parts['query']) ? $_parts['query'] : NULL;
	} else {
		$_p = strpos($str, '?');

		# todo fragment process
		if ($_p !== FALSE) {
			$_path = substr($str, 0, $_p);
			$_query_string = substr($str, $_p + 1, (strlen($str) - $_p - 1));
		} else {
			$_path = $str;
		}
	}

	$_path = trim($_path, '/');

	if (!empty($_path)) {
		$_p = strrpos($_path, '.');
		if ($_p !== FALSE) {
			$_result['suffix'] = substr($_path, $_p + 1, (strlen($_path) - $_p - 1));
			$_path = substr($_path, 0, $_p);
		}
		$_result['path'] = explode('/', $_path);
	} else {
		$_result['path'] = NULL;
	}

	if (!empty($_query_string)) {
		parse_str($_query_string, $_result['query_string']);
	}

	return $_result;
}
function site_uri($uri = NULL, $suffix = NULL) {
	return get_instance()->config->site_uri($uri, $suffix);
}
function get_https_url($uri) {
	$CI = & get_instance();
	$_use_https = $CI->config->item('use_https');
	if (is_array($uri)) $uri = implode('/', $uri);
	if (is_string($uri)) $uri = trim($uri, '/');
	$_url = '/'.$uri;

	if ($_use_https == 'https_only') {
		if (!is_https()) {
			$_protocol = 'https';
			$_url = $_protocol .'://'.$_SERVER['HTTP_HOST'].$_url;
		}
	} else if ($_use_https == 'https_container') {
		$_https_container = $CI->config->item('https_container', '');
		$_https_containers = strlen($_https_container) == 0 ? array() : explode('|', $_https_container);
		$_first_uri_segment = NULL;
		if (is_string($uri)) {
			if ($_pos = strpos($uri, '/')) {
				$_first_uri_segment = substr($uri, 0, $_pos);
			} else {
				$_first_uri_segment = $uri;
			}
		} else if (is_array($uri) && count($uri) > 0) {
			$_first_uri_segment = $uri[0];
		}
		if (empty($_first_uri_segment)) {
			$_protocol = 'http';
			$_url = $_protocol .'://'.$_SERVER['HTTP_HOST'].$_url;
		} else {
			if (!in_array($_first_uri_segment, $_https_containers) && in_array(get_instance()->uri->segment(1), $_https_containers)) {
				$_protocol = 'http';
				$_url = $_protocol .'://'.$_SERVER['HTTP_HOST'].$_url;
			} else if (in_array($_first_uri_segment, $_https_containers) && !in_array(get_instance()->uri->segment(1), $_https_containers)) {
				$_protocol = 'https';
				$_url = $_protocol .'://'.$_SERVER['HTTP_HOST'].$_url;
			}
		}
	}
	return $_url;
}
function url_for($uri = NULL, $query_data = NULL, $suffix = NULL) {
	$query_string = '';
	if (!empty($query_data)) {
		$query_string = '?'.(is_array($query_data) ? http_build_query($query_data) : $query_data);
	}
	$_url = get_https_url($uri);
	if (preg_match('!^\w+://! i', $_url)) {
		return $_url.(empty($suffix) ? '' : '.'.$suffix).$query_string;
	}
	return '/'.site_uri($uri, $suffix).$query_string;
}
function get_referer_url() {
	$referer_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;

	if (!is_null($referer_url)
		&& (preg_match('#^https?://#i', $referer_url)
		&& strpos($referer_url, site_url()) >= 0)) {
		$_uri_array = get_uri_array($referer_url);

		if (isset($_uri_array['query_string']['return_url'])) {
			unset($_uri_array['query_string']['return_url']);
		}

		$referer_url = url_for($_uri_array['path'], $_uri_array['query_string'], get_instance()->_get_output_format());
	}

	return $referer_url;
}