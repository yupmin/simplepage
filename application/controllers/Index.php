<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends MY_Controller {
	public function __construct() {
		parent::__construct();

		$this->load->helper('bootstrap3');

		// view
		$this->_add_stylesheet('bootstrap.min');
		$this->_add_stylesheet('bootstrap-theme.min');
		$this->_add_stylesheet('font-awesome.min');
		$this->_add_stylesheet('default');
		$this->_add_footer_javascript('jquery-2.1.3.min');
		$this->_add_footer_javascript('bootstrap');
		$this->_add_footer_javascript('url');
		$this->_add_footer_javascript('default');

		$_data = NULL;
		$this->_partial('header', 'header', $_data);
		$this->_partial('footer', 'footer', $_data);
		#$this->output->enable_profiler(TRUE);
	}

	public function index() {
		$_data = NULL;
		
		// view
		$this->_partial('main_content', 'index-index', $_data);
		$this->_render();
	}
}
/* EOF */