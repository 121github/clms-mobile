<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Bonus extends CI_Controller {

  public function __construct() {
    parent::__construct();
    user_auth_check();
    $this->load->model('Bonus_model');
    $this->load->model('Leads_model');
  }

  //view bonus report
  public function report() {
	
	$months = get_select_months();
    $month = ($this->input->post('month') ? $this->input->post('month') : date('Y-m-01'));

    if ($this->input->post('prospector')) {
      $prospectors[] = $this->input->post('prospector');
    } else {
      $prospectors = $this->Leads_model->get_prospectors();
    }
    $bonuses = array();

    foreach ($prospectors as $prospector):
      $options = array("prospector" => $prospector, "month" => $month);
      $bonuses[$prospector] = $this->Bonus_model->get_bonuses($options);
    endforeach;

    if ($this->input->post()) {
      echo json_encode(array("success" => true, "month"=>$month, "data" => $bonuses));
      exit;
    }
    //$this->firephp->log($bonuses);

    $data = array(
        'pageId' => 'bonus-report',
        'title' => 'Draft Bonus Report',
        'bonuses' => $bonuses,
        'month' => $month,
        'months' => $months,
        'prospectors' => $prospectors
    );
    $this->template->load('default', 'reports/bonus.php', $data);
  }

}

?>