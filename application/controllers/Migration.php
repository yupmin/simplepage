<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration extends MY_Controller {
	protected $_this_version = 0;
	protected $_max_version = 0;

	public function __construct() {
		parent::__construct();

		$this->load->library('migration_manager');

		if ($this->input->is_cli_request()) {
			set_time_limit(0);

		} else {
			$this->load->library('form_validation');
			$this->load->helper('bootstrap3');

			// render
			$this->_add_stylesheet('bootstrap.min');
			$this->_add_stylesheet('bootstrap-theme.min');
			$this->_add_stylesheet('migration');
			$this->_add_stylesheet('font-awesome.min');
			$this->_add_stylesheet('default');
			$this->_add_footer_javascript('jquery-2.1.3.min');
			$this->_add_footer_javascript('bootstrap');
			$this->_add_footer_javascript('ie10-viewport-bug-workaround');
		}
	}

	private function _before_install() {
		return @chmod(FCPATH.'public/attachment', DIR_WRITE_MODE)
			&& @chmod(FCPATH.'private/cache', DIR_WRITE_MODE)
			&& @chmod(FCPATH.'private/logs', DIR_WRITE_MODE)
			&& @chmod(FCPATH.'private/sessions', DIR_WRITE_MODE)
			&& @chmod(FCPATH.'private/attachment', DIR_WRITE_MODE);
	}

	public function prepare() {
		$_result = @chmod(APPPATH.'config', DIR_WRITE_MODE)
			&& @chmod(APPPATH.'config/database.php', FILE_WRITE_MODE)
			&& @chmod(FCPATH.'private/sessions', DIR_WRITE_MODE);

		echo 'migration result : '.($_result ? 'ok':'fail')."\n";
	}

	// web
	public function setup() {
		// check configuration && database_revision
		$_site_name = $this->config->item_value('site_name', 'Simplepage');
		$_site_key = $this->config->item_value('site_key', 'simplepage');
		$_service_domain = $this->config->item_value('service_domain', $this->input->server('HTTP_HOST'));
		$_configs = array('site_name' => $_site_name
			, 'site_key' => $_site_key
			, 'service_domain' => $_service_domain);

		$_data = NULL;
		$_error = FALSE;
		$_message = $_result = NULL;

		if (!file_exists(APPPATH.'config/extension.php')) {
			if ($this->input->post()) {
				$this->form_validation->set_rules('config[site_name]', l('site name'), 'trim|required');
				$this->form_validation->set_rules('config[site_key]', l('site key'), 'trim|required');
				$this->form_validation->set_rules('config[service_domain]', l('service domain'), 'trim|required');
				$this->form_validation->set_rules('account[userid]', l('userid'), 'trim|required|callback_confirm_userid');
				$this->form_validation->set_rules('account[name]', l('name'), 'trim|required');
				$this->form_validation->set_rules('account[password]', l('password'), 'trim|required|matches[verify_password]|md5');
				$this->form_validation->set_rules('verify_password', l('verify password'), 'trim|required|md5');

				if ($this->form_validation->run() && ($_config = $this->input->post('config'))
					&& ($_account = $this->input->post('account'))
					&& ($_verify_password = $this->input->post('verify_password'))) {

					/* Site */
					$_config['redirect_manage'] = '/manage/user';
					$_config['allow_signin'] = TRUE;
					$_config['use_secure_account'] = FALSE;

					$_config['default_language_key'] = '';
					$_config['default_timezone'] = '';

					/* Email */
					$_config['default_email_protocol'] = 'sendmail';
					$_config['default_email_mailpath'] = NULL;
					$_config['default_email_smtp_host'] = NULL;
					$_config['default_email_smtp_user'] = NULL;
					$_config['default_email_smtp_pass'] = NULL;
					$_config['default_email_smtp_timeout'] = NULL;
					$_config['default_email_from_name'] = '';
					$_config['default_email_from_email'] = '';

					/* Debug log */
					$_config['use_write_log'] = FALSE;
					$_account['user_level'] = 'administrator';
					$_users['users'] = array($_account['userid'] => elements(array('name', 'password', 'user_level'), $_account));

					if ($this->migration_manager->update_config_by($_config)
						&& $this->migration_manager->update_config_by($_users, 'user')) {

					} else {
						$_error = TRUE;
						$_config_extension_path = APPPATH.'config/extension.php';
						$_message = l('change file permission 666 \'%s\'.', $_config_extension_path);
					}
				} else {
					$_error = TRUE;
					$_message = l('failed to set up.');
				}

				if ($_error) {
					raise_message($_message, 'danger');
				} else {
					$this->_redirect();
				}
			}

			$_data['service_domain'] = $_configs['service_domain'];
			$_data['site_name'] = $_configs['site_name'];
			$_data['site_key'] = $_configs['site_key'];

			// view
			$this->_partial('main_content', 'migration-setup', $_data);
			$this->_render('migration', $_data);
			return;
		}
	}

	// callback
	public function confirm_userid($userid) {
/*		if (is_null($user)) {
			$this->form_validation->set_message('confirm_userid', l('userid is not exists.'));
			return FALSE;
		}*/

		return TRUE;
	}
}