<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Input extends CI_Input {
	public function post($index = '', $xss_clean = FALSE) {
		// this will be true if post() is called without arguments
		if($index === '') {
			return ($_SERVER['REQUEST_METHOD'] === 'POST');
		}

		// otherwise do as normally
		return parent::post($index, $xss_clean);
	}

	public function file($index = NULL) {
		if (empty($_FILES)) return FALSE;

		$files = array();
		foreach($_FILES as $a => $b) {
			$files[$a] = array();

			if (is_array($b['name'])) {
				foreach($b as $c => $d) {
					if (is_array($d)) {
						foreach($d as $e => $f) {
							if ($f > 0)
							if (!isset($files[$a][$e]) || !is_array($files[$a][$e]))
								$files[$a][$e] = array();

							if (is_array($f)) {
								foreach($f as $g => $h) {
									if (!empty($h)) {
										if (!isset($files[$a][$e][$g]) || !is_array($files[$a][$e][$g]))
											$files[$a][$e][$g] = array();

										$files[$a][$e][$g][$c] = $h;
									}
								}
							} else if (!empty($f)) {
								$files[$a][$e][$c] = $f;
							}
						}
					}
				}
			} else {
				$files[$a] = $b;
			}
		}

		return $this->_fetch_from_array($files, $index);
	}

	public function file_value($index = '', $default = NULL) {
		$_value = $this->file($index);
		return !is_null($_value) ? $_value : $default;
	}

	public function get_value($index = '', $default = NULL, $xss_clean = FALSE) {
		$_value = $this->get($index, $xss_clean);
		return !is_null($_value) ? $_value : $default;
	}

	public function post_value($index = '', $default = NULL, $xss_clean = FALSE) {
		$_value = $this->post($index, $xss_clean);
		return !is_null($_value) ? $_value : $default;
	}

	public function get_post_value($index = '', $default = NULL, $xss_clean = FALSE) {
		$_value = $this->get_post($index, $xss_clean);
		return !is_null($_value) ? $_value : $default;
	}

	public function post_get_value($index = '', $default = NULL, $xss_clean = FALSE) {
		$_value = $this->post_get($index, $xss_clean);
		return !is_null($_value) ? $_value : $default;
	}
}
/* EOF */