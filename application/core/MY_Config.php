<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Config extends CI_Config {
	public function item_value($index = '', $default = NULL) {
		$_value = $this->item($index);
		return !is_null($_value) ? $_value : $default;
	}
	public function site_uri($uri = '', $suffix = NULL) {
		if (empty($uri)) {
			$_index_page = (string) $this->item('index_page'); // for php5.3, 5.4
			$suffix = (!empty($suffix) && !empty($_index_page) ? '.'.$suffix : '');
			return $this->item('index_page').$suffix;
		}

		if (is_string($uri)) $uri = ltrim($uri, '/');
		$uri = $this->_uri_string($uri);

		if ($this->item('enable_query_strings') === FALSE) {
			// customize
			$suffix = empty($suffix) ? (isset($this->config['url_suffix']) && !empty($this->config['url_suffix']) ? '.'.$this->config['url_suffix'] : '') : '.'.$suffix;

			if ($suffix !== '') {
				if (($offset = strpos($uri, '?')) !== FALSE) {
					$uri = substr($uri, 0, $offset).$suffix.substr($uri, $offset);
				} else {
					$uri .= $suffix;
				}
			}

			return $this->slash_item('index_page').$uri;
		} elseif (strpos($uri, '?') === FALSE) {
			$uri = '?'.$uri;
		}

		return $this->item('index_page').'/'.$uri;
	}
}
/* EOF */