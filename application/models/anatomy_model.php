<?php
class Anatomy_model extends CI_Model {
	
	// Module id
	private $mid;
	// Lecture id
	private $lid;
	// Question id
	private $qid;
	// Session id
	private $sid;
	// Session type
	private $type;
 
    function Index_model()
    {
        parent::__construct();
    }
    
    function __construct()
    {
    	$this->mid = $this->session->userdata('module');
        $this->lid = $this->session->userdata('lecture');
        $this->qid = $this->session->userdata('question');
        $this->sid = $this->session->userdata('session');
        $this->type = $this->session->userdata('type');
    }
    
    /*
     	Sets the user's session vars for this quiz
    	@return null;
     */
    function setSession()
    {
    	$sess = array(
	    	'module' => $this->mid,
	    	'lecture' => $this->lid,
	    	'question' => $this->qid,
	    	'session' => $this->sid,
	    	'type' => $this->type
    	);
    	$this->session->set_userdata($sess);
    }
	
	/*
		Checks if the answer to the question is correct. If yes, then update active session.
		@param	answer	the user's answer
		@return string
	*/
	function verifyAnswer($answer)
	{
		// Get the global vars
		$e_mid = $this->db->escape($this->mid);
		$e_lid = $this->db->escape($this->lid);
		$e_qid = $this->db->escape($this->qid);
		$e_sid = $this->db->escape($this->sid);
		$e_uid = $this->db->escape($this->session->userdata('user_id'));
		$type = $this->type;
		
		$query = $this->db->query("SELECT `answer` FROM `questions` WHERE `id` = $e_qid LIMIT 1");
		$query = $query->result_array();
		
		// Correct Answer
		if ($answer == $query[0]['answer'])
			$this->db->query("UPDATE `quizzes_active` SET `correct` = `correct` + 1 WHERE `id` = $e_sid LIMIT 1");
		// Wrong Answer
		else
			$this->db->query("UPDATE `quizzes_active` SET `wrong` = `wrong` + 1 WHERE `id` = $e_sid LIMIT 1");
		
		// Get the session filters and practice var
		$session = $this->db->query("SELECT `practice`, `filters` FROM `quizzes_active` WHERE `id` = $e_sid AND `user` = $e_uid LIMIT 1");
		$session = $session->result_array();
		$json = json_decode($session[0]['filters'], true);
		$exam = $json['exam']; $e_exam = $this->db->escape($exam);
		$rank = $json['rank']; $e_rank = $this->db->escape($rank);
		$keywords = $json['keywords']; $e_key = $this->db->escape($keywords);
		$practice = $session[0]['practice'];
		
		// Get the next question
		$str = "SELECT `q`.`id`, `q`.`module`, `q`.`lecture_no`"; 
		if ($keywords != "")
			$str .= ", ( (1.2 * (MATCH(`q`.`question`) AGAINST ($e_key IN BOOLEAN MODE))) +
			(.65 * (MATCH(`q`.`a1`) AGAINST ($e_key IN BOOLEAN MODE))) +
			(.65 * (MATCH(`q`.`a2`) AGAINST ($e_key IN BOOLEAN MODE))) +
			(.65 * (MATCH(`q`.`a3`) AGAINST ($e_key IN BOOLEAN MODE))) +
			(.65 * (MATCH(`q`.`a4`) AGAINST ($e_key IN BOOLEAN MODE))) +
			(.65 * (MATCH(`q`.`a5`) AGAINST ($e_key IN BOOLEAN MODE))) ) AS `relevance`";
		$str .= " FROM `questions` AS `q` ";
		if ($rank != "")
			$str .= "LEFT JOIN `users_ranks` AS `ur` ON `q`.`id` = `ur`.`q_id`";
		$str .= " WHERE 1 = 1 ";
		if ($exam != "")
			$str .= "AND `q`.`test_no` = $e_exam ";
		if ($rank != "")
			$str .= "AND `ur`.`user` = $e_uid AND `ur`.`rank` = $e_rank ";
			
		if ($keywords != "")
			$str .= "AND ((MATCH(`q`.`question`, `q`.`a1`, `q`.`a2`, `q`.`a3`, `q`.`a4`, `q`.`a5`) AGAINST ($e_key IN BOOLEAN MODE)) > 0)";
		if ($type != "course")
			$str .= " AND `q`.`lecture_no` = $e_lid AND `q`.`id` > $e_qid";
		else
			$str .= " AND `q`.`id` > $e_qid";
			
		if ($keywords != "")
			$str .= " ORDER BY `relevance` LIMIT 1";
		else
		{
			if ($type != "course" || ($type == "course" && ($rank != "" || $exam != "")))
				$str .= " ORDER BY `q`.`id` LIMIT 1";
			else
				$str .= " ORDER BY RAND() LIMIT 1";
		}
		
		$next_q = $this->db->query($str);
		$next_q = $next_q->result_array();
		
		// There is a next question for this lecture
		if (!empty($next_q))
		{
			// Update the session
			$this->qid = $next_q[0]['id'];
			$this->db->query("UPDATE `quizzes_active` SET `question_no` = $this->qid WHERE `id` = $e_sid LIMIT 1");
		}
		// There are no more questions in this lecture
		else if (empty($next_q) && $practice == 1)
		{
			if ($type == "lecture" || $type == "course")
				$this->destroySession(0);
			// Module
			else if ($type == "module")
			{
				// Build the query
				$str = "SELECT `q`.`id`, `q`.`module`, `q`.`lecture_no`"; 
				if ($keywords != "")
					$str .= ", ( (1.2 * (MATCH(`q`.`question`) AGAINST ($e_key IN BOOLEAN MODE))) +
					(.65 * (MATCH(`q`.`a1`) AGAINST ($e_key IN BOOLEAN MODE))) +
					(.65 * (MATCH(`q`.`a2`) AGAINST ($e_key IN BOOLEAN MODE))) +
					(.65 * (MATCH(`q`.`a3`) AGAINST ($e_key IN BOOLEAN MODE))) +
					(.65 * (MATCH(`q`.`a4`) AGAINST ($e_key IN BOOLEAN MODE))) +
					(.65 * (MATCH(`q`.`a5`) AGAINST ($e_key IN BOOLEAN MODE))) ) AS `relevance`";
				$str .= " FROM `questions` AS `q` ";
				if ($rank != "")
					$str .= "LEFT JOIN `users_ranks` AS `ur` ON `q`.`id` = `ur`.`q_id`";
				$str .= " WHERE 1 = 1 ";
				if ($exam != "")
					$str .= "AND `q`.`test_no` = $e_exam ";
				if ($rank != "")
					$str .= "AND `ur`.`user` = $e_uid AND `ur`.`rank` = $e_rank ";	
				$str .= "AND `q`.`module` = $e_mid AND `q`.`lecture_no` > $e_lid "; 
				if ($keywords != "")
					$str .= "AND ((MATCH(`q`.`question`, `q`.`a1`, `q`.`a2`, `q`.`a3`, `q`.`a4`, `q`.`a5`) AGAINST ($e_key IN BOOLEAN MODE)) > 0) ORDER BY `relevance` LIMIT 1";
				else
					$str .= "ORDER BY `q`.`lecture_no`, `q`.`id` LIMIT 1";
				
				$next_q = $this->db->query($str);
				$next_q = $next_q->result_array();
				
				// Check if there are more lectures
				if (!empty($next_q))
				{
					$this->mid = $next_q[0]['module'];
					$this->lid = $next_q[0]['lecture_no'];
					$this->qid = $next_q[0]['id'];
					// Update the session
					$this->db->query("UPDATE `quizzes_active` SET `lecture` = $this->lid, `question_no` = $this->qid WHERE `user` = $e_uid AND `id` = $e_sid LIMIT 1");	
				}
				else
					$this->destroySession(0);
			}
		}
		// Save the quiz to the scorecard
		else if (empty($next_q) && $practice == 0)
			$this->destroySession(1);
		
		$this->setSession();
		return $query[0]['answer'];
	}
	
	/*
		Gets the next question
		@return array
	*/
	function nextQuestion()
	{
		$e_qid = $this->db->escape($this->qid);
		
		$quest = $this->db->query("SELECT `id`, `question`, `a1`, `a2`, `a3`, `a4`, `a5`, `module`, `lecture_no` FROM `questions` WHERE `id` = $e_qid LIMIT 1");
		$quest = $quest->result_array();

		if (!empty($quest))
		{
			$result = array();
			$result['question'] = $quest[0];
			$result['question']['rank'] = $this->getQuestionRank();
			
			return $result;
		}
		else
			return false;
	}
	
	/*
		Returns the user's difficulty rank of the question
		@return	string
	*/
	function getQuestionRank()
	{
		$user = $this->db->escape($this->session->userdata('user_id'));
		$qid = $this->db->escape($this->qid);
		
		$rank = $this->db->query("SELECT * FROM `users_ranks` WHERE `user` = $user AND `q_id` = $qid LIMIT 1");
		$rank = $rank->result_array();
		
		if (!empty($rank))
			return $rank[0]['rank'];
		else
			return "0";
	}
	
	/*
		Gets the exam dates and difficulty rankings
		@return	array
	*/
	function getFilters()
	{
		$type = $this->type;
		$result = array();
		$e_lid = $this->db->escape($this->lid);
		$e_mid = $this->db->escape($this->mid);
		$e_uid = $this->db->escape($this->session->userdata('user_id'));

		// Build queries
		$str = "SELECT `test_no` FROM `questions` WHERE 1=1";
		if ($type == "lecture")
			$str .= " AND `lecture_no` = $e_lid";
		else if ($type == "module")
			$str .= " AND `module` = $e_mid";
		$str .= " GROUP BY `test_no` ORDER BY `test_no` DESC";
		
		$q = $this->db->query($str);
		$result['exams'] = $q->result_array();
		
		$str = "SELECT `r`.`id`, `r`.`name` FROM `questions` AS `q`
			LEFT JOIN `users_ranks` AS `ur` ON `q`.`id` = `ur`.`q_id`
			LEFT JOIN `ranks` AS `r` ON `r`.`id` = `ur`.`rank`
			WHERE `ur`.`user` = $e_uid";
		if ($type == "lecture")
			$str .= " AND `q`.`lecture_no` = $e_lid";
		else if ($type == "module")
			$str .= " AND `q`.`module` = $e_mid";
		$str .= " GROUP BY `r`.`id` ORDER BY `r`.`id`";
		
		$q = $this->db->query($str);
		$result['ranks'] = $q->result_array();
		
		return $result;
	}
	
	/*
		Sets the filters for this quiz
		@param	exam	the exam date filter
		@param	rank	the difficulty id
		@param	keywords	the search keywords
		@return	array
	*/
	function setFilters($exam, $rank, $keywords)
	{
		// Get the global vars
		$type = $this->type;
		$e_lid = $this->db->escape($this->lid);
		$e_mid = $this->db->escape($this->mid);
		$e_sid = $this->db->escape($this->sid);
		$user = $this->session->userdata('user_id');
		$e_user = $this->db->escape($user);
		
		// Build the array and encode it
		$e_rank = $this->db->escape($rank);
		$e_exam = $this->db->escape($exam);
		$key = str_replace(",","",$keywords);
		$e_key = $this->db->escape($key);
		$json = array('exam' => $exam, 'rank' => $rank, 'keywords' => $keywords);
		$json = $this->db->escape(json_encode($json));
		
		// Find the new question that matches the filters
		$str = "SELECT `q`.`id` AS `q_id`, `q`.`module`, `q`.`lecture_no`"; 
		if ($keywords != "")
			$str .= ", ( (1.2 * (MATCH(`q`.`question`) AGAINST ($e_key IN BOOLEAN MODE))) +
			(.65 * (MATCH(`q`.`a1`) AGAINST ($e_key IN BOOLEAN MODE))) +
			(.65 * (MATCH(`q`.`a2`) AGAINST ($e_key IN BOOLEAN MODE))) +
			(.65 * (MATCH(`q`.`a3`) AGAINST ($e_key IN BOOLEAN MODE))) +
			(.65 * (MATCH(`q`.`a4`) AGAINST ($e_key IN BOOLEAN MODE))) +
			(.65 * (MATCH(`q`.`a5`) AGAINST ($e_key IN BOOLEAN MODE))) ) AS `relevance`";
		$str .= " FROM `questions` AS `q` ";
		if ($rank != "")
			$str .= "LEFT JOIN `users_ranks` AS `ur` ON `q`.`id` = `ur`.`q_id`";
		$str .= " WHERE 1 = 1 ";
		if ($exam != "")
			$str .= "AND `q`.`test_no` = $e_exam ";
		if ($rank != "")
			$str .= "AND `ur`.`user` = $e_user AND `ur`.`rank` = $e_rank ";
			
		if ($keywords != "")
			$str .= "AND ((MATCH(`q`.`question`, `q`.`a1`, `q`.`a2`, `q`.`a3`, `q`.`a4`, `q`.`a5`) AGAINST ($e_key IN BOOLEAN MODE)) > 0)";
		if ($type == "lecture")
			$str .= " AND `q`.`lecture_no` = $e_lid";
		else if ($type == "module")
			$str .= " AND `q`.`module` = $e_mid";
			
		if ($keywords != "")
			$str .= " ORDER BY `relevance` LIMIT 1";
		else
		{
			if ($type != "course" || ($type == "course" && ($rank != "" || $exam != "")))
				$str .= " ORDER BY `q`.`id` LIMIT 1";
			else
				$str .= " ORDER BY RAND() LIMIT 1";
		}

		$query = $this->db->query($str);
		$query = $query->result_array();
		
		// Modify the existing session
		if (!empty($query))
		{
			$query = $query[0];
			$this->db->query("UPDATE `quizzes_active` SET `started` = ".time().", `filters` = ".$json.", `wrong` = 0, `correct` = 0, 
			`module` = ".$query['module'].", `lecture` = ".$query['lecture_no'].", `question_no` = ".$query['q_id']." 
			WHERE `id` = $e_sid AND `user` = $e_user LIMIT 1");
		}
		else
			$this->db->query("UPDATE `quizzes_active` SET `filters` = ".$json.", `wrong` = 0, `correct` = 0, `started` = ".time().", `module` = 0, `lecture` = 0, `question_no` = 0 WHERE `id` = $e_sid AND `user` = $e_user LIMIT 1");
	}
	
	/*
		Returns all lectures
		@return	array
	*/
	function getLectures()
	{
		$query = $this->db->query("SELECT * FROM `lectures` ORDER BY `lecture`");
		$query = $query->result_array();
		return $query;
	}
	
	/*
		Returns all modules
		@return	array
	*/
	function getModules()
	{
		$query = $this->db->query("SELECT `module` FROM `lectures` GROUP BY `module` ORDER BY `module`");
		$query = $query->result_array();
		return $query;
	}
	
	/*
		Returns all difficulty rankings
		@return	array
	*/
	function getRanks()
	{
		$query = $this->db->query("SELECT * FROM `ranks` ORDER BY `id`");
		return $query->result_array();
	}
	
	/*
		Returns a lecture's first question and starts the new session
		@param	lid	lecture id
		@return	array on success
	*/
	function startLecture($lid)
	{
		$e_lid = $this->db->escape($lid);
		
		// Find the corresponding module
		$query = $this->db->query("SELECT * FROM `lectures` WHERE `lecture` = $e_lid LIMIT 1");
		$query = $query->result_array();
		if (!empty($query))
		{
			$result = array();
			$result['info'] = $query[0];
			
			// Destroy an existing session
			$this->destroySession(0);
			
			// Get the first question
			$question = $this->db->query("SELECT * FROM `questions` WHERE `lecture_no` = $e_lid ORDER BY `id` LIMIT 1");
			$question = $question->result_array();
			$result['question'] = $question[0];
			$this->qid = $question[0]['id'];
			
			// Get the rank for this question
			$result['rank'] = $this->getQuestionRank();
			
			// Start a new practice session
			$this->type = 'lecture';
			$this->lid = $lid;
			$this->mid = $question[0]['module'];
			$result['session'] = $this->startSession(1);
			
			$this->setSession();
			return $result;
		}
		else
			return false;
	}
	
	/*
		Starts a new quiz
		@param	lid	lecture id
		@return	array
	*/
	function startQuiz($lid)
	{
		$e_lid = $this->db->escape($lid);
		
		// Find the corresponding module
		$query = $this->db->query("SELECT * FROM `lectures` WHERE `lecture` = $e_lid LIMIT 1");
		$query = $query->result_array();
		if (!empty($query))
		{
			$result = array();
			$result['info'] = $query[0];
			
			// Destroy an existing session and save it to the scorecard
			$this->destroySession(1);
			
			// Get the first question
			$question = $this->db->query("SELECT * FROM `questions` WHERE `lecture_no` = $e_lid ORDER BY `id` LIMIT 1");
			$question = $question->result_array();
			$result['question'] = $question[0];
			$this->qid = $question[0]['id'];
			
			// Start a new practice session
			$this->type = 'lecture';
			$this->lid = $lid;
			$this->mid = $question[0]['module'];
			$result['session'] = $this->startSession(0);
			
			$this->setSession();
			return $result;
		}
		else
			return false;
	}
	
	/*
		Returns the first question of the first lecture of the module
		@param	mid	module id
		@return	array on success
	*/
	function startModule($mid)
	{
		$e_mid = $this->db->escape($mid);
		$query = $this->db->query("SELECT * FROM `questions` WHERE `module` = $e_mid ORDER BY `lecture_no`, `id` LIMIT 1");
		$query = $query->result_array();
		
		if (!empty($query))
		{
			$result = array();
			$result['question'] = $query[0];
			$this->qid = $query[0]['id'];
			
			// Destroy the existing session
			$this->destroySession(0);
			
			// Get the rank for this question
			$result['rank'] = $this->getQuestionRank();
			
			// Start a new practice session
			$this->lid = $query[0]['lecture_no'];
			$this->mid = $mid;
			$this->type = 'module';
			$result['session'] = $this->startSession(1);
			
			$this->setSession();
			return $result;
		}
		else
			return false;
	}
	
	/*
		Returns a random question for the course 
		@return	array on success
	*/
	function startCourse()
	{
		$query = $this->db->query("SELECT * FROM `questions` ORDER BY RAND() LIMIT 1");
		$query = $query->result_array();
		
		if (!empty($query))
		{
			$result = array();
			$result['question'] = $query[0];
			$this->qid = $query[0]['id'];
			
			// Destroy the existing session
			$this->destroySession(0);
			
			// Get the rank for this question
			$result['rank'] = $this->getQuestionRank();
			
			// Start a new practice session
			$this->type = 'course';
			$this->mid = $query[0]['module'];
			$this->lid = $query[0]['lecture_no'];
			$result['session'] = $this->startSession(1);
			
			$this->setSession();
			return $result;
		}
		else
			return false;
	}
	
	/*
		Gets the total number of questions in a given module or lecture
		@param	filters	the user defined filters
		@return	int
	*/
	function getTotal($filters = array('rank' => "", 'exam' => "", 'keywords' => ""))
	{
		// Get the global vars
		$type = $this->type;
		$e_lid = $this->db->escape($this->lid);
		$e_mid = $this->db->escape($this->mid);

		// Get the session's filters
		$filters = json_decode($filters, true);
		$e_user = $this->db->escape($this->session->userdata('user_id'));
		$e_exam = $this->db->escape($filters['exam']);
		$e_rank = $this->db->escape($filters['rank']);
		$e_key = $this->db->escape($filters['keywords']);
	
		$str = "SELECT COUNT(`q`.`id`) AS `total`"; 
		if ($filters['keywords'] != "")
			$str .= ", ( (1.2 * (MATCH(`q`.`question`) AGAINST ($e_key IN BOOLEAN MODE))) +
			(.65 * (MATCH(`q`.`a1`) AGAINST ($e_key IN BOOLEAN MODE))) +
			(.65 * (MATCH(`q`.`a2`) AGAINST ($e_key IN BOOLEAN MODE))) +
			(.65 * (MATCH(`q`.`a3`) AGAINST ($e_key IN BOOLEAN MODE))) +
			(.65 * (MATCH(`q`.`a4`) AGAINST ($e_key IN BOOLEAN MODE))) +
			(.65 * (MATCH(`q`.`a5`) AGAINST ($e_key IN BOOLEAN MODE))) ) AS `relevance`";
		$str .= " FROM `questions` AS `q` ";
		if ($filters['rank'] != "")
			$str .= "LEFT JOIN `users_ranks` AS `ur` ON `q`.`id` = `ur`.`q_id`";
		$str .= " WHERE 1 = 1 ";
		if ($filters['exam'] != "")
			$str .= "AND `q`.`test_no` = $e_exam ";
		if ($filters['rank'] != "")
			$str .= "AND `ur`.`user` = $e_user AND `ur`.`rank` = $e_rank ";
			
		if ($filters['keywords'] != "")
			$str .= "AND ((MATCH(`q`.`question`, `q`.`a1`, `q`.`a2`, `q`.`a3`, `q`.`a4`, `q`.`a5`) AGAINST ($e_key IN BOOLEAN MODE)) > 0)";
		if ($type == "lecture")
			$str .= " AND `q`.`lecture_no` = $e_lid";
		else if ($type == "module")
			$str .= " AND `q`.`module` = $e_mid";
		
		$query = $this->db->query($str);
		$query = $query->result_array();
		return $query[0]['total'];
	}
	
	/*
		Changes the rank of a question
		@param	rank	the new rank
	*/
	function changeRank($rank)
	{
		$qid = $this->qid;
		$e_qid = $this->db->escape($qid);
		$e_rank = $this->db->escape($rank);
		$user = $this->session->userdata('user_id');
		$e_user = $this->db->escape($user);
		
		$query = $this->db->query("SELECT * FROM `users_ranks` WHERE `q_id` = $e_qid AND `user` = $e_user LIMIT 1");
		$query = $query->result_array();
		
		if (!empty($query))
			$this->db->query("UPDATE `users_ranks` SET `rank` = $e_rank WHERE `q_id` = $e_qid AND `user` = $e_user LIMIT 1");
		else
		{
			$data = array('q_id' => $qid, 'user' => $user, 'rank' => $rank);
			$str = $this->db->insert_string('users_ranks', $data);
			$this->db->query($str);
		}
	}
	
	/*
		Starts a new quiz session and returns the session id
		@param	practice	1 if true, 0 if false
		@return	array on success
	*/
	function startSession($practice)
	{
		// Set the global vars
		$module = $this->mid;
		$lecture = $this->lid;
		$question = $this->qid;
		$type = $this->type;
		
		$uid = $this->session->userdata('user_id');
		
		$data = array('user' => $uid, 'question_no' => $question, 'module' => $module, 'lecture' => $lecture, 'type' => $type, 'correct' => 0, 'wrong' => 0, 'practice' => $practice, 'started' => time());
		$str = $this->db->insert_string('quizzes_active', $data);
		
		if ($this->db->query($str))
		{
			$e_lid = $this->db->escape($lecture);
			$e_mod = $this->db->escape($module);
			
			// Get the new session id
			$query = $this->db->query("SELECT * FROM `quizzes_active` WHERE `user` = $uid AND `module` = $e_mod AND `lecture` = $e_lid AND `question_no` = $question ORDER BY `id` DESC LIMIT 1");
			$query = $query->result_array();
			if (!empty($query))
			{
				// Set the global session var
				$this->sid = $query[0]['id'];
				
				$result = array();
				$result['session'] = $query[0];

				// Get the total number of questions
				$result['session']['total'] = $this->getTotal($query[0]['filters']);
				
				return $result['session'];
			}
			else
				return false;
		}
		else
			return false;
	}
	
	/*
		Returns a previously saved session
		@param	module	module number
		@param	lecture	lecture number
		@param	type	quiz type
		@return	array
	*/
	function getSession($module, $lecture, $type)
	{
		$uid = $this->session->userdata('user_id');
		$e_mid = $this->db->escape($module);
		$e_lid = $this->db->escape($lecture);
		
		$session = "";
		if ($type == "course") // Course
			$session = $this->db->query("SELECT * FROM `quizzes_active` WHERE `user` = $uid AND `type` = 'course' LIMIT 1");
		else if ($type == "lecture") // Lecture
			$session = $this->db->query("SELECT * FROM `quizzes_active` WHERE `user` = $uid AND `lecture` = $e_lid AND `type` = 'lecture' LIMIT 1");
		else if ($type == "module") // Module
			$session = $this->db->query("SELECT * FROM `quizzes_active` WHERE `user` = $uid AND `module` = $e_mid AND `type` = 'module' LIMIT 1");
		$session = $session->result_array();
		
		if (!empty($session))
		{
			// Set the global vars
			$this->mid = $module;
			$this->lid = $lecture;
			$this->type = $type;
			$this->sid = $session[0]['id'];
			
			$result = array();
			$result['session'] = $session[0];
			
			// Get the total number of questions
			$result['session']['total'] = $this->getTotal($session[0]['filters']);
			
			// Get the current question
			$query = $this->db->query("SELECT * FROM `questions` WHERE `id` = ".$this->db->escape($session[0]['question_no'])." LIMIT 1");
			$query = $query->result_array();
			
			if (!empty($query))
			{
				// Set the global question var
				$this->qid = $query[0]['id'];
				
				$result['question'] = $query[0];
				
				// Get the rank for the current question
				$result['rank'] = $this->getQuestionRank();
			}
			else
			{
				$result['question'] = "";
				$result['rank'] = 0;
			}
			
			// Get the info for the lecture
			if ($type == "lecture")
			{
				$query = $this->db->query("SELECT `name` FROM `lectures` WHERE `lecture` = $e_lid LIMIT 1");
				$query = $query->result_array();
				$result['info'] = $query[0];
			}
			else
				$result['info'] = "";
			
			$this->setSession();
			return $result;
		}
		else
			return false;
	}
	
	/*
		Destroys an active quiz session
		@param	save	1 to save (quiz), 0 to delete
		@return	boolean
	*/
	function destroySession($save = 0)
	{	
		// Get the global vars
		$module = $this->mid;
		$lecture = $this->lid;
		$type = $this->type;
		$sid = $this->sid;
		
		$uid = $this->session->userdata('user_id');
		$e_uid = $this->db->escape($uid);
		$e_mid = $this->db->escape($module);
		$e_lid = $this->db->escape($lecture);
		$e_sid = $this->db->escape($sid);
		
		// Save the session to quizzes
		if ($save == 1)
		{
			// Get the session
			$session = $this->db->query("SELECT * FROM `quizzes_active` WHERE `id` = $e_sid LIMIT 1");
			$session = $session->result_array();
			if (!empty($session))
			{
				$session = $session[0];
				
				// Get the total number of problems for this quiz
				$total = $this->getTotal($session['filters']);
				
				$wrong = $session['wrong'];
				if ($wrong + $session['correct'] < $total)
					$wrong = $total - $session['correct'];
				
				$data = array('user' => $uid, 'module' => $module, 'lecture' => $lecture, 'type' => $type, 'correct' => $session['correct'], 'wrong' => $wrong, 'started' => $session['started'], 'completed' => time());
				$str = $this->db->insert_string('quizzes_taken', $data);
				$this->db->query($str);
			}
		}
		// Remove the active session
		$this->db->query("DELETE FROM `quizzes_active` WHERE `id` = $e_sid AND `user` = $e_uid LIMIT 1");
			
		return true;
	}
	
}

/* End of file anatomy_model.php */
/* Location: ./system/application/models/anatomy_model.php */

?>