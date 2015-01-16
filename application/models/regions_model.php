<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Regions_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
    
    public function all() {
      $qry = "select * from leads where lead_status = 'Live' and prospector is not null limit 50"; 
            return $this->db->query($qry)->result_array();
    }
    public function filter($region) {
        $qry = "select * from leads where lead_status = 'Live' and (rep_group like '$region' or p_add3='$region' or p_county = '$region') and prospector is not null limit 50";
            return $this->db->query($qry)->result_array();
      
    }
}
?>