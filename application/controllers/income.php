<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Income extends CI_Controller {

  public function __construct() {
    parent::__construct();
    user_auth_check();
    $this->load->model('Income_model');
    $this->load->model('Leads_model');
  }

  //view income report
  public function index() {
	  
	 $prospectors = $this->Leads_model->get_prospectors();
	 $months = get_select_months();
	 $month = ($this->input->post('month') ? $this->input->post('month') : "");
	 $prospector = ($this->input->post('prospector') ? $this->input->post('prospector') : "");
	 $options = array("prospector"=>$prospector,"month"=>$month);
	 $income = $this->Income_model->get_income($options);
	
	 
    $data = array(
        'pageId' => 'income-report',
        'title' => 'Income Report',
        'income' => $income,
        'month' => $month,
        'months' => $months,
        'prospectors' => $prospectors
    );
	
	
    $this->template->load('default', 'reports/income.php', $data);
  }

//view income report
  public function filter() {
	   if ($this->input->is_ajax_request()) {
	 $prospectors = $this->Leads_model->get_prospectors();
	 $months = get_select_months();
	 $month = ($this->input->post('month') ? $this->input->post('month') : "");
	 $prospector = ($this->input->post('prospector') ? $this->input->post('prospector') : "");
	 $options = array("prospector"=>$prospector,"month"=>$month);
	 $income = $this->Income_model->get_income($options);
	
		echo json_encode(array("success"=>true,"data"=>$income));
		exit; 
	 }
  }



}

?>