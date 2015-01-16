<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Feedback extends CI_Controller {

  public function __construct() {
    parent::__construct();
    user_auth_check();
    $this->load->model('Feedback_model');
    $this->load->model('Leads_model');
  }
  
  //view feedback
    public function view() {
      /*check all the inputs*/
    $date_from = date('Y-m-d',strtotime('-1 month'));
    $date_to = date('Y-m-d');
    $prospector =  "";
    /* Put them into an array to pass to the query */
    $options = array("prospector" => $prospector, "date_from" => $date_from, "date_to" => $date_to);
        
    $feedback = $this->Feedback_model->get_feedback($options);
    $prospectors = $this->Leads_model->get_prospectors();
    $data = array(
        'pageId' => 'view-feedback',
        'title' => 'View Feedback',
        'prospectors' => $prospectors,
        'feedback' => $feedback
    );
    $this->template->load('default', 'feedback/view.php', $data);
  }
  
  public function load_view(){
    if ($this->input->is_ajax_request()) {
       /*check all the inputs*/
    $date_from = ($this->input->post('date_from') ? to_mysql_datetime($this->input->post('date_from')) : date('Y-m-d',strtotime('-1 week')));
    $date_to = ($this->input->post('date_to') ? to_mysql_datetime($this->input->post('date_to')) : date('Y-m-d'));
    $prospector =  ($this->input->post('prospector') ? $this->input->post('prospector') : "");
    /* Put them into an array to pass to the query */
    $options = array("prospector" => $prospector, "date_from" => $date_from, "date_to" => $date_to);
     /*echo results in json response*/
    echo json_encode($this->Feedback_model->get_feedback($options));
    }
  }
  
    //view feedback
    public function answers() {
    $id = $this->uri->segment(3);
    
      $questions= array("contact"=>"Was the contact level correct?",
        "renewal_date"=>"Was the renewal date accurate?",
        "premium"=>"Was the premium value accurate (+/-10%)?",
        "insurer"=>"Was the current insurer correct?",
        "broker"=>"Was the broker correct?",
        "turnover"=>"Was the turnover accurate?",
        "employees"=>"Was the number of employees accurate?",
        "trade"=>"Was the company trade correct?",
		"quote"=>"Will you be quoting?",
        "policy"=>"Was the policy type accurate?",
        "comments"=>"General Comments");
      
    $answers = $this->Feedback_model->get_feedback_answers($id);
    
    $data = array(
        'pageId' => 'feedback-answers',
        'title' => 'Feedback Answers',
        'answers' => $answers,
        'questions'=>$questions
    );
    $this->template->load('default', 'feedback/answers.php', $data);
  }
  
}
?>
