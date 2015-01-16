<?php

class Create_broker_table_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }
    
    public function create(){
//create the insurance table
$create = "CREATE TABLE IF NOT EXISTS `renewal_ownership` (
  `renewal_id` int(11) NOT NULL,
  `prospector` varchar(50) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`renewal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1" ;

$this->db->query($create);
    }
    
    public function populate(){
$populate = "replace into `renewal_ownership` (select renewals.id,user,contact from renewals left join history using(urn) where history.`status` = 'Policy Added' and urn like 'PRO%' group by renewals.id)";


if ($this->db->query($populate)):
$msg  = "renewal ownership table was created";
else:
$msg = "There was an error creating the renewal ownership table";
endif;

return array("action"=>"populate renewal ownership table","msg"=>$msg);
    }
    
}
?>