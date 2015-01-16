<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Export_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }


    public function regional_appointments($options) {
		
       $qry = "select urn,begin_date,set_by,IF(attendee is null,'No CAE',attendee) attendee,coname, date_format(renewals.date,'%d/%m/%Y') as renewal,u.rep_group,rcm,rm,score,cancelled from events e left join leads using(urn) left join renewals using(urn) left join (select event_id,attendee from events_attendees left join events on events.id=events_attendees.event_id where attendee<>set_by) ea on e.id = ea.event_id left join users u on ea.attendee = login left join events_feedback ef on e.id = ef.id left join rcm_list on u.rep_group =  region where set_by is not null and set_by <> '' and `status` = 'Live' ";
	   
	   if(isset($options['from'])&&!empty($options['from'])){
		 $qry .= " and begin_date >= '".$options['from']."' ";  
	   }
	      if(isset($options['to'])&&!empty($options['to'])){
		 $qry .= " and begin_date <= '".$options['to']."' ";  
	   }
	        if(isset($options['bde'])&&!empty($options['bde'])){
		 $qry .= " and set_by = '".$options['bde']."' ";  
	   }
	   
	    $qry .= "group by e.id";
		$this->firephp->log($qry);
	   $result=  $this->db->query($qry)->result_array();

		return $result;
    }
	
	    public function renewals_export($options) {
		
       $qry = "select ro.prospector as bde,rep_group as region,renewals.`date`,coname,concat(title,' ',firstname,' ',lastname) contact, p_add1,p_add2,p_add3,p_town,p_county,p_postcode from leads left join renewals using(urn) left join contacts using(urn) left join renewal_ownership ro on renewals.id = renewal_id where ro.prospector is not null";
	   
	   if(isset($options['from'])&&!empty($options['from'])){
		 $qry .= " and renewals.`date` >= '".$options['from']."' ";  
	   }
	      if(isset($options['to'])&&!empty($options['to'])){
		 $qry .= " and renewals.`date` <= '".$options['to']."' ";  
	   }
	        if(isset($options['bde'])&&!empty($options['bde'])){
		 //$qry .= " and set_by = '".$options['bde']."' ";  
	   }
$qry .= " group by renewals.id order by renewals.`date` asc ";
		$this->firephp->log($qry);
	   $result=  $this->db->query($qry)->result_array();

		return $result;
    }
	
	 public function renewals_noadd_export($options) {
		
       $qry = "select if(p_add1='' or p_postcode = '','Incomplete address','No Contact') as reason,renewals.urn,ro.prospector as bde,rep_group as region,renewals.`date`,coname,concat(title,' ',firstname,' ',lastname) contact, p_add1,p_add2,p_add3,p_town,p_county,p_postcode from leads left join renewals using(urn) left join contacts using(urn) left join renewal_ownership ro on renewals.id = renewal_id where ro.prospector is not null and ((p_add1 = '' or p_postcode = '') or (contacts.id is null))";
	   
	   if(isset($options['from'])&&!empty($options['from'])){
		 $qry .= " and renewals.`date` >= '".$options['from']."' ";  
	   }
	      if(isset($options['to'])&&!empty($options['to'])){
		 $qry .= " and renewals.`date` <= '".$options['to']."' ";  
	   }
	        if(isset($options['bde'])&&!empty($options['bde'])){
		 //$qry .= " and set_by = '".$options['bde']."' ";  
	   }
$qry .= " group by renewals.id order by renewals.`date` asc ";

	   $result=  $this->db->query($qry)->result_array();

		return $result;
    }
	
		 public function renewals_notel_export($options) {
		
       $qry = "select ro.prospector,urn,coname,type,`date` renewal_date from contacts left join renewals using(urn) left join renewal_ownership ro on ro.renewal_id = renewals.id left join leads using(urn) where urn in(select urn from contacts where telephone = '' and mobile = '' and work = '') and urn not in(select urn from contacts where telephone <> '' or mobile <> '' or work <> '') and ro.prospector is not null group by urn ";
	   
	   if(isset($options['from'])&&!empty($options['from'])){
		 $qry .= " and renewals.`date` >= '".$options['from']."' ";  
	   }
	      if(isset($options['to'])&&!empty($options['to'])){
		 $qry .= " and renewals.`date` <= '".$options['to']."' ";  
	   }
	        if(isset($options['bde'])&&!empty($options['bde'])){
		 //$qry .= " and set_by = '".$options['bde']."' ";  
	   }


	   $result=  $this->db->query($qry)->result_array();

		return $result;
    }
	
	
}