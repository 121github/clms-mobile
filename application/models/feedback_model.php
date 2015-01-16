<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Feedback_model extends CI_Model {

  function __construct() {
        parent::__construct();
    }
    
    public function get_feedback($options) {
       /*the base query*/
      $qry="select urn,coname,id,begin_date,begin_hour,title,text,end_hour,begin_mins,end_mins,set_by,bde,cae,date_format(events_feedback.date_added,'%d/%m%/%y') as feedback_date,score,date_format(events.begin_date,'%d/%m%/%y') as app_date from events_feedback left join events using(id) left join leads using(urn) where complete = 1 ";
      /*add the filter options if applicable*/
      if(!empty($options['prospector'])){ 
        $qry .= " and bde = '".$options['prospector']."'"; 
        }  
      if(!empty($options['date_from'])){ 
        $qry .= " and events.begin_date between '" . $options['date_from'] . "' and '".$options['date_to']."'"; 
        }
        $qry .= " order by events_feedback.id desc ";
        $result = $this->db->query($qry)->result_array();
       /* run the complete query*/ 
        return $result; 
    }
    
    
     public function get_feedback_answers($id) {
       /*the base query*/
      $qry="select urn,coname,question,value as score,reason,bde,cae,score as total,date_format(events_feedback.date_added,'%d/%m%/%y') as feedback_date from events_feedback left join events_feedback_answers using(id) left join events using(id) left join leads using(urn) where complete = 1 and id = '$id'";
 $_SESSION['feedback'] = $qry;
        $answers = $this->db->query($qry)->result_array();
       
       /* run the complete query*/ 
        return $answers; 
    }
}

?>
