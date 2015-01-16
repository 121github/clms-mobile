<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Regions extends CI_Controller {

    
    private $_quickSearchFields = array(
        'urn',
        'coname',
        'postcode',
        'postrange'
    );
    private $_quickViewFields = array(
        'prospector',
        'call_status',
        'order_by'
    );

    public function __construct() {
        parent::__construct();
        user_auth_check();
        $this->load->model('Leads_model');
        $this->load->model('History_model');
        $this->load->model('Logging_model');
        $this->load->model('Appointment_model');
        $this->load->model('Regions_model');
    }
    
    public function view()
    {
        $data = array(
            'pageId' => 'view-regions',
            'title'  => 'View Regions',
            'prospects'  => $this->Regions_model->all()
        );
        $this->template->load('default', 'regions/view', $data);
    }

       public function filter()
    {
          if ($this->input->is_ajax_request()) {
            $region = $this->input->post('region');
            $data = $this->Regions_model->filter($region);
            if(count($data)){
            echo json_encode(array('success' => true, 'message' => 'OK', 'data' => $data));
            } else {
             echo json_encode(array('success' => false, 'message' => 'No records found in this region')); 
            }
          }
    }
}