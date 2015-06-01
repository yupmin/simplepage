<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
use Ikimea\Browser\Browser;

class Browser_manager {
	protected $_this_browser;
	protected $_this_mobile_detect;

	public function __construct() {
		$this->CI =& get_instance();

		if (class_exists('Ikimea\Browser\Browser') && class_exists('Mobile_Detect')) {
			$this->_this_browser = new Browser;
			$this->_this_mobile_detect = new Mobile_Detect;
		} else {
			$this->CI->load->library('user_agent');
		}
	}

	public function is_ie_greater($version) {
		if (isset($this->_this_browser)) {
			return $this->_this_browser->getBrowser() == Browser::BROWSER_IE && version_compare($this->_this_browser->getVersion(), $version) >= 0;
		} else {
			return $this->CI->agent->is_browser('Internet Explorer') && version_compare($this->CI->agent->version(), $version) >= 0;
		}
	}
	public function is_ie_lesser($version) {
		if (isset($this->_this_browser)) {
			return $this->_this_browser->getBrowser() == Browser::BROWSER_IE && version_compare($this->_this_browser->getVersion(), $version) < 0;
		} else {
			return $this->CI->agent->is_browser('Internet Explorer') && version_compare($this->CI->agent->version(), $version) < 0;
		}
	}
	public function get_browser() {
		if (isset($this->_this_browser)) {
			return $this->_this_browser->getBrowser();
		} else {
			return $this->CI->agent->browser();
		}
	}
	public function get_version() {
		if (isset($this->_this_browser)) {
			return $this->_this_browser->getVersion();
		} else {
			return $this->CI->agent->version();
		}
	}
	public function is_mobile() {
		if (isset($this->_this_mobile_detect)) {
			return $this->_this_mobile_detect->isMobile();
		} else {
			return $this->CI->agent->is_mobile();
		}
	}
	public function is_tablet() {
		if (isset($this->_this_mobile_detect)) {
			return $this->_this_mobile_detect->isTablet();
		} else {
			return TRUE;
		}
	}
	public function get_platform() {
		if (isset($this->_this_browser)) {
			return $this->_this_browser->getPlatform();
		} else {
			return $this->CI->agent->platform();
		}
	}
	public function set_useragent($agent) {
		if (isset($this->_this_browser)) {
			$this->_this_browser->setUserAgent($agent);
		}
		if (isset($this->_this_mobile_detect)) {
			$this->_this_mobile_detect->setUserAgent($agent);
		}
		return TRUE;
	}
	public function set_httpheader($header) {
		if (isset($this->_this_mobile_detect)) {
			$this->_this_mobile_detect->setHttpHeaders($header);
		}
		return TRUE;
	}

	public function get_view_format($output_format) {
		if ($output_format == 'html') {
			if ($this->is_mobile() && !$this->is_tablet()) {
				// ipad, android table ?
				$_view_format = 'mobile';
			} else {
				$_view_format = 'html5';
				switch(strtolower($this->get_browser())) {
					case 'firefox':
						if (version_compare($this->get_version(), '4.0') < 0)
							$_view_format = 'xhtml';
						break;
					case 'chrome':
						if (version_compare($this->get_version(), '9.0') < 0)
							$_view_format = 'xhtml';
						break;
					case 'internet explorer':
						if (version_compare($this->get_version(), '9.0') < 0)
							$_view_format = 'xhtml';
						break;
					case 'safari':
						if (version_compare($this->get_version(), '5.0') < 0)
							$_view_format = 'xhtml';
						break;
					case 'opera':
						if (version_compare($this->get_version(), '10.6') < 0)
							$_view_format = 'xhtml';
						break;
					default:
						$_view_format = 'html5';
				}
			}
		} else
			$_view_format = $output_format;
		return $_view_format;
	}
}
/* EOF */