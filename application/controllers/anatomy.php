<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Anatomy extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('user_id'))
			redirect('account/signin', 'location', 301);
	}
	
	function index()
	{	
		$data['title'] = "Welcome";
		$this->template->load('template/tmp', 'index', $data);
	}
	
	function lecture()
	{
		$this->load->model('Anatomy_model', 'Ana');
		$this->template->addCSS('anatomy');
		$this->template->addJS('anatomy');
		$lid = $this->uri->segment(3);
		$new = $this->uri->segment(4);
		if ($lid == "")
		{
			// Home page
			$data['title'] = "Choose a Lecture";
			$data['lectures'] = $this->Ana->getLectures();
		}
		else if ($new == "new")
		{
			// Returns the lecture info and starts a new session
			$data['data'] = $this->Ana->startLecture($lid);
			if (!$data['data'])
				redirect('anatomy/lecture', 'location', 301);
			$data['title'] = "Lecture " . $lid . ": " . $data['data']['info']['name'];
			
		}
		else if ($new == "filter")
		{
			// Starts the session where it last left off
			$data['data'] = $this->Ana->getSession(0, $lid, 'lecture');
			if (!$data['data'])
				$data['title'] = "No Questions Found";
			else
				$data['title'] = "Lecture " . $lid . ": " . $data['data']['info']['name'];
		}
		else
		{
			// Starts the session where it last left off
			$data['data'] = $this->Ana->getSession(0, $lid, 'lecture');
			if (!$data['data'])
			{
				// Starts a new session for this lecture
				$data['data'] = $this->Ana->startLecture($lid);
				if (!$data['data'])
					redirect('anatomy/lecture', 'location', 301);
			}
			$data['title'] = "Lecture " . $lid . ": " . $data['data']['info']['name'];
		}
		$data['filters'] = $this->Ana->getFilters();
		$data['ranks'] = $this->Ana->getRanks();
		$this->template->load('template/tmp', 'anatomy/lecture', $data);
	
	}
	
	function module()
	{
		$this->load->model('Anatomy_model', 'Ana');
		$this->template->addCSS('anatomy');
		$this->template->addJS('anatomy');
		$mid = $this->uri->segment(3);
		$new = $this->uri->segment(4);
		if ($mid == "")
		{
			// Home Page
			$data['title'] = "Choose a Module";
			$data['modules'] = $this->Ana->getModules();
		}
		else if ($new == "new")
		{
			// Returns the module info and starts a new session
			$data['data'] = $this->Ana->startModule($mid);
			if (!$data['data'])
				redirect('anatomy/module', 'location', 301);
			$data['title'] = "Module " . $mid;
		}
		else if ($new == "filter")
		{
			// Starts the session where it last left off
			$data['data'] = $this->Ana->getSession($mid, 0, 'module');
			if (!$data['data'])
				$data['title'] = "No Questions Found";
			else
				$data['title'] = "Module " . $mid;
		}
		else
		{
			// Starts the session where it last left off
			$data['data'] = $this->Ana->getSession($mid, 0, 'module');
			if (!$data['data'])
			{
				// Starts a new session for this module
				$data['data'] = $this->Ana->startModule($mid);
				if (!$data['data'])
					redirect('anatomy/module', 'location', 301);
			}
			$data['title'] = "Module " . $mid;
		}
		$data['filters'] = $this->Ana->getFilters();
		$data['ranks'] = $this->Ana->getRanks();
		$this->template->load('template/tmp', 'anatomy/module', $data);
	}
	
	function course()
	{
		$this->load->model('Anatomy_model', 'Ana');
		$this->template->addCSS('anatomy');
		$this->template->addJS('anatomy');
		$data['title'] = "Course Questions in Random Order";
		if ($this->uri->segment(3) == "new")
		{
			// Returns the question info and starts a new session
			$data['data'] = $this->Ana->startCourse();
			if (!$data['data'])
				redirect('', 'location', 301);
		}
		else
		{
			// Starts the session where it last left off
			$data['data'] = $this->Ana->getSession(0, 0, 'course');
			if (!$data['data'])
			{
				// Starts a new session for this course
				$data['data'] = $this->Ana->startCourse();
				if (!$data['data'])
					redirect('', 'location', 301);
			}
		}
		$data['filters'] = $this->Ana->getFilters();
		$data['ranks'] = $this->Ana->getRanks();
		$this->template->load('template/tmp', 'anatomy/course', $data);
	}
	
	function quiz()
	{
		$this->load->model('Anatomy_model', 'Ana');
		$this->template->addCSS('anatomy');
		$this->template->addJS('anatomy');
		$lid = $this->uri->segment(3);
		if ($lid == "")
		{
			// Home page
			$data['title'] = "Choose a Quiz";
			$data['lectures'] = $this->Ana->getLectures();
		}
		else
		{
			// Starts a new session for this lecture
			$data['data'] = $this->Ana->startQuiz($lid);
			if (!$data['data'])
				redirect('anatomy/quiz', 'location', 301);
			$data['title'] = "Quiz " . $lid . ": " . $data['data']['info']['name'];
		}
		$this->template->load('template/tmp', 'anatomy/quiz', $data);
	}
	
	/*
		AJAX request for verifying an answer is correct
	*/
	function submitAnswer()
	{
		$this->load->model('Anatomy_model', 'Ana');
		$answer = $this->input->get("answer");
		$result = $this->Ana->verifyAnswer($answer);
		echo $result;
	}
	
	/*
		AJAX request for the next question
	*/
	function nextQuestion()
	{
		$this->load->model('Anatomy_model', 'Ana');
		$result = json_encode($this->Ana->nextQuestion());
		echo $result;
	}
	
	/*
		Sets the filters and restarts the quiz
	*/
	function setFilters()
	{
		$this->load->model('Anatomy_model', 'Ana');
		$type = $this->uri->segment(3);
		$id = "";
		if ($type == "lecture")
			$id = $this->session->userdata('lecture');
		else if ($type == "module")
			$id = $this->session->userdata('module');
		else
			$id = "0";
		$keywords = $this->input->post("keywords");
		if ($keywords == "Keywords")
			$keywords = "";
		$exam = $this->input->post("exam");
		$rank = $this->input->post("rank");
		$this->Ana->setFilters($exam, $rank, $keywords);
		redirect('anatomy/'.$type.'/'.$id.'/filter', 'location', 301);
	}
	
	/*
		AJAX request that sets the new difficulty ranking for a question
	*/
	function changeRank()
	{
		$this->load->model('Anatomy_model', 'Ana');
		$rank = $this->input->get("rank");
		$this->Ana->changeRank($rank);
	}
	
	/*
		AJAX POPUP that confirms stopping the quiz
	*/
	function quitQuiz()
	{
		$data['title'] = "Stop Quiz";
		$this->template->load('template/popup', 'anatomy/quit', $data);
	}
	
	function timeout()
	{
		$data['title'] = "Out of Time";
		$this->load->model('Anatomy_model', 'Ana');
		$this->Ana->destroySession(1);
		$this->template->load('template/popup', 'anatomy/timeout', $data);
	}
	
	/*
		AJAX POPUP that deletes the session and marks the scorecard
	*/
	function quitQuizConfirm()
	{
		$this->load->model('Anatomy_model', 'Ana');
		$this->Ana->destroySession(1);
		redirect('anatomy/quiz', 'location', 301);
	}
}

/* End of file anatomy.php */
/* Location: ./system/application/controllers/anatomy.php */

?>