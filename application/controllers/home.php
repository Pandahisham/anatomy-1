<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Index_model', 'Index');
	}
	
	function index()
	{	
		$data['title'] = "Welcome";
		$data['classes'] = $this->Index->getClassYears();
		$this->template->load('template/tmp', 'index', $data);
	}
	
	function scorecard()
	{
		if (!$this->session->userdata('user_id'))
			redirect('account/signin', 'location', 301);
		
		$data['scores'] = $this->Index->getScores();
		$data['title'] = "Scorecard";
		$this->template->load('template/tmp', 'scorecard', $data);
	}
	
	function email()
	{
		if (!$this->session->userdata('user_id') && $this->session->userdata('user_id') != 1)
			redirect('home', 'location', 301);
		
		$this->load->library('email');
		$emails = $this->Index->getEmails();
		foreach ($emails as $address)
		{
			$this->email->clear();

			$this->email->to($address['email']);
			$this->email->from('admin@juxi.phlybuy.com', 'UTHSCSA Anatomy Quiz Bank');
			$this->email->subject('Modules 5 and 6 now available');
			$this->email->message('Hello!
			
Just letting you know that UTHSCSA Anatomy Modules 5 and 6 are now available at juxi.phlybuy.com

I also added the Scorecard feature, which reports various statistics about the lecture quizzes you have taken and compares them to your class\'s average.
			
			
Cheers,
http://juxi.phlybuy.com
				
				
This is an automated email. Don\'t reply to this message.');
			$this->email->send();
		}
	}
}

/* End of file home.php */
/* Location: ./system/application/controllers/home.php */

?>