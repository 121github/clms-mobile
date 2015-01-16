<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Exports extends CI_Controller {

  public function __construct() {
    parent::__construct();
    user_auth_check();
    $this->load->model('Export_model');
	$this->load->model('Leads_model');
  }

  //view bonus report
  public function index() {
    $data = array(
        'pageId' => 'export',
        'title' => 'Exporter',
		'bde' => $this->Leads_model->get_prospectors()
    );
    $this->template->load('default', 'exports/exporter.php', $data);	
	
	
  }
  
  public function regional_appointments(){
	 if ($this->input->post()) {
	  $options= array();
	$options['from'] = ($this->input->post('date_from')?to_mysql_datetime($this->input->post('date_from')):"2014-01-01");
	 $options['to'] = ($this->input->post('date_to')?to_mysql_datetime($this->input->post('date_to')):"2050-01-01");
	  $options['bde'] = ($this->input->post('bde')?$this->input->post('bde'):"");
	  //print_r($options);
	  //exit;
	$result = $this->Export_model->regional_appointments($options);

	    $filename = "Regional-appointments";

    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename={$filename}.csv");
    header("Pragma: no-cache");
    header("Expires: 0");

           $outputBuffer = fopen("php://output", 'w');
		     
		   		$headers = array("URN","Appointment Date","BDE","CAE","Company","Renewal Date","Region","Branch Manager","Regional Manager","Quality Score","Cancelled");
				fputcsv($outputBuffer, $headers);
		   
        foreach($result as $val) {
            fputcsv($outputBuffer, $val);
        }
        fclose($outputBuffer);
		}  
	  
  }
  
  
   public function renewals_export(){
	  $options= array();
	//$options['from'] = ($this->input->post('date_from')?to_mysql_datetime($this->input->post('date_from')):"2014-01-01");
	 //$options['to'] = ($this->input->post('date_to')?to_mysql_datetime($this->input->post('date_to')):"2050-01-01");
	 // $options['bde'] = ($this->input->post('bde')?$this->input->post('bde'):"");
	$result = $this->Export_model->renewals_export($options);

	    $filename = "Renewals-export";

    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename={$filename}.csv");
    header("Pragma: no-cache");
    header("Expires: 0");

           $outputBuffer = fopen("php://output", 'w');
		     
		   		$headers = array("BDE","Region","Renewal","Company","Name","Address1","Address2","Address3","Address4","Address5","Postcode");
				fputcsv($outputBuffer, $headers);
		   
        foreach($result as $val) {
            fputcsv($outputBuffer, $val);
        }
        fclose($outputBuffer);

  }
  
     public function renewals_noadd_export(){
	  $options= array();
	//$options['from'] = ($this->input->post('date_from')?to_mysql_datetime($this->input->post('date_from')):"2010-01-01");
	 //$options['to'] = ($this->input->post('date_to')?to_mysql_datetime($this->input->post('date_to')):"2050-01-01");
	 // $options['bde'] = ($this->input->post('bde')?$this->input->post('bde'):"");
	  //print_r($options);
	  //exit;
	$result = $this->Export_model->renewals_noadd_export($options);
	    $filename = "Renewals-no-address-export";

    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename={$filename}.csv");
    header("Pragma: no-cache");
    header("Expires: 0");

           $outputBuffer = fopen("php://output", 'w');
		     
		   		$headers = array("Reason","URN","BDE","Region","Renewal","Company","Name","Address1","Address2","Address3","Address4","Address5","Postcode");
				fputcsv($outputBuffer, $headers);
		   
        foreach($result as $val) {
            fputcsv($outputBuffer, $val);
        }
        fclose($outputBuffer); 
	  
  }
  
    
     public function renewals_notel_export(){
	  $options= array();
	//$options['from'] = ($this->input->post('date_from')?to_mysql_datetime($this->input->post('date_from')):"2010-01-01");
	 //$options['to'] = ($this->input->post('date_to')?to_mysql_datetime($this->input->post('date_to')):"2050-01-01");
	 // $options['bde'] = ($this->input->post('bde')?$this->input->post('bde'):"");
	  //print_r($options);
	  //exit;
	$result = $this->Export_model->renewals_notel_export($options);
	    $filename = "Renewals-no-tel-export";

    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename={$filename}.csv");
    header("Pragma: no-cache");
    header("Expires: 0");

           $outputBuffer = fopen("php://output", 'w');
		     
		   		$headers = array("Prospector","URN","Company","Product","Renewal Date");
				fputcsv($outputBuffer, $headers);
		   
        foreach($result as $val) {
            fputcsv($outputBuffer, $val);
        }
        fclose($outputBuffer); 
	  
  }
  
}