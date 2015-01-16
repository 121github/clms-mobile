<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Income_model extends CI_Model {

  function __construct() {
    parent::__construct();
  }

  public function get_income($options) {
	  $query = "select urn, leads.coname as company, clientkey, prospector, last_quote, effective, cae, prodtarget,premium from leads left join acturis_live al on leads.acturis = al.clientkey where leads.acturis is not null and leads.acturis <> '' and acturis is not null and prospector is not null and prospector <> '1' ";
	  
	  if(!empty($options['month'])){
		  $query .= " and year(STR_TO_DATE(effective,'%d/%m/%Y')) = year('" . $options['month'] . "') and month(STR_TO_DATE(effective,'%d/%m/%Y'))=month('" . $options['month'] . "') ";
	  }

	  if(!empty($options['prospector'])){
		  $query .= " and prospector = '" . $options['prospector'] . "'";
		  $query .= " and STR_TO_DATE(last_quote,'%d/%m/%Y') > (select min(contact) from history where user = '" . $options['prospector'] . "' and history.urn = leads.urn)";
	  } else {
		  $query .= " and STR_TO_DATE(last_quote,'%d/%m/%Y') > (select min(contact) from history where user_group = 'Prospects' and history.urn = leads.urn) ";  
	  }
$query .= " order by STR_TO_DATE(effective,'%d/%m/%Y') desc";
		 //$query .= "group by prospector";
	  $result = $this->db->query($query)->result_array();
	  $total = 0;
	  foreach($result as $k => $v){
		$total += $v['premium'];  
		  
	  $last_appontment = "select id,date_format(begin_date,'%d/%m/%Y') as last_app,attendee from events left join events_attendees on id=event_id where `status` = 'Live' and set_by = '".$v['prospector']."' and urn = '".$v['urn']."'";
	  $apps = $this->db->query($last_appontment)->result_array();
	  foreach($apps as $app){
		$result[$k]['last_appointment']['id'] = $app['id'];
	 	$result[$k]['last_appointment']['date'] = $app['last_app'];
		$result[$k]['last_appointment']['attendees'][] = $app['attendee'];
	  }
	  }
	  $result['total'] = $total;
	  
  return $result;
  }

}

?>