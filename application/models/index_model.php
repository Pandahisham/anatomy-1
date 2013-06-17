<?php
class Index_model extends CI_Model {
 
    function Index_model()
    {
        parent::__construct();
    }

	function getClassYears()
	{
		$query = $this->db->query("SELECT * FROM `classes` ORDER BY `year`");
		$query = $query->result_array();
		if (!empty($query))
			return $query;
		else
			return false;
	}
	
	function getScores()
	{
		$user = $this->session->userdata('user_id');
		$e_uid = $this->db->escape($user);
		
		$qt = $this->db->query("SELECT `qt`.`user`, `l`.`name`, `qt`.`lecture`, `qt`.`module`, `qt`.`correct`, `qt`.`wrong`, `qt`.`started`, `qt`.`completed` FROM `quizzes_taken` AS `qt`
		LEFT JOIN `lectures` AS `l` ON `l`.`lecture` = `qt`.`lecture`
		WHERE `user` = $e_uid AND `qt`.`type` = 'lecture' ORDER BY `qt`.`lecture`");
		$lectures = $qt->result_array();

		if (!empty($lectures))
		{
			$result = array();
			$i = 0;
			$lec = 0;
			foreach ($lectures as $l)
			{
				$lec = $l['lecture'];
				// Create a new element if this lecture id is not present
				if (!array_key_exists($l['lecture'], $result))
				{
					$result[$l['lecture']] = array('name' => $l['name'], 'me' => '', 'class' => '');
			
					// Compute class average
					$class = $this->db->query("SELECT `lecture`, `correct`, `wrong` FROM `quizzes_taken`
					WHERE `lecture` = ". $l['lecture'] ." AND `type` = 'lecture' AND `user` != $e_uid");
					$class = $class->result_array();
					
					if (!empty($class))
					{
						$correct = 0;
						$total = 0;
						foreach ($class as $c)
						{
							$correct += $c['correct'];
							$total += $c['correct'] + $c['wrong'];
						}
						$result[$l['lecture']]['class'] = ceil(($correct / $total) * 100);
					}
					unset($class);
					
					// Compute my average
					$me = array();
					$count = 0;
					$correct = 0;
					$lowest = 0;
					$highest = 0;
					$best_time = time();
					while ($i < sizeof($lectures) && $lectures[$i]['lecture'] == $lec)
					{
						$temp = $lectures[$i];
						if ($temp['correct'] > $highest)
							$highest = $temp['correct'];
						if ($temp['wrong'] > $lowest)
							$lowest = $temp['wrong'];
						if (($temp['completed'] - $temp['started']) < $best_time)
							$best_time = $temp['completed'] - $temp['started'];
						$correct += $temp['correct'];
						$count++;
						$i++;
					}
					$total = $l['wrong'] + $l['correct'];
					$result[$l['lecture']]['me'] = array('lowest' => ceil((($total - $lowest) / $total) * 100), 
						'highest' => ceil(($highest / $total) * 100),
						'average' => ceil(($correct / ($total * $count)) * 100),
						'time' => $best_time);
				}
			}
			//var_export($result);die();
			return $result;
		}
		else
			return array();
	}
	
	function getEmails()
	{
		$q = $this->db->query("SELECT `email` FROM `users`");
		$q = $q->result_array();
		if (!empty($q))
			return $q;
		else
			return array();
	}
	
}

/* End of file index_model.php */
/* Location: ./system/application/models/index_model.php */

?>