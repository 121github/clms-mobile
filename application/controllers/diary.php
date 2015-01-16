<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Diary extends CI_Controller {

    public function __construct() {
        parent::__construct();
        user_auth_check();
        $this->load->model('Diary_model');
        $this->load->model('Leads_model');
    }

    public function month() {
        $ajax = false;
        //
        if(!isset($_SESSION['diary_attendees'])||empty($_SESSION['diary_attendees'])):
        $_SESSION['diary_attendees']= array($_SESSION['login']);
        endif;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ajax = true;
            $date = $this->input->post('date');
            if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $date)) {
                echo json_encode(array('SUCCESS' => false, 'MESSAGE' => 'Invalid date selection.'));
                exit;
            }
        } else {
            //Date will always be the 1st of the month we are looking at.
            $date = date("Y-m-01");
        }

        $appointments = $this->Diary_model->get_appointments('month', $date);

        $dates = array();
        foreach ($appointments as $row) {
            $dates[$row['begin_date']][$row['status']] = $row['status'];
        }

        if ($ajax) {
            echo json_encode(array('SUCCESS' => true, 'APPOINTMENTS' => $dates));
            exit;
        }

        $currentYear = intval(date("Y"));
        $years = array();
        for ($i = 0; $i <= 10; $i++) {
            $years[] = $currentYear + $i;
        }

        $dateSelection = array(
            'months' => array(
                '0' => 'January',
                '1' => 'February',
                '2' => 'March',
                '3' => 'April',
                '4' => 'May',
                '5' => 'June',
                '6' => 'July',
                '7' => 'August',
                '8' => 'September',
                '9' => 'October',
                '10' => 'November',
                '11' => 'December',
            ),
            'currentMonth' => date("n") - 1,
            'years' => $years,
            'currentYear' => $currentYear
        );

        if(!empty($_SESSION['diary_region'])):
          $attendees = $this->Leads_model->get_repgroup_managers($_SESSION['diary_region']);
        else:
          $_SESSION['diary_region'] = "Prospects";
          $attendees = $this->Leads_model->get_repgroup_managers($_SESSION['diary_region']);; 
        endif;
        
       if(!isset($_SESSION['diary_attendees'])):
          $_SESSION['diary_attendees'] = array($_SESSION['login']);
        endif;
        
        $data = array(
            'pageId' => 'diary-manager-month',
            'pageClass' => 'diary-manager-month',
            'title' => 'Diary Manager',
            'appointments' => str_replace("'", "\'", json_encode($dates)),
            'dateStr' => date("F Y"),
            'regions' => $this->Leads_model->get_regions(),
            'managers' => $attendees,
            'dateSelection' => $dateSelection,
            'today' => date("Y-m-d")
        );
        $this->template->load('default', 'diary/month', $data);
    }

    public function week() {
        $appointments = $this->Diary_model->get_appointments('week');

        $data = array(
            'pageId' => 'diary-manager-week',
            'pageClass' => 'diary-manager-week',
            'title' => 'Diary Manager'
        );
        $this->template->load('default', 'diary/week', $data);
    }

    public function day() {
        //If the uri segment doesnt exist get the date from php date function.
        $date = $this->uri->segment(3) ? $this->uri->segment(3) : date("Y-m-d");
        $time = strtotime($date);
        $today = $time === strtotime(date("Y-m-d")) ? true : false;
        if(!isset($_SESSION['diary_attendees'])||empty($_SESSION['diary_attendees'])):
          $_SESSION['diary_attendees']= array($_SESSION['login']);
        endif;
        
        $appointments = $this->Diary_model->get_appointments('day', $date);

        $data = array(
            'pageId' => 'diary-manager-day-' . $date,
            'pageClass' => 'diary-manager-day',
            'title' => 'Diary Manager',
            'today' => $today,
            'dayDesc' => date('l jS \of F Y', $time),
            'appointments' => str_replace("'", "\'",json_encode($appointments))
        );
        
        $this->template->load('default', 'diary/day', $data);
    }

        public function diary_view() {
        if ($this->input->is_ajax_request()) {
            $attendees = $this->input->post('attendees');
            $region = $this->input->post('region');
            $_SESSION['diary_region'] = $region;
            unset($_SESSION['diary_attendees']);
            $_SESSION['diary_attendees'] = array();
            foreach ($attendees as $attendee) {
                $_SESSION['diary_attendees'][] = $attendee;
            }
        echo json_encode(array("success"=>true));
            exit;
        }
    }
    
}

/* End of file diary.php */
/* Location: ./application/controllers/diary.php */