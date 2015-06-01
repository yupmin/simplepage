<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Account_manager {
	protected $_userid_type;
	protected $_userid_length;
	protected $_expire;

	public function __construct() {
		$this->CI =& get_instance();

		$this->_userid_type = 'loweralnum';
		$this->_userid_length = 16;
		$this->_expire = 3600 * 24 * 7;
	}

	public function get_account_user_level(){
		$_user_level = $this->CI->session->userdata('account_user_level');
		return empty($_user_level) ? FALSE : $_user_level;	
	}
	public function get_account_name(){
		$_name = $this->CI->session->userdata('account_name');
		return empty($_name) ? FALSE : $_name;	
	}
	public function get_account_userid(){
		$_userid = $this->CI->session->userdata('account_userid');
		return empty($_userid) ? FALSE : $_userid;	
	}

	public function _set_userdata($account_name, $account_user_level, $userid, $timezone = NULL, $language_key = NULL, $expire = NULL) {
		if (is_null($expire)) $expire = $this->_expire;

		if (empty($timezone)) {
			$timezone = $this->CI->config->item('default_timezone');
		}
		if (empty($language_key)) {
			$language_key = $this->CI->config->item('default_language_key');
		}
		$this->CI->session->set_userdata(array('account_name' => $account_name, 'account_user_level' => $account_user_level, 'account_userid' => $userid));
		$this->CI->input->set_cookie(array('name'=>'timezone', 'value' => $timezone, 'expire' => $expire));
		$this->CI->input->set_cookie(array('name'=>'language_key', 'value'=> $language_key, 'expire' => $expire));
		return TRUE;
	}

	public function signin_by_userid($userid, $password, $stay_signed_in = NULL, $expire = NULL) {
		if (is_null($stay_signed_in)) $stay_signed_in = FALSE;
		if (is_null($expire)) $expire = $this->_expire;

		$this->CI->config->load('user');
		$_users = $this->CI->config->item('users');
		$account = (object) $_users[$userid];

		if ($account->password != $password) return FALSE;
		if (!$this->_set_userdata($account->name, $account->user_level, $userid)) return FALSE;

		$this->CI->input->set_cookie(array('name'=>'userid', 'value'=> $userid, 'expire' => $expire));

		return TRUE;
	}

	public function signout($userid) {
		$this->CI->session->sess_destroy();
		return TRUE;
	}
}
/* EOF */