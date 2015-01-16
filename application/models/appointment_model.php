<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Appointment_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function insert_appointment($data) {
        return $this->db->insert('events', $data);
    }

    public function edit_appointment($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('events', $data);
    }

    public function delete_appointments($ids) {
        $this->db->where_in('event_id', $ids);
        $this->db->delete('events_attendees');
        
        $this->db->where_in('id', $ids);
        return $this->db->delete('events');
    }

    public function delete_attendees($attendee, $id) {
        $this->db->where_in('event_id', $id);
        $this->db->delete('events_attendees');
    }

    public function update_attendees($attendee, $id) {
        $this->db->replace('events_attendees', array(
            'event_id' => $id,
            'attendee' => $attendee
        ));
    }
	
	public function get_appointments($options){
		$distanceQry = $distanceJoin = '';
        if (!is_null($options['postcode'])) {
            $distanceQry  = $this->_get_distance_query($options['postcode']);
            $distanceJoin = "LEFT JOIN locations on locations.postcode = leads.p_postcode";
        }
		
		$type = $options['type'];
		$qry = "SELECT (SELECT id FROM planner WHERE planner.manager = '" . $_SESSION['login'] . "' AND planner.urn = events.urn) AS plan_id, leads.coname, events_attendees.attendee, 
                events.urn, events.title, events.set_by AS appointment_owner, events.status,
                events.text, events.begin_date, events.id as event_id, 
                events.begin_hour, events.end_hour, events.begin_mins, 
                events.end_mins, leads.p_add1, leads.p_add2, leads.p_add3, events.set_by, events.cancelled, leads.p_town, leads.p_postcode $distanceQry
                FROM events left join events_attendees on events.id = events_attendees.event_id LEFT JOIN leads ON events.urn = leads.urn 
                $distanceJoin 
                WHERE `status` = 'Live' and cancelled is null ";
                //add the options to the query
                if(!empty($options['prospector'])){ $qry .= " and events.set_by = '" . $options['prospector'] . "'"; }
                if(!empty($options['month'])){ $qry .= " and month(events.$type) = '".$options['month']."'"; }
				if(!empty($options['year'])){ $qry .= " and year(events.$type) = '".$options['year']."'"; }
				
          $attendees=array();
          $array = array();
         foreach($this->db->query($qry)->result_array() as $appointment){
         $array[$appointment['event_id']]=$appointment;
         $attendees[$appointment['event_id']][]=$appointment['attendee'];
         $array[$appointment['event_id']]['attendees']=$attendees[$appointment['event_id']];
         if($appointment['status']=='BDE'){
           $array[$appointment['event_id']]['type'] = "BDE";
         } else {
           $array[$appointment['event_id']]['type'] = "CAE";
         }

        }
        foreach($attendees as $k => $v){
         if(count($v)<2&&$array[$k]['status']=="Live"){
           $array[$k]['tba'] = "To be confirmed";
         } else {
           $array[$k]['tba'] = "";
         }
        }
        
        return $array;
	}
    
	public function get_cancellations($options){
		$qry = "select e.urn, e.id, coname,bde,e.title,e.text, cae, ef.date_added, e.`status`,fa.reason,e.begin_date,e.begin_hour,e.begin_mins from events_feedback ef left join events e using(id) left join events_feedback_answers fa on fa.id = ef.id left join leads using(urn) where cancelled = 1 and question = 'cancelled' and  e.`status` = 'Live' ";
		if(!empty( $options['month'])){
		$qry .= " and year(e.date_added) = year('" . $options['month'] . "') and month(e.date_added)=month('" . $options['month'] . "') ";
		}
		if(!empty( $options['prospector'])){
		$qry .= " and bde = '" . $options['prospector'] . "' ";
		}
		$results = $this->db->query($qry)->result_array();
		$this->firephp->log($qry);
		return $results;
	}
	
}