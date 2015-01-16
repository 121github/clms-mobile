<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Bonus_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get_bonuses($options)
    {
        //$conversions_cap ="7200";
        //$conversions_threshold ="1200";
        $renewals_bonus = 0;
        $appointment_bonus = 0;
        $conversions_bonus = 0;

        $renewals_qry = "select * from renewals r left join renewal_ownership ro on r.id = ro.renewal_id where year(date_added) = year('" . $options['month'] . "') and month(date_added)=month('" . $options['month'] . "') and prospector = '" . $options['prospector'] . "'";
//$this->firephp->log($renewals_qry);
        $renewals = $this->db->query($renewals_qry)->num_rows();
        //set the renewal boundries
        $renewals_cap = "310";
        $renewals_threshold = "52";
        $renewal_lower = 0.81;
        $renewal_higher = 1.21;
        $renewal_limit = 155;
        if ($renewals <= $renewals_threshold) {
            $renewals_bonus = 0;
        } else if ($renewals > $renewals_threshold && $renewals <= $renewal_limit) {
            $renewals_bonus = ($renewals - $renewals_threshold) * $renewal_higher;
        } else if ($renewals > $renewal_limit) {
            $renewals_bonus = (($renewal_limit - $renewals_threshold) * $renewal_higher) + (($renewals - $renewal_limit) * $renewal_lower);
        } else if ($renewals > $renewals_cap) {
            $renewals_bonus = (($renewal_limit - $renewals_threshold) * $renewal_higher) + (($renewals_cap - $renewal_limit) * $renewal_lower);
        }

        $cancelled_qry = "select * from events e left join events_attendees ea on e.id = ea.event_id and attendee=set_by where `status` = 'Live' and cancelled = 1 and year(e.date_added) = year('" . $options['month'] . "') and month(e.date_added)=month('" . $options['month'] . "') and set_by = '" . $options['prospector'] . "'";
        $cancelled = $this->db->query($cancelled_qry)->num_rows();
        $this->firephp->log($cancelled_qry);

        $appointments_qry = "select * from events e left join events_attendees ea on e.id = ea.event_id and attendee=set_by where `status` = 'Live' and cancelled is null and year(date_added) = year('" . $options['month'] . "') and month(date_added)=month('" . $options['month'] . "') and set_by = '" . $options['prospector'] . "'";
        $appointments_with_feedback_qry = "select * from events e left join event_feedback ef on ef.id = e.id left join events_attendees ea on e.id = ea.event_id and attendee=set_by where `status` = 'Live' and ef.`complete` = 1 and cancelled is null and year(date_added) = year('" . $options['month'] . "') and month(date_added)=month('" . $options['month'] . "') and set_by = '" . $options['prospector'] . "'";
        $appointments_without_feedback_qry = "select * from events e left join event_feedback ef on ef.id = e.id left join events_attendees ea on e.id = ea.event_id and attendee=set_by where `status` = 'Live' and ef.`complete` = 0 and cancelled is null and year(date_added) = year('" . $options['month'] . "') and month(date_added)=month('" . $options['month'] . "') and set_by = '" . $options['prospector'] . "'";

        $appointments = $this->db->query($appointments_qry)->num_rows();
        $appointments_with_feedback = $this->db->query($appointments_with_feedback_qry)->num_rows();
        $appointments_without_feedback = $this->db->query($appointments_without_feedback_qry)->num_rows();

        //set the appointment boundries
        $appointment_cap = 24;
        $appointment_threshold = 4;
        $appointment_lower = 10.42;
        $appointment_higher = 15.63;
        $appointment_limit = 12;
        if ($appointments_with_feedback <= $appointment_threshold) {
            $appointment_bonus = 0;
        } else if ($appointments_with_feedback > $appointment_threshold && $appointments_with_feedback <= $appointment_limit) {
            $appointment_bonus = ($appointments_with_feedback - $appointment_threshold) * $appointment_higher;
        } else if ($appointments_with_feedback > $appointment_limit) {
            $appointment_bonus = (($appointment_limit - $appointment_threshold) * $appointment_higher) + (($appointments_with_feedback - $appointment_limit) * $appointment_lower);
        } else if ($appointments_with_feedback > $appointment_cap) {
            $appointment_bonus = (($appointment_limit - $appointment_threshold) * $appointment_higher) + (($appointment_cap - $appointment_limit) * $appointment_lower);
        }


        /* converion query no longer needed */
        /*
        $conversions_qry = "select sum(al.premium) premium from leads left join acturis_live al on leads.acturis = al.clientkey where leads.acturis is not null and leads.acturis <> '' and acturis is not null and prospector is not null and year(STR_TO_DATE(effective,'%d/%m/%Y')) = year('" . $options['month'] . "') and month(STR_TO_DATE(effective,'%d/%m/%Y'))=month('" . $options['month'] . "') and prospector = '" . $options['prospector'] . "' group by prospector";
        $conversions_row = $this->db->query($conversions_qry)->row();
        $conversions = ($conversions_row ? $conversions_row->premium : 0);

        if($conversions>0):
               $conversions_bonus = 1000/($conversions_cap/$conversions);
        if($conversions_bonus>1000):$conversions_bonus=1000;
                elseif($conversions<=$conversions_threshold): $conversions_bonus=0;
          endif;
        endif;
        /*
        /* quality query */
        $quality_qry = "select avg(score) as score from events_feedback ef left join events e using(id) where year(e.begin_date) = year('" . $options['month'] . "') and month(e.begin_date)=month('" . $options['month'] . "') and bde = '" . $options['prospector'] . "'";
        //$this->firephp->log($quality_qry);
        $quality_row = $this->db->query($quality_qry)->row();
        $quality = ($quality_row->score ? $quality_row->score : 50);
        //this minimum quality bonus is 50%
        $quality_bonus = (($quality / 100) + 1);


        $total_bonus = $renewals_bonus;
        $total_bonus += $appointment_bonus;
        //$total_bonus += $conversions_bonus;
        $conversions = 0;
        $conversions_bonus = 0;

        $total_bonus = $total_bonus * $quality_bonus;

        $data = array("prospector" => $options['prospector'],"with_feedback"=>$appointments_with_feedback,"without_feedback"=>$appointments_with_feedback, "renewals" => $renewals, "appointments" => $appointments, "cancelled" => $cancelled, "renewals_bonus" => number_format($renewals_bonus, 2), "appointments_bonus" => number_format($appointment_bonus, 2), "quality" => number_format($quality, 2), "total_bonus" => number_format($total_bonus, 2));

        return $data;
    }

}

?>