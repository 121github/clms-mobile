<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model {
    
    function __construct() {
        parent::__construct();
    }
    
    /**
     * Check the username & password provied and that they are an active user.
     * If validation is successful, set the session variables.
     *  
     * @param string $username
     * @param string $password
     * @return boolean true if validation is successful.
     */
    public function validate_login($username, $password)
    {
        $qry    = "SELECT *, DATE_FORMAT(lastlog,'%D %M %Y') AS logdate, 
                   DATE_FORMAT(lastlog,'%T') AS logtime 
                   FROM users WHERE login = ? 
                   AND pass = ? AND active = 1 ";
        $result = $this->db->query($qry, array($username, $password))->result_array();
        if (!empty($result)) 
        {
            $result                = $result[0];
            $_SESSION['login']     = $result['login'];
            $_SESSION['user']      = $result['user'];
            $_SESSION['marketing'] = $result['marketing'];
            $_SESSION['type']      = $result['type'];
            $_SESSION['rep_group'] = $result['rep_group'];
            $_SESSION['logdate']   = $result['logdate'];
            $_SESSION['logtime']   = $result['logtime'];

            if ($result['type'] == 'M') 
            {
                $_SESSION['rep_array'] = $this->db->get_where('users_regions', array('login' => $username))->result_array();
            }

            return true;
        }
        return false;
    }

    
    public function set_password($password){
      $password = md5($password);
      $this->db->where("login",$_SESSION['login']);
      $this->db->update("users", array("pass" => $password));
    }
}