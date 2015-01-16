<?php

/**
 * This class handles all functionality for cron jobs
 */
class Cron extends CI_Controller {
    
    private $_renewal_reminder_days = 60;
    
    public function __construct() {
        parent::__construct();
        $cronPass = $this->uri->segment(3);
        if (!$cronPass ||$cronPass !== '4c6b24a6e90cec43929f2477b5fb4ebc') {
            echo "Access denied";
            exit;
        }
        $this->load->model('Cron_model');
		
    }
   
	
    public function cae_allocation_reminder() {
        $data = $this->Cron_model->get_unallocated();
         if (empty($data)) {
            echo "No unallocated CAE appointments found";
        }
        //$this->firephp->log($data);
        
         //Set up ci email
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        
               //Loop through the prospectors & send emails
        foreach ($data as $prospector) {
            if (!empty($prospector['email'])) {
                $this->email->from("noreply@swintoncommercial-clms.co.uk", 'Swinton Commercial');
                $this->email->subject('Prospector unallocated appointments');
                $this->email->to($prospector['email']);
                $this->email->bcc("john.langley@swinton.co.uk");
                $this->email->bcc("david.smith@swinton.co.uk");
				 $this->email->bcc("bradf@121customerinsight.co.uk");
                $msg = "<h2>Unallocated CAE appointments</h2>"
                     . "<table width='100%' style='text-align: center;border-spacing: 0;border-collapse: collapse;'>"
                         . "<tr>"
                             . "<th style='border-bottom: 2px solid #ddd;'>URN</th>"
                             . "<th style='border-bottom: 2px solid #ddd;'>Coname</th>"
                             . "<th style='border-bottom: 2px solid #ddd;'>Title</th>"
                             . "<th style='border-bottom: 2px solid #ddd;'>Appointment Date</th>"
                             . "<th style='border-bottom: 2px solid #ddd;'>Created On</th>"
                             . "<th style='border-bottom: 2px solid #ddd;'>Created By</th>"
                             . "<th style='border-bottom: 2px solid #ddd;'>Days until appointment</th>"
                         . "</tr>";
                $i = 0;
                foreach ($prospector['Appointment'] as $appointment) {
                    $bgColor = $i % 2 === 0 ? '#F3F3F3' : '#FFFFFF';
                    $msg .= "<tr style='background-color: $bgColor'>"
                    . "<td style='border-bottom: 1px solid #ddd;'><a href='https://www.swintoncommercial-clms.co.uk/clms-mobile/index.php/leads/detail/".$appointment['urn']."'>" . $appointment['urn'] . "</a></td>"
                    . "<td style='border-bottom: 1px solid #ddd;'>" . $appointment['coname'] . "</td>"
                    . "<td style='border-bottom: 1px solid #ddd;'>" . $appointment['title'] . "</td>"                   . "<td style='border-bottom: 1px solid #ddd;'>" . $appointment['date'] . "</td>"                    . "<td style='border-bottom: 1px solid #ddd;'>" . $appointment['created'] . "</td>"
                    . "<td style='border-bottom: 1px solid #ddd;'>" . $prospector['name'] . "</td>"
                    . "<td style='border-bottom: 1px solid #ddd;'>" . $appointment['days'] . "</td>"                    . "</tr>";
                    $i++;
                }
                $msg .= "</table>";
                $this->email->message($msg);
                $this->email->send();
                echo $this->email->print_debugger();
                $this->email->clear();
            }
        }
        
    
    }
    
    /**
     * https://www.swintoncommercial-clms.co.uk/clms-mobile/index.php/cron/renewal_reminder/4c6b24a6e90cec43929f2477b5fb4ebc
     */
    public function renewal_reminder() {
        $data = $this->Cron_model->get_upcoming_renewals($this->_renewal_reminder_days);
        if (empty($data)) {
            echo "No " . $this->_renewal_reminder_days . " day renewals";
        }
        //Set up ci email
        $this->load->library('email');
        $config['mailtype'] = 'html';
        $this->email->initialize($config);
        //Loop through the prospectors & send emails
        foreach ($data as $prospector) {
            if (!empty($prospector['email'])) {
                $this->email->from("noreply@swintoncommercial-clms.co.uk", 'Swinton Commercial');
                $this->email->subject('Prospector policy renewal reminder');
                $this->email->to($prospector['email']);
				 $this->email->bcc("bradf@121customerinsight.co.uk");
                $msg = "<h2>Upcoming Policy Renewals</h2>"
                     . "<table width='100%' style='text-align: center;border-spacing: 0;border-collapse: collapse;'>"
                         . "<tr>"
                             . "<th style='border-bottom: 2px solid #ddd;'>URN</th>"
                             . "<th style='border-bottom: 2px solid #ddd;'>Company</th>"
                             . "<th style='border-bottom: 2px solid #ddd;'>Policy Type</th>"
                             . "<th style='border-bottom: 2px solid #ddd;'>Insurer</th>"
                             . "<th style='border-bottom: 2px solid #ddd;'>Broker</th>"
                             . "<th style='border-bottom: 2px solid #ddd;'>Renewal Date</th>"
                             . "<th style='border-bottom: 2px solid #ddd;'>Days Until Renewal</th>"
                         . "</tr>";
                $i = 0;
                foreach ($prospector['Policy'] as $policy) {
                    $bgColor = $i % 2 === 0 ? '#F3F3F3' : '#FFFFFF';
                    $msg .= "<tr style='background-color: $bgColor'>"
                              . "<td style='border-bottom: 1px solid #ddd;'><a href='https://www.swintoncommercial-clms.co.uk/clms-mobile/index.php/leads/detail/".$policy['urn']."'>" . $policy['urn'] . "</a></td>"
                              . "<td style='border-bottom: 1px solid #ddd;'>" . $policy['coname'] . "</td>"
                              . "<td style='border-bottom: 1px solid #ddd;'>" . $policy['type'] . "</td>"
                              . "<td style='border-bottom: 1px solid #ddd;'>" . $policy['insurer'] . "</td>"
                              . "<td style='border-bottom: 1px solid #ddd;'>" . $policy['broker'] . "</td>"
                              . "<td style='border-bottom: 1px solid #ddd;'>" . $policy['renewal_date'] . "</td>"
                              . "<td style='border-bottom: 1px solid #ddd;'>" . $policy['days_until_renewal'] . "</td>"
                        . "</tr>";
                    $i++;
                }
                $msg .= "</table>";
                $this->email->message($msg);
                $this->email->send();
                echo $this->email->print_debugger();
                $this->email->clear();
            }
        }
        
    }

}
