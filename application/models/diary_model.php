<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Diary_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }
    
    public function get_appointments($selection, $date) {
     
      $attendees = "";
      foreach($_SESSION['diary_attendees'] as $attendee){
        $attendees .= "'".$attendee."',";
      }
      $attendees = rtrim($attendees,",");
        switch ($selection) {
            case 'month':
              $qry = "select begin_date,status,attendee from `events` left join `events_attendees` on `events_attendees`.`event_id` = `events`.`id` where (attendee in(".$attendees.") or events.manager in(".$attendees.")) AND begin_date >= '".$date."' AND begin_date <= '" . date("Y-m-t", strtotime($date)) . "' group by `begin_date`,`status`";
                break;
            case 'day':
               $qry = "select `id`, `begin_date`, `begin_hour`, `end_hour`, `begin_mins`, `end_mins`, `title`, `text`, `events`.`urn`,`status`,`attendee`,`coname` from `events` left join `events_attendees` on `events_attendees`.`event_id` = `events`.`id` left join `leads` on `events`.urn = `leads`.urn where (attendee in(".$attendees.") or events.manager in(".$attendees.")) AND begin_date = '" . $date ."' group by events.id order by begin_date ";
                break;
        }
       $query = $this->db->query($qry);
        return $query->result_array();
    }

}