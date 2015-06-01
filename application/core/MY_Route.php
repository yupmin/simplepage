<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Router extends CI_Router {
	protected function _validate_request($segments) {
		$c = count($segments);
		// Loop through our segments and return as soon as a controller
		// is found or when such a directory doesn't exist

		// customize : suffix remove at this time
		$_last_segment_index = count($segments) > 0 ? count($segments) - 1 : 0;
		if (($_pos = strrpos($segments[$_last_segment_index], '?')) !== FALSE) {
			$_get_params = substr($segments[$_last_segment_index], $_pos + 1, strlen($segments[$_last_segment_index]) - $_pos);
			$segments[$_last_segment_index] = substr($segments[$_last_segment_index], 0, $_pos);
			parse_str($_get_params, $_get_params);
			$_GET = array_merge($_GET, $_get_params);
		}
		if (($_pos = strrpos($segments[$_last_segment_index], '.')) !== FALSE) {
			$_suffix = substr($segments[$_last_segment_index], $_pos + 1, strlen($segments[$_last_segment_index]) - $_pos);
			$segments[$_last_segment_index] = substr($segments[$_last_segment_index], 0, $_pos);
		}

		while ($c-- > 0) {
			$test = $this->directory
				.ucfirst($this->translate_uri_dashes === TRUE ? str_replace('-', '_', $segments[0]) : $segments[0]);

			if (!file_exists(APPPATH.'controllers/'.$test.'.php') && is_dir(APPPATH.'controllers/'.$this->directory.$segments[0])) {
				$this->set_directory(array_shift($segments), TRUE);
				continue;
			}
			/* customize */
			else if (file_exists(APPPATH.'controllers/'.$test.'.php')) {
				// do nothing
			} else {
				if (file_exists(APPPATH.'controllers/'.ucfirst('index').'.php')) {
					$segments = array_merge(array('index'), $segments);
				}
			}

/*			// customize : suffix remove at this time
			$_last_segment_index = count($segments) > 0 ? count($segments) - 1 : 0;
			if (($_pos = strrpos($segments[$_last_segment_index], '?')) !== FALSE) {
				$_get_params = substr($segments[$_last_segment_index], $_pos + 1, strlen($segments[$_last_segment_index]) - $_pos);
				$segments[$_last_segment_index] = substr($segments[$_last_segment_index], 0, $_pos);
				parse_str($_get_params, $_get_params);
				$_GET = array_merge($_GET, $_get_params);
			}

			$_suffix = NULL;
			if (($_pos = strrpos($segments[$_last_segment_index], '.')) !== FALSE) {
				$_suffix = substr($segments[$_last_segment_index], $_pos + 1, strlen($segments[$_last_segment_index]) - $_pos);
				$segments[$_last_segment_index] = substr($segments[$_last_segment_index], 0, $_pos);
			}*/

			return $segments;
		}

		// This means that all segments were actually directories
		return $segments;
	}
}
// EOF