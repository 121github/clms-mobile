<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Update extends CI_Controller {
  
 public function __construct() 
    {
        parent::__construct();
        $this->load->model('updates/Create_insurance_table_model');
        $this->load->model('updates/Create_broker_table_model');
        $this->load->model('updates/Create_renewal_ownership_table_model');
    }
  
    
    
 function index(){
   $this->Create_insurance_table_model->create();
   $response[] = $this->Create_insurance_table_model->populate();
   
   $this->Create_broker_table_model->create();
   $response[] = $this->Create_broker_table_model->populate();
   
   $this->Create_renewal_ownership_table_model->create();
   $response[] = $this->Create_renewal_ownership_table_model->populate();
   
   $data = array(
            'pageId' => 'Prospector-update',
            'title' => 'Prospector Update',
            'response'=>$response
        );
   
   $this->template->load('default', 'update/index',$data);
 }
  
}
?>
