<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Account extends MY_Controller {
	protected $_default_expire = 86400;
	protected $_per_page = 10;

	public function __construct() {
		parent::__construct();

		// set initial return url
		if (strlen($this->_return_url) != 0 && $this->_this_url != $this->_return_url) {
			$this->_this_url = $this->_return_url;
			$this->_data['self']->this_url = $this->_this_url;
		} else if (strlen($this->_return_url) == 0) {
			$this->_this_url = $this->_return_url = '/';
			$this->_data['self']->this_url = $this->_this_url;
			$this->_data['self']->return_url = $this->_return_url;
		}

		// load library
		$this->load->library('account_manager');
		$this->load->library('typography');
		$this->load->library('form_validation');
		$this->load->helper('bootstrap3');

		// view
		$this->_add_stylesheet('bootstrap.min');
		$this->_add_stylesheet('bootstrap-theme.min');
		$this->_add_stylesheet('default');
		$this->_add_stylesheet('font-awesome.min');
		$this->_add_footer_javascript('jquery-2.1.3.min');
		$this->_add_footer_javascript('bootstrap');
		$this->_add_footer_javascript('url');
		$this->_add_footer_javascript('default');

		// header render
		$_data = NULL;
		$this->_partial('header', 'header', $_data);
		$this->_partial('footer', 'footer', $_data);
		#$this->output->enable_profiler(TRUE);
	}

	public function signin() {
		$_data = NULL;
		$_error = FALSE;
		$_message = $_result = NULL;

		$_account = NULL;
		if($this->input->post()) {
			// validate
			$this->form_validation->set_rules('account[userid]', l('userid'), 'trim|required|callback_confirm_userid');
			$this->form_validation->set_rules('account[password]', l('password'), 'trim|required|md5|callback_verify_password');

			if ($this->form_validation->run() && ($_account = $this->input->post('account', TRUE))) {

				if ($this->account_manager->signin_by_userid($_account['userid'], $_account['password'])) {
					// do nothing
				} else {
					$_error = TRUE;
				}

				if ($_error) {
					$_message = l('failed to sign in.');
				} else {
					$_message = l('successfully signed in.');
				}

				if ($this->input->is_ajax_request() && in_array($this->_output_format, array('json', 'xml'))) {
					$this->_render_response($_error, $_message, $_result);
					return;
				} else {
					if ($_error) {
						raise_message($_message, 'danger');
					} else {
						raise_flash($_message, 'success');
						$this->_redirect();
					}
				}
			} else {
				$_error = TRUE;
				$_message = l('failed to sign in.');
				raise_message($_message, 'danger');
			}
		}

		// initialize data
		// account email
		$_data['account_userid'] = $this->input->cookie('userid') ? $this->input->cookie('userid') : NULL;
		if($_account) {
			$_data['account_userid'] = element('userid', $_account, $_data['account_userid']);
		}

		// form post values
		$_data['form_post_values'] = array();
		if (!empty($this->_return_url)) $_data['form_post_values']['return_url'] = $this->_return_url;

		// view
		$this->_set_head_title(l('sign in'));
		$this->_partial('main_content', 'account-signin', $_data);
		$this->_render();
	}
	public function signout() {
		if($_userid = $this->account_manager->get_account_userid()) {
			$this->account_manager->signout($_userid);
		}

		$this->_redirect();
	}

	// callback
	public function confirm_userid($userid) {
		$this->config->load('user');
		$_users = $this->config->item('users');

		if (!in_array($userid, array_keys($_users))) {
			$this->form_validation->set_message('confirm_userid', l('userid is not exists.'));
			return FALSE;
		}

		$this->_callback_userid = $userid;
		return TRUE;
	}
	public function verify_password($password) {
		if (!empty($this->_callback_userid)) {
			$this->config->load('user');
			$_users = $this->config->item('users');

			$account = (object) $_users[$this->_callback_userid];

			if ($account->password != $password) {
				$this->form_validation->set_message('verify_password', l('password is not mached.'));
				return FALSE;
			}
		}
		return TRUE;
	}
}
/* EOF */