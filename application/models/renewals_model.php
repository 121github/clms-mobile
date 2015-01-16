<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Renewals_model extends CI_Model {
    
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
    public function get_renewals($options)
    {
		
		
		$qry = "select urn,coname,date_format(r.date,'%d/%m/%Y') date,ro.prospector,date_format(ro.date_added,'%d/%m/%Y') date_added,r.type,r.broker,r.insurer,r.premium from renewals r left join renewal_ownership ro on id=renewal_id left join leads l using(urn) where ro.prospector is not null ";
		
		if(!empty($options['month'])){ 
		$qry .= " and month(r.date)=".intval($options['month']); 
		}
		if(!empty($options['prospector'])){ 
		$qry .= " and ro.prospector=".$this->db->escape($options['prospector']); 
		}
		if(!empty($options['date_from'])){ 
		$qry .= " and date(ro.date_added) between '".$options['date_from']."' and '".$options['date_to']."'"; 
		}
		if(!empty($options['type'])){ 
		$qry .= " and r.type=".$this->db->escape($options['type']); 
		}
		
		return $this->db->query($qry)->result_array();
		
	}
	
}