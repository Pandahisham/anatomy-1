<?php
class Account_model extends CI_Model {
 
    function Account_model()
    {
        parent::__construct();
    }
	
	function getUser($col, $val)
	{
		// Returns user row from DB based on $col (unique)
		$val = $this->db->escape($val);
		$query = $this->db->query("SELECT * FROM `users` WHERE `$col` = $val LIMIT 1");
		$query = $query->result_array();
		if (!empty($query))
			return $query[0];
		else
			return false;
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
	
	function createUser($email, $pw, $class, $key)
	{
		// Insert a new user into the DB
		$pw = $this->pbkdf2($pw);
		$data = array('email' => $email, 'password' => $pw, 'class' => $class, 'active' => '0', 'role' => 1, 'created' => time());
		$str = $this->db->insert_string('users', $data);
		if ($this->db->query($str)) {
			// Get this user's new user id based on email
			$uid = $this->getUser('email', $email);
			// Add a request until activated
			$this->addRequest($uid['id'], 'active', '1', $key);
			return true;
		}
		else
			return false;
	}
	
	function login($email, $pass)
	{
		// Log a user in if they exist
		$pass = $this->pbkdf2($pass);
		$sql = "SELECT `id`, `class`, `active`, `role` FROM `users` WHERE `email` = ? AND `password` = ? LIMIT 1";
		$query = $this->db->query($sql, array($email, $pass));	
		if ($query->num_rows() > 0) {
			$query = $query->result_array();
			return $query[0];
		}
		else
			return false;
	}
	
	 /** PBKDF2 Implementation (described in RFC 2898)
     *
     *  @param string p password
     *  @param string s salt
     *  @param int c iteration count (use 1000 or higher)
     *  @param int kl derived key length
     *  @param string a hash algorithm
     *
     *  @return string derived key
    */
    function pbkdf2( $p, $s = "J11t25g10L", $c = 1284, $kl = 32, $a = 'sha256' ) {
     
        $hl = strlen(hash($a, null, true)); # Hash length
        $kb = ceil($kl / $hl);              # Key blocks to compute
        $dk = '';                           # Derived key
     
        # Create key
        for ( $block = 1; $block <= $kb; $block ++ ) {
     
            # Initial hash for this block
            $ib = $b = hash_hmac($a, $s . pack('N', $block), $p, true);
     
            # Perform block iterations
            for ( $i = 1; $i < $c; $i ++ )
     
                # XOR each iterate
                $ib ^= ($b = hash_hmac($a, $b, $p, true));
     
            $dk .= $ib; # Append iterated block
        }
     
        # Return derived key of correct length
        return substr($dk, 0, $kl);
    }

	
	function changePassword($pw)
	{
		// Account settings > Change Password
		$sid = $this->session->userdata('user_id');
		$sql = "UPDATE `users` SET `password` = ? WHERE `id` = '$sid' LIMIT 1";
		$this->db->query($sql, $this->pbkdf2($pw));
		return true;
	}
	
	function addRequest($uid, $col, $value, $key)
	{
		// Delete all other requests with the same $col and $user
		$this->db->query("DELETE FROM `users_requests` WHERE `user` = '$uid' AND `col` = '$col'");
		// Insert a new request into users_requests
		$data = array('user' => $uid, 'col' => $col, 'request' => $value, 'key' => $key);
		$str = $this->db->insert_string('users_requests', $data);
		if ($this->db->query($str))
			return true;
		else
			return false;
	}
	
	function doRequest($uid, $key)
	{
		// Perform request that matches $uid and $key in a single row
		$uid = $this->db->escape($uid);
		$key = $this->db->escape($key);
		$data = $this->db->query("SELECT * FROM `users_requests` WHERE `user` = $uid AND `key` = $key LIMIT 1");
		$data = $data->result_array();
		if (!empty($data)) {
			$sql = "UPDATE `users` SET `" . $data[0]['col'] . "` = ? WHERE `id` = $uid LIMIT 1";
			$this->db->query($sql, $data[0]['request']);
			$this->db->query("DELETE FROM `users_requests` WHERE `user` = $uid AND `key` = $key LIMIT 1");
			return $data[0]['col'];
		}
		else
			return false;
	}
	
	function getRequest($uid, $col)
	{
		// Get a row (request) based on user's id and column desired
		$uid = $this->db->escape($uid);
		$col = $this->db->escape($col);
		$query = $this->db->query("SELECT * FROM `users_requests` WHERE `user` = $uid AND `col` = $col LIMIT 1");
		$query = $query->result_array();
		if (!empty($query))
			return $query[0];
		else
			return false;
	}
}

/* End of file account_model.php */
/* Location: ./system/application/models/account_model.php */

?>