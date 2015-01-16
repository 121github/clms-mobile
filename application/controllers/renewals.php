<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Renewals extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->model('Renewals_model');
  }

  public function view() {
	  $month  ="";
	  $prospector= ""; 
	  if($this->uri->segment(3)){
	  if(intval($this->uri->segment(3))){
	  $month = $this->uri->segment(3);
	  } else {
	 $prospector=$this->uri->segment(3);
	  }
	  }

	  if($this->uri->segment(4)){
	  if(intval($this->uri->segment(4))){
	  $month = $this->uri->segment(4);
	  } else {
	 $prospector=$this->uri->segment(4);
	  }
	  }
	
	if(isset($_SESSION['renewals']['date_from'])){
		$options['date_from'] = $_SESSION['renewals']['date_from'];
	}
		if(isset($_SESSION['renewals']['date_to'])){
		$options['date_to'] = $_SESSION['renewals']['date_to'];
	}
	  $options = $_SESSION['renewal_breakdown'];
	  $options["month"] = $month;
	  $options["prospector"]=$prospector;

	  $description = "Showing renewals";
	  		if(!empty($options['month'])){ 
		 $description .= " due in ".date("F", mktime(0, 0, 0, $options['month'], 10));
		}
		if(!empty($options['prospector'])){ 
		 $description .= " captured by ".$options['prospector'];
		}
		if(!empty($options['date_from'])){ 
		 $description .= " captured between ".date('d/m/y',strtotime($options['date_from']))." and ".date('d/m/y',strtotime($options['date_to']));
		}
		if(!empty($options['type'])){ 
		 $description .= " with a type of ".$options['type'];
		}
	  
 	$renewals = $this->Renewals_model->get_renewals($options);
	
	    $data = array(
        'pageId' => 'view-renewals',
        'title' => 'View Renewals',
        'renewals' => $renewals,
		'description'=>$description
    );
	
    $this->template->load('default', 'lists/renewals.php', $data);
  }
  
}