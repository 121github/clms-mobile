<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Planner_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
    
    public function get_planner_prospects($postcode,$date="") {
        $distanceQry = '';
        if (!is_null($postcode)) {
            $distanceQry = $this->_get_distance_query($postcode);
            $this->db->order_by('planner.order ASC, distance ASC');
        } else {
            $this->db->order_by('planner.order', 'ASC');
        }
        $this->db->select(
            "leads.coname, leads.urn, `date`, leads.p_add1, leads.p_add2, leads.p_add3,
             leads.p_town, leads.p_postcode, planner.id as plan_id, ". $distanceQry
        );
        $this->db->from('planner');
        $this->db->join('leads', 'planner.urn = leads.urn', 'left');
        $this->db->join('locations', 'locations.postcode = leads.p_postcode', 'left');
        $this->db->where('planner.manager', $_SESSION['login']);
        if(!empty($date)){
         
        $this->db->where('planner.date', $date);    
        }
        
        return $this->db->get()->result_array();
    }
    
    public function get_appointments($options) 
    {
        $distanceQry = $distanceJoin = '';
        if (!is_null($options['postcode'])) {
            $distanceQry  = $this->_get_distance_query($options['postcode']);
            $distanceJoin = "LEFT JOIN locations on locations.postcode = leads.p_postcode";
        }
        
        $qry = "SELECT (SELECT id FROM planner WHERE planner.manager = '" . $_SESSION['login'] . "' AND planner.urn = events.urn) AS plan_id, leads.coname, events_attendees.attendee, 
                events.urn, events.title, events.set_by AS appointment_owner, events.status,
                events.text, events.begin_date, events.id as event_id, 
                events.begin_hour, events.end_hour, events.begin_mins, 
                events.end_mins, leads.p_add1, leads.p_add2, leads.p_add3, events.set_by, events.cancelled, leads.p_town, leads.p_postcode $distanceQry
                FROM events left join events_attendees on events.id = events_attendees.event_id LEFT JOIN leads ON events.urn = leads.urn 
                $distanceJoin 
                WHERE set_by in (select login from users where rep_group = 'Prospects') ";
                //add the options to the query
                if(!empty($options['set_by'])){ $qry .= " and events.set_by = '" . $options['set_by'] . "'"; }
                if(!empty($options['attendee'])){ $qry .= " and events.id in(select event_id from events_attendees where attendee = '" . $options['attendee'] . "')"; }
                if(!empty($options['date_from'])){ $qry .= " and events.begin_date between '" . $options['date_from'] . "' and '".$options['date_to']."'"; }
$this->firephp->log($qry);
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
    
    private function _get_distance_query($postcode){
        $coords = postcode_to_coords($postcode);
        return ", (((ACOS(SIN((" . $coords['lat'] . "*PI()/180)) * SIN((locations.lat*PI()/180))+COS((" . $coords['lat'] . "*PI()/180)) * COS((locations.lat*PI()/180)) * COS(((" . $coords['lng'] . "- locations.lng)*PI()/180))))*180/PI())*60*1.1515) AS distance";
    }
    
    public function add_to_planner($urn) {
        $this->db->replace('planner', array(
            'urn' => $urn,
            'manager' => $_SESSION['login']
        ));
        if ($this->db->_error_message()):
return $this->db->_error_message();
        else:
            return "success";
        endif;
    }

    public function remove_from_planner($urn) {
        return $this->db->delete('planner', array(
            'urn' => $urn,
            'manager' => $_SESSION['login']
        ));
    }
   
    public function set_planner_date($data) {
        $this->db->where(array('urn' => $data['urn'], 'manager' => $_SESSION['login']));
        return $this->db->update('planner', $data);
    }
    
    public function update_plan($urn, $data) {
        $this->db->where(array('urn' => $urn, 'manager' => $_SESSION['login']));
        $this->db->update('planner', $data);
    }
    
    /*
     * Use the functions codeigiter gives you like 'update_batch' to update
     * multiple records with different values.
     */
    public function update_sort_orders($data) {
        $this->db->update_batch('planner', $data, 'urn'); 
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    
    public function lead_is_in_planner($urn)
    {
        $this->db->select('id');
        $this->db->from('planner');
        $this->db->where('urn', $urn);
        $this->db->where('manager', $_SESSION['login']);
        if ($this->db->get()->num_rows() > 0) {
            return true;
        }
        return false;
    }
    
    public function attendees_set_by($set_by=''){
        $this->db->select('distinct(attendee)');
        $this->db->from('events');
        $this->db->join('events_attendees','events.id = events_attendees.event_id');
        if(!empty($set_by)){
        $this->db->where('set_by',$set_by);
        }
        return $this->db->get()->result_array();
    }
    
    public function clear_planner($date=''){
      if(empty($date)):
      return $this->db->delete('planner', array('manager' => $_SESSION['login'])); 
      else:
      return $this->db->delete('planner', array('manager' => $_SESSION['login'],'date'=>$date));   
      endif;
     
    }
}