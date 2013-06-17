<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Account_model', 'Account');
		$this->template->addCSS('account');
	}
	
	function index()
	{
		// Redirect if not logged in
		if (!$this->session->userdata('user_id'))
			redirect('account/signin', 'location', 301);
		$sid = $this->session->userdata('user_id');
		$data['user'] = $this->Account->getUser('id', $sid);
		$data['error'] = array();
		if ($_POST) {
			$step = $this->input->post('step');
			// Change Email
			if ($step == "Email") {
				$email = $this->input->post('email');
				if ($email != "" && !preg_match("/^\w+@([a-zA-Z0-9-]+\.)?[a-zA-Z0-9-]+\.[a-zA-Z]{2,6}$/", $email))
					$data['error'][] = 'That is not a valid email address.';
				// Update info
				if (empty($data['error'])) {
					$key = mt_rand(1000,9999) . "j" . mt_rand(1000,9999) . "x";
					if ($this->Account->addRequest($sid, 'email', $email, $key)) {
						$this->load->library('email');
						$this->email->from('no-reply@juxi.phlybuy.com', 'UTHSCSA Anatomy Galen Quiz Bank');
						$this->email->to($email);
						$this->email->subject("Change Email Request");
						$this->email->message("
						Dear UTHSCSA Student,
						
						You have recently requested to change your Anatomy Galen Quiz Bank email address to " . $email . ". If this is true, please click the link below to verify this new email adddress:
						"
						. site_url('account/requests/' . $sid . '/' . $key) .
						"
						If you did not make this request then please ignore this email.
						
						Regards,
						http://juxi.phlybuy.com");
						$this->email->send();
						$data['error'][] = 'A confirmation code has been sent to ' . $email;
					} else
						$data['error'][] = 'There was a problem with the databse. Contact the gods.';
				}
			}
			// Change Password
			else {
				$pw = $this->input->post('password');
				$vpw = $this->input->post('vpassword');
				$opw = $this->input->post('opassword');
				if ($data['user']['password'] != $this->Account->pbkdf2($opw))
					$data['error'][] = 'Old password is incorrect.';
				if ($pw != $vpw)
					$data['error'][] = 'New passwords do not match.';
				if (strlen($pw) < 6 || strlen($pw) > 12)
					$data['error'][] = 'Password must be between 6 and 12 characters.';
				if (empty($data['error'])) {
					// Change password to new one
					$this->Account->changePassword($pw);
					$data['error'][] = 'Your password was changed successfully.';
				}
			}
		}
        $this->load->helper('form');
		$data['title'] = "Account Settings";
		$this->template->addJS('account');
		$this->template->load('template/tmp', 'account/index', $data);
	}
	
	function register()
	{
		// Redirect if already logged in
		if ($this->session->userdata('user_id'))
			redirect('account', 'location', 301);
		$this->load->helper('form'); // used for "set_value" in view
		$data['success'] = false;
		$data['error'] = array();
		if ($_POST) {
			$email = $this->input->post('email');
			$checkEmail = $this->Account->getUser('email', $email);
			if (!preg_match("/^[a-zA-Z0-9_.-]+@{1}\blivemail.uthscsa.edu\b$/", $email))
				$data['error'][] = 'That is not a valid email address.';
			else if ( !empty($checkEmail) )
				$data['error'][] = 'That email address is already being used.';
			$pw = $this->input->post('password');
			$vpw = $this->input->post('vpassword');
			$class = $this->input->post('class');
			if ($pw != $vpw)
				$data['error'][] = 'Passwords do not match.';
			else if (strlen($pw) < 6 || strlen($pw) > 12)
				$data['error'][] = 'Password must be between 6 and 12 characters.';
			if (empty($class))
				$data['error'][] = 'Please select a class year.';
			if (empty($data['error'])) {
				$key =  mt_rand(1000,9999) . "j" . mt_rand(1000,9999) . "x";
				if ($this->Account->createUser($email, $pw, $class, $key)) {
					$user = $this->Account->getUser('email', $email); // get the new user's ID
					$this->load->library('email');
					$this->email->from('no-reply@juxi.phlybuy.com', 'UTHSCSA Anatomy Galen Quiz Bank');
					$this->email->to($email);
					$this->email->subject("Verify Email Address");
					$this->email->message("
					Dear UTHSCSA Student,
					
					Thank you for taking the time to register an account for the Anatomy Galen Quiz Bank. Click the link below to activate your account:
					" 
					. site_url('account/requests') . '/' . $user['id'] . '/' . $key . 
					"
					
					Thank you,
					http://juxi.phlybuy.com");
					$this->email->send();
					$data['success'] = true;
				}
				else
					$data['error'][] = 'There appears to be a problem with the database. Please contact the gods.';
			}
		}
		$data['classes'] = $this->Account->getClassYears();
		$data['title'] = "Create an Account";
		$this->template->load('template/tmp', 'account/register', $data);
	}
	
	function requests()
	{
		// Only if the user_id has any pending requests in the `users_requests` table
		// It changes the col id if the keys match
		// ../account/requests/user_id/key
		// Currently used for: Account activation, Email changes
		$uid = $this->uri->segment(3);
		$key = $this->uri->segment(4);
		$data['request'] = $this->Account->doRequest($uid, $key); // returns '$col'
		$data['title'] = 'Performing Request';
		$this->template->load('template/tmp', 'account/requests', $data);
	}
	
	function goodbye()
	{
		$this->session->sess_destroy();
		redirect('', 'location', 301);
	}
	
	function signin()
	{
		// Redirect if already logged in
		if ($this->session->userdata('user_id'))
			redirect('account', 'location', 301);
		if ($_POST) {
			$email = $this->input->post('email');
			$pw = $this->input->post('password');
			$log = $this->Account->login($email, $pw);
			if (!empty($log)) {
				if ($log['active'] == '1') {
					// initiliaze cookie values
					$cookie = array(
						'user_id' => $log['id'],
						'role' => $log['role'],
						'class' => $log['class']
					);
					$this->session->set_userdata($cookie);
					// Redirect to user's profile page
					redirect('home', 'location', 301);
				}
				else {
					$this->session->set_flashdata('error', 'This account hasn\'t been activated yet.');
					redirect('account/signin', 'location', 301);
				}
			}
			else {
				// Login failed
				$this->session->set_flashdata('error','Username or password incorrect. Please try again.');
				redirect('account/signin', 'location', 301);
			}
		}
		$data['title'] = "Sign in";
		$this->template->load('template/tmp', 'account/signin', $data);
	}
	
	function resend()
	{
		// Redirect if already logged in
		if ($this->session->userdata('user_id'))
			redirect('account', 'location', 301);
		$data['success'] = false;
		if ($_POST) {
			$email = $this->input->post('email');
			if ($email == "") {
				$this->session->set_flashdata('error', 'Enter an email address.');
				redirect('account/resend', 'location', 301); // reload due to errors
			}
			$user = $this->Account->getUser('email', $email);
			if ($user && $user['active'] != '1') {
				// Get the key
				$request = $this->Account->getRequest($user['id'], 'active');
				// Account is valid and inactive, so email user
				$this->load->library('email');
				$this->email->from('no-reply@juxi.phlybuy.com', 'UTHSCSA Anatomy Galen Quiz Bank');
				$this->email->to($email);
				$this->email->subject("Verify Email Address");
				$this->email->message("
				Dear UTHSCSA Student,
				
				Thank you for taking the time to register an account for the Anatomy Galen Quiz Bank. Click the link below to activate your account:
				" 
				. site_url('account/requests/' . $user['id'] . '/' . $request['key']) . 
				"
				
				Thank you,
				http://juxi.phlybuy.com");
				$this->email->send();
				$data['success'] = true;
			}
			else {
				$this->session->set_flashdata('error', 'Either that account has already been activated or the email is incorrect.');
				redirect('account/resend', 'location', 301);
			}
		}
		$data['title'] = 'Resend Activation Code';
		$this->template->load('template/tmp', 'account/resend', $data);
	}
	
	function recover()
	{
		// Recover user's password ---- Need to create some extra security to prevent potential hackers and spam bots
		// Redirect if already logged in
		if ($this->session->userdata('user_id'))
			redirect('account', 'location', 301);
		$data['success'] = false;
		$data['error'] = null;
		if ($_POST) {
			$email = $this->input->post('email');
			if ($email == "")
				$data['error'] = 'Enter an email address.';
			if (is_null($data['error'])) {
				//$pw = $this->Account->getPassword($email);
				if ($pw != false) {
					// mail user
					$msg = "You recently requested a password recovery at PhlyBuy.com. Below is your password:\r\n\r\n$pw\r\n\r\n Please visit http://www.phlybuy.com/index.php/account/activate to activate your account. If you believe this is a mistake, please ignore this email.\r\n\r\nThank you,\r\nphlyBuy Team\r\nhttp://www.phlybuy.com";
					$headers = 'From: noreply@phlybuy.com' . "\r\n" . 'Reply-To: noreply@phlybuy.com' . "\r\n" . 'X-Mailer: PHP/' . phpversion();
					//mail($email, 'PhlyBuy Activation Code', $msg, $headers);
					$data['succces'] = true;
				}
				else
					$data['error'] = 'That email address is incorrect.';
			}
		}
		$data['title'] = 'Account Recovery';
		$this->template->load('template/tmp', 'account/recover', $data);
	}
	
}

/* End of file account.php */
/* Location: ./system/application/controllers/account.php */

?>