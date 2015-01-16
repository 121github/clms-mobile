<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Planner extends CI_Controller {

  public function __construct() {
    parent::__construct();
    user_auth_check();
    $this->load->model('Planner_model');
    $this->load->model('Logging_model');
    $this->load->model('Leads_model');
  }

  public function printable() {
    $date = '';
    if ($this->uri->segment(3)):
      $date = $this->uri->segment(3);
    endif;
    $this->load->model('Leads_model');

    $postcode = validate_postcode($_SESSION['current_postcode']) ? $_SESSION['current_postcode'] : null;

    $prospects = $this->Planner_model->get_planner_prospects($postcode, $date);

    foreach ($prospects as &$prospect) {
      $prospect = array_merge($prospect, $this->Leads_model->get_company_details($prospect['urn']));
      $prospect = array_merge($prospect, array('contacts' => $this->Leads_model->get_contacts($prospect['urn'])));
      $prospect = array_merge($prospect, array('policies' => $this->Leads_model->get_policies($prospect['urn'])));
    }

    $this->load->view('planner/printable', array(
        'prospects' => $prospects
    ));
  }

  public function data_enrichment_form() {
    $this->load->view('planner/data_enrichment_form');
  }

  public function prospects() {
    $date = '';
    $sql_date = '';
    if ($this->uri->segment(3)):
      $sql_date = $this->uri->segment(3);
      $date = date('d/m/y', strtotime($sql_date));
    endif;
    $postcode = validate_postcode($_SESSION['current_postcode']) ? $_SESSION['current_postcode'] : null;
    $prospects = $this->Planner_model->get_planner_prospects($postcode, $sql_date);
    $data = array(
        'pageId' => 'prospect-planner',
        'pageClass' => 'prospect-planner',
        'title' => 'Journey Planner',
        'prospects' => $prospects,
        'date' => $date,
        'sqldate' => $sql_date
    );

    $this->template->load('default', 'planner/prospects', $data);
  }

  public function appointments() {

    $postcode = validate_postcode($_SESSION['current_postcode']) ? $_SESSION['current_postcode'] : null;

    $set_by = ($this->input->get('set_by') ? $this->input->get('set_by') : $_SESSION['login']);
    $attendee = ($this->input->get('attendee') ? $this->input->get('attendee') : "");
    $date_from = ($this->input->get('date_from') ? to_mysql_datetime($this->input->get('date_from')) : date('Y-m-d'));
    $date_to = ($this->input->get('date_to') ? to_mysql_datetime($this->input->get('date_to')) : date('Y-m-d', strtotime('+1 month')));

    $options = array("set_by" => $set_by, "attendee" => $attendee, "date_from" => $date_from, "date_to" => $date_to, "postcode" => $postcode);
    //get all the appointments matching the filter options
    $appointments = $this->Planner_model->get_appointments($options);
    //change the dates back to uk format so we can put them in the template
    $options['date_to'] = date('d/m/Y', strtotime($date_to));
    $options['date_from'] = date('d/m/Y', strtotime($date_from));
    $prospectors = $this->Leads_model->get_prospectors();
    $data = array(
        'pageId' => 'appointment-planner',
        'pageClass' => 'appointment-planner',
        'title' => 'Appointments',
        'appointments' => $appointments,
        'prospectors' => $prospectors,
        'options' => $options
    );

    $this->template->load('default', 'planner/appointments', $data);
  }

  /**
   * This function will be called by ajax request when the user click an addToPlanner class. It passes the urn.
   */
  public function add_to_planner() {
    if ($this->input->is_ajax_request()) {
      $urn = $this->input->post('urn');
      if (!$urn):
        echo json_encode(array('success' => false, 'message' => 'Could not find the record ID'));
        exit;
      endif;
      $response = $this->Planner_model->add_to_planner($urn);
      if ($urn && $response == "success") {
        echo json_encode(array('success' => true, 'message' => 'OK.', 'urn' => $urn));
        exit;
      }
      echo json_encode(array('success' => false, 'message' => 'Failed to add to planner. ' . $response));
      exit;
    }
  }

  public function remove_from_planner() {
    if ($this->input->is_ajax_request()) {
      $urn = $this->input->post('urn');
      if ($urn && $this->Planner_model->remove_from_planner($urn)) {
        echo json_encode(array('success' => true, 'message' => 'OK.'));
        exit;
      }
      echo json_encode(array('success' => false, 'message' => 'Failed to remove from planner.'));
      exit;
    }
  }

  public function set_planner_date() {
    if ($this->input->is_ajax_request()) {
      $data = array("urn" => $this->input->post('urn'), "date" => to_mysql_datetime($this->input->post('date')));
      if ($data && $this->Planner_model->set_planner_date($data)) {
        echo json_encode(array('success' => true, 'message' => 'OK.'));
        exit;
      }
      echo json_encode(array('success' => false, 'message' => 'Failed to add date'));
      exit;
    }
  }

  public function reload_planner() {
    if ($this->input->is_ajax_request()) {
      $date = to_mysql_datetime($this->input->post('date'));
      $postcode = validate_postcode($_SESSION['current_postcode']) ? $_SESSION['current_postcode'] : null;
      if ($prospects = $this->Planner_model->get_planner_prospects($postcode, $date)) {
        echo json_encode(array('success' => true, 'message' => 'OK', 'data' => $prospects));
      } else {
        echo json_encode(array('success' => false, 'message' => 'Failed to add date'));
      }
    }
  }

  public function update_planner_order() {

    $sortData = json_decode($this->input->post('sortData'), true);

    if (!empty($sortData) && $this->Planner_model->update_sort_orders($sortData)) {
      echo json_encode(array('success' => true, 'message' => 'OK.'));
      exit;
    }
    echo json_encode(array('success' => false, 'message' => 'Failed to update sort order. Please try again.'));
    exit;
  }

  public function store_postcode() {
    $postcode = $this->input->post('postcode');
    if (validate_postcode($postcode)) {
      $this->Logging_model->log_user_location(
              $this->input->post('lat'), $this->input->post('lng'), $postcode, $this->input->post('locality')
      );
      $_SESSION['current_postcode'] = $postcode;
    }
  }

  public function get_attendee_options() {
    if ($this->input->is_ajax_request()) {
      $set_by = $this->input->post('value');
      $attendees = $this->Planner_model->attendees_set_by($set_by);
      echo json_encode($attendees);
    }
  }

  public function clear_planner() {
    if ($this->input->is_ajax_request()) {
      $date = $this->input->post('date');
      if ($this->Planner_model->clear_planner($date)):
        echo json_encode(array("success" => true));
      else:
        echo json_encode(array("success" => false, "message" => "Could not edit the planner. Please contact your admistrator"));
      endif;
    }
  }

}

/* End of file planner.php */
/* Location: ./application/controllers/planner.php */
?>