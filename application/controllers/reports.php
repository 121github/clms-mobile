<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Reports extends CI_Controller {

  public function __construct() {
    parent::__construct();
    user_auth_check();
    $this->load->model('Report_model');
    $this->load->model('Leads_model');
  }

  public function renewals() {
    $renewal_types = $this->Report_model->get_renewal_types();
    $options = array("type" => "", "date_from" => '2014-02-01', "date_to" => date('Y-m-d'));
	$_SESSION['renewal_breakdown']=$options;
    $renewal_data = $this->Report_model->renewal_data($options);

    $data = array(
        'pageId' => 'renewals-report',
        'title' => 'Renewals Report',
        'types' => $renewal_types,
        'data' => $renewal_data
    );
    $this->template->load('default', 'reports/renewals.php', $data);
  }

  public function load_renewal_data() {
    if ($this->input->is_ajax_request()) {
      $options = array("type" => $this->input->post("type"),
          "date_from" => to_mysql_datetime($this->input->post('date_from')),
          "date_to" => to_mysql_datetime($this->input->post('date_to')));
	$_SESSION['renewal_breakdown']=$options;
      $renewal_data = $this->Report_model->renewal_data($options);
      if ($renewal_data):
        echo json_encode(array("success" => true, "data" => $renewal_data, "post" => $options));
      else:
        echo json_encode(array("success" => false, "message" => "Failed to load renewal stats"));
      endif;
    }
  }

    public function appointments() {
    
    $options = array("year" => "2014","type"=>"date_added");
	$_SESSION['appointment_breakdown'] = $options;
    $appointment_data = $this->Report_model->appointment_data($options);
    $years = $this->Report_model->appointment_years();
            
    $data = array(
        'pageId' => 'appointments-report',
        'title' => 'Appointments Report',
        'data' => $appointment_data,
        'years' => $years
    );
    $this->template->load('default', 'reports/appointments.php', $data);
  }

  public function load_appointment_data() {
    if ($this->input->is_ajax_request()) {
      
      
      $options = array("type" => $this->input->post("type"),
       "year"=>intval($this->input->post("year")));
$_SESSION['appointment_breakdown'] = $options;

       if($this->input->post("type")=="begin_date"){
 $description = "Showing the number of CAE appointments in each month for ".$options['year']; 
      } else {
        $description = "Showing the number of appointments created each month in ".$options['year'];
      }
      
      $appointment_data = $this->Report_model->appointment_data($options);
      if ($appointment_data):
        echo json_encode(array("success" => true, "data" => $appointment_data, "post" => $options,'description'=>$description));
      else:
        echo json_encode(array("success" => false, "message" => "Failed to load appointment stats"));
      endif;
    }
  }
  
  
  public function management_information() {
    $data = array(
        'pageId' => 'management_information-report',
        'title' => 'Management Information Report',
        'bde' => $this->Leads_model->get_prospectors()
    );
    $this->template->load('default', 'reports/management_information.php', $data);
  }

  public function get_prospectors(){
            echo json_encode($this->Leads_model->get_prospectors());
             exit;
  }
  
  public function reset_export_session(){
   unset($_SESSION['export_mi']);
   unset($_SESSION['export_renewals']);
  }
  
  public function load_management_information() {
    if ($this->input->is_ajax_request()) {
      unset($_SESSION['mi_export']);
      $dateFrom = to_mysql_datetime($this->input->post('date_from'));
      $dateTo = to_mysql_datetime($this->input->post('date_to'));
      
      if ((!$dateFrom || !$dateTo) || !is_valid_date_range($dateFrom, $dateTo)) {
        echo json_encode(array('success' => false, 'message' => 'Invalid date selection'));
        exit;
      }
	  
      $miResults = $this->Report_model->get_mi_data($this->input->post());

	        //add headers to export
      if(!isset($_SESSION['export_mi'][0])){
      $_SESSION['export_mi'][] = array('Prospector','First Visit','Renewal Date','Policy Type','Broker','Insurer','Premium','Turnover','Turnover Validated','Employees','Employees Validated','Consent to call','BDE App','CAE App','Total Visits'); }
      
      $_SESSION['export_mi'][] = $miResults;

      echo json_encode(array('success' => true, 'date_from'=>$this->input->post('date_from'),'date_to'=>$this->input->post('date_to'),'miResults' => $miResults));
      exit;
    }
  }

  /**
   * user_tracking method
   * 
   * Get prospectors & load the user tracking report page
   */
  public function user_tracking() {
    $this->load->model('Leads_model');
    $prospectors = $this->Leads_model->get_prospectors();
    $data = array(
        'pageId' => 'user_tracking-report',
        'title' => 'User Tracking Report',
        'prospectors' => $prospectors
    );
    $this->template->load('default', 'reports/user_tracking.php', $data);
  }

  public function load_tracking_data() {

    if ($this->input->is_ajax_request()) {
      if ($this->input->post('prospector')) {
        $prospector = $this->input->post('prospector');
      } else {
        $prospector = $_SESSION['login'];
      }
      if (!$prospector || $prospector === 'no_selection_made') {
        echo json_encode(array('success' => false, 'message' => 'Invalid prospector'));
        exit;
      }

      if ($this->input->post('date_from') && $this->input->post('date_to')) {
        $dateFrom = to_mysql_datetime($this->input->post('date_from'));
        $dateTo = to_mysql_datetime($this->input->post('date_to'));
      } else {
        $dateFrom = date('Y-m-d');
        $dateTo = date('Y-m-d');
      }

      if ((!$dateFrom || !$dateTo) || !is_valid_date_range($dateFrom, $dateTo)) {
        echo json_encode(array('success' => false, 'message' => 'Invalid date selection'));
        exit;
      }

      echo json_encode(array('success' => true, 'tracking_data' => $this->Report_model->load_tracking_data($prospector, $dateFrom, $dateTo)));
      exit;
    }
  }

  public function activity() {
    $this->load->model('Leads_model');
    $prospectors = $this->Leads_model->get_prospectors();
    $data = array(
        'pageId' => 'activity-report',
        'title' => 'Activity Report',
        'prospectors' => $prospectors
    );
    $this->template->load('default', 'reports/activity.php', $data);
  }

  public function load_activity_data() {
    if ($this->input->is_ajax_request()) {
      if ($this->input->post('prospector')) {
        $prospector = $this->input->post('prospector');
      } else {
        $prospector = $_SESSION['login'];
      }
      if (!$prospector || $prospector === 'no_selection_made') {
        echo json_encode(array('success' => false, 'message' => 'Invalid prospector'));
        exit;
      }

      if ($this->input->post('date_from') && $this->input->post('date_to')) {
        $dateFrom = to_mysql_datetime($this->input->post('date_from'));
        $dateTo = to_mysql_datetime($this->input->post('date_to'));
      } else {
        $dateFrom = date('Y-m-d', strtotime("-1 week"));
        $dateTo = date('Y-m-d');
      }

      if ((!$dateFrom || !$dateTo) || !is_valid_date_range($dateFrom, $dateTo)) {
        echo json_encode(array('success' => false, 'message' => 'Invalid date selection'));
        exit;
      }
      $dates = $this->Report_model->dateRange($dateTo, $dateFrom, "-1 day", "Y-m-d");
      $options = array("agent" => $prospector, "from" => $dateFrom, "to" => $dateTo, "dates" => $dates);
      echo json_encode(array('success' => true, 'activity_data' => $this->Report_model->get_full_activity($options)));
      exit;
    }
  }

  public function daily() {
    if ($this->uri->segment(4)) {
      $from = mysql_escape_string($this->uri->segment(4));
      $to = mysql_escape_string($this->uri->segment(4));
    } else {
      $from = date('Y-m-d');
      $to = date('Y-m-d');
    }
    $prospector = ($this->uri->segment(5) ? mysql_escape_string($this->uri->segment(5)) : $_SESSION['login']);
    //$this->firephp->log($this->uri->segment(4));
    $dates = $this->Report_model->dateRange($to, $from, "-1 day", "Y-m-d");
    $options = array("agent" => $prospector, "from" => $from, "to" => $to, "dates" => $dates);
    $page = 'reports/daily.php';
    if ($this->uri->segment(3) === "contacts") {
      $type = "contact";
      $title = "Daily Activity Report";
      $stats = $this->Report_model->get_contact_activity($options);
    } else if ($this->uri->segment(3) === "companies") {
      $type = "company detail";
      $title = "Daily Activity Report";
      $stats = $this->Report_model->get_company_activity($options);
    } else if ($this->uri->segment(3) === "policies") {
      $type = "policy detail";
      $title = "Daily Activity Report";
      $stats = $this->Report_model->get_policy_activity($options);
    } else {
      $stats = $this->Report_model->get_full_activity($options);
      $page = "reports/activity.php";
      $title = "Activity Report";
      $type = "";
    }



    $data = array(
        'pageId' => 'activity-report',
        'title' => $title,
        'stats' => $stats,
        'type' => $type,
        'date' => date('d/m/y', strtotime($from))
    );
    $this->template->load('default', $page, $data);
  }

  public function acturis() {
    $stats = $this->Report_model->acturis_duplicates($options);


    $data = array(
        'pageId' => 'acturis-report',
        'title' => 'Acturis Report',
        'stats' => $stats
    );
    $this->template->load('default', 'reports/acturis.php', $data);
  }

  public function get_log() {
    if ($this->input->is_ajax_request()) {
      $id = $this->input->post('id');
      $log = $this->Report_model->get_log($id);
      if (is_array($log)) {
        echo json_encode(array('success' => true, 'message' => 'OK', 'log' => $log));
      } else {
        echo json_encode(array('success' => false, 'message' => 'Could not retrieve the log for this record', 'data' => $log));
      }
      exit;
    }
  }

  public function export_mi(){
    $filename = "MI-Export";

    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename={$filename}.csv");
    header("Pragma: no-cache");
    header("Expires: 0");

           $outputBuffer = fopen("php://output", 'w');
        foreach($_SESSION['export_mi'] as $val) {
            fputcsv($outputBuffer, $val);
        }
        fclose($outputBuffer);
  }
  
    public function export_renewals(){
    $filename = "Renewals-Export";

    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename={$filename}.csv");
    header("Pragma: no-cache");
    header("Expires: 0");

           $outputBuffer = fopen("php://output", 'w');
        foreach($_SESSION['export_renewals'] as $val) {
            fputcsv($outputBuffer, $val);
        }
        fclose($outputBuffer);
  }
  
      public function export_appointments(){
    $filename = "Appointments-Export";

    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename={$filename}.csv");
    header("Pragma: no-cache");
    header("Expires: 0");

           $outputBuffer = fopen("php://output", 'w');
        foreach($_SESSION['export_appointments'] as $val) {
            fputcsv($outputBuffer, $val);
        }
        fclose($outputBuffer);
  }
  
  
}

?>