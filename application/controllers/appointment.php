<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Appointment extends CI_Controller {

    public function __construct() {
        parent::__construct();
        user_auth_check();
        $this->load->model('Appointment_model');
        $this->load->model('History_model');
    }

    /**
     * This function is accessed via AJAX to create a new appointment. All
     * form fields are required so validation must be run. The hours and minutes
     * of the appointment time must be extracted from the 'h:m (AM/PM) format',
     * and must valudate that the end time is not before the start time.
     */
    public function save() {
		   $this->load->model('Logging_model');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('id', '', 'trim');
        $this->form_validation->set_rules('fn', '', 'trim|required');
        $this->form_validation->set_rules('urn', 'URN', 'trim|required|max_length[10]');
        $this->form_validation->set_rules('manager', 'Attendee', 'trim|required');
        $this->form_validation->set_rules('title', 'Title', 'trim|required');
        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        $this->form_validation->set_rules('begin_date', 'Date', 'trim|required');
        $this->form_validation->set_rules('start_time', 'Start Time', 'trim|required');
        $this->form_validation->set_rules('finish_time', 'Finish Time', 'trim|required');
        $this->form_validation->set_rules('status', 'Appointment type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('success' => false, 'message' => 'There are errors in your form.'));
            exit;
        }
        
        //Check that the date of the appointment is not in the past
        /*
        if (date_is_past($this->input->post('begin_date'))) {
            echo json_encode(array('success' => false, 'message' => 'Invalid date selection.'));
            exit;
        }
*/
        $startTime = $this->_extractHrsAndMins($this->input->post('start_time'));
        $endTime   = $this->_extractHrsAndMins($this->input->post('finish_time'));

        //Check that start time is not before end time.
        if ($endTime['hour'] < $startTime['hour'] || $endTime['hour'] == $startTime['hour'] && $endTime['mins'] <= $startTime['mins']) {
            echo json_encode(array('success' => false, 'message' => 'Invalid time selection.'));
            exit;
        }

        $data = array(
            'urn'        => $this->input->post('urn'),
            'begin_date' => to_mysql_datetime($this->input->post('begin_date')),
            'end_date'   => to_mysql_datetime($this->input->post('begin_date')),
            'begin_hour' => $startTime['hour'],
            'end_hour'   => $endTime['hour'],
            'begin_mins' => $startTime['mins'],
            'end_mins'   => $endTime['mins'],
            'title'      => $this->input->post('title'),
            'text'       => $this->input->post('comments'),
            'manager'    => $this->input->post('manager'),
            'set_by'     => $_SESSION['login'],
            'status'     => $this->input->post('status') //appointment type
        );

        $id = $this->input->post('id');
        $fn = $this->input->post('fn');
        if ($fn == 'add' && $this->Appointment_model->insert_appointment($data)) {
            $data['id']   = $this->db->insert_id();
            //updates the event_attendees table with the current prospector
             $this->Appointment_model->update_attendees($data['manager'],$data['id']);
            $data['date'] =  date('d/m/Y H:i',strtotime($this->input->post('begin_date')." ".$startTime['hour'].":".$startTime['mins']));
            $this->History_model->log_appointment_added($data['urn']);
            echo json_encode(array('success' => true, 'message' => 'OK', 'appointment' => $data));
            exit;
        }
        //stops the set_by field changing if somebody updates someone elses appointment

        unset($data['set_by']);
        
        if ($fn == 'edit' && !empty($id)) {
			$logging = $this->Logging_model->log_event_update($data,$id);
			$this->Appointment_model->edit_appointment($id, $data);
			$data['id']   = $id;
            $data['date'] =  $this->input->post('begin_date')." ".$startTime['hour'].":".STR_PAD($startTime['mins'],2,"0",STR_PAD_LEFT);
            $this->History_model->log_appointment_updated($data['urn'],$logging);
            echo json_encode(array('success' => true, 'message' => 'OK', 'appointment' => $data));
            exit;
        }

        echo json_encode(array('success' => false, 'message' => 'Failed to save appointment.'));
        exit;
    }

    /**
     *
     * @param string $time in the format 'hh:mm (AM||PM')
     * @return array of hour converted to 24 hr & mins as int vals 
     */
    private function _extractHrsAndMins($time) {
        $splt = preg_split('/[: ]+/', $time);
        $hr = intval($splt[0]);
        $hr24 = $splt[2] == 'PM' && $hr < 10 ? $hr + 12 : $hr;
        return array(
            'hour' => $hr24,
            'mins' => intval($splt[1])
        );
    }

    public function delete() {
        if ($this->input->is_ajax_request()) {
            $ids = $this->input->post('ids');
            if (!$ids) {
                echo json_encode(array('success' => false, 'message' => 'Invalid appointments(s).'));
                exit;
            }
            if (!$this->Appointment_model->delete_appointments(json_decode($ids, true))) {
                echo json_encode(array('success' => false, 'message' => 'Failed to delete appointment(s). Please try again.'));
                exit;
            }
            
            $this->History_model->log_appointment_deleted($this->input->post('urn'), count(json_decode($ids, true)));
            
            echo json_encode(array('success' => true, 'message' => 'OK'));
            exit;
        }
    }
    
	
	public function view(){
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
		$postcode = validate_postcode($_SESSION['current_postcode']) ? $_SESSION['current_postcode'] : null;
	  $options = $_SESSION['appointment_breakdown'];
	  $options["month"] = $month;
	  $options["prospector"]=$prospector;
 	$options["postcode"]=$postcode;


	  $description = "Showing appointments";
	  	if(!empty($options['month'])){ 
			if($options['type']=="begin_date"){ 
		 $description .= " set in ".date("F", mktime(0, 0, 0, $options['month'], 10));
		  } else {
		$description .= " set for ".date("F", mktime(0, 0, 0, $options['month'], 10)); 
		}
	}
		
		if(!empty($options['prospector'])){ 
		 $description .= " set by ".$options['prospector'];
		}
	  
 	$appointments = $this->Appointment_model->get_appointments($options);
	$this->firephp->log($appointments);
	    $data = array(
        'pageId' => 'view-appointments',
        'title' => 'View Appointments',
        'appointments' => $appointments,
		'description'=>$description
    );
	
    $this->template->load('default', 'lists/appointments.php', $data);	
	}
	
	public function cancellations(){
		$month = $this->uri->segment(3);
		$bde = $this->uri->segment(4);
		$options = array("prospector"=>$bde,"month"=>$month);
	$appointments = $this->Appointment_model->get_cancellations($options);
	    $data = array(
        'pageId' => 'view-appointments',
        'title' => 'View Appointments',
        'appointments' => $appointments,
		'description'=>"Appointment Cancellations"
    );
	
    $this->template->load('default', 'lists/cancellations.php', $data);	
	}
	
}