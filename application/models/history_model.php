<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class History_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function log_history($data = array()) {
        $fields = array(
            'id' => 0,
            'urn' => '',
            'contact' => date('Y-m-d H:i:s'),
            'description' => '',
            'status' => '',
            'comments' => null,
            'nextcall' => null,
            'user' => $_SESSION['login'],
            'user_group' => $_SESSION['rep_group'],
            'log_id' => null
        );

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $fields)) {
                $fields[$key] = $value;
            }
        }

        return $this->db->insert('history', $fields);
    }

    public function log_file_deleted($urn, $count) {
        if (empty($urn)) {
            return false;
        }
        return $this->log_history(
                        array(
                            'urn' => $urn,
                            'description' => $_SESSION['login'] . " deleted $count document(s)",
                            'status' => 'Document deleted'
                        )
        );
    }

    public function log_file_downloaded($urn, $count) {
        if (empty($urn)) {
            return false;
        }
        return $this->log_history(
                        array(
                            'urn' => $urn,
                            'description' => $_SESSION['login'] . " downloaded $count document(s)",
                            'status' => 'Document downloaded'
                        )
        );
    }

    public function log_file_uploaded($urn, $file_name) {
        if (empty($urn) || empty($file_name)) {
            return false;
        }
        return $this->log_history(
                        array(
                            'urn' => $urn,
                            'description' => $_SESSION['login'] . ' uploaded a document',
                            'status' => 'Document uploaded',
                            'comments' => $file_name
                        )
        );
    }

    public function log_contact_added($urn, $log = '') {
        if (empty($urn)) {
            return false;
        }
        return $this->log_history(
                        array(
                            'urn' => $urn,
                            'description' => $_SESSION['login'] . ' added a contact',
                            'status' => 'Contact added',
                            'log_id' => $log
                        )
        );
    }

    public function log_policy_added($urn, $log = '') {
        if (empty($urn)) {
            return false;
        }
        return $this->log_history(
                        array(
                            'urn' => $urn,
                            'description' => $_SESSION['login'] . ' added a policy',
                            'status' => 'Policy added',
                            'log_id' => $log
                        )
        );
    }

    public function log_contact_updated($urn, $log = '') {
        if (empty($urn)) {
            return false;
        }
        return $this->log_history(
                        array(
                            'urn' => $urn,
                            'description' => $_SESSION['login'] . ' updated a contact',
                            'status' => 'Contact updated',
                            'log_id' => $log
                        )
        );
    }

    public function log_policy_updated($urn, $log = '') {
        if (empty($urn)) {
            return false;
        }
        return $this->log_history(
                        array(
                            'urn' => $urn,
                            'description' => $_SESSION['login'] . ' updated a policy',
                            'status' => 'Policy updated',
                            'log_id' => $log
                        )
        );
    }

    public function log_contact_deleted($urn, $count, $log = '') {
        if (empty($urn)) {
            return false;
        }
        return $this->log_history(
                        array(
                            'urn' => $urn,
                            'description' => $_SESSION['login'] . ' deleted ' . $count . ' contact(s)',
                            'status' => 'Contact deleted',
                            'log_id' => $log
                        )
        );
    }

    public function log_policy_deleted($urn, $count, $log = '') {
        if (empty($urn)) {
            return false;
        }
        if ($count > 1) {
            $policy = "policies";
        } else {
            $policy = "policy";
        }

        return $this->log_history(
                        array(
                            'urn' => $urn,
                            'description' => $_SESSION['login'] . ' deleted ' . $count . ' ' . $policy,
                            'status' => 'Policy deleted',
                            'log_id' => $log
                        )
        );
    }

    public function log_lead_updated($formData, $historyData = array()) {
        $urn = $formData['urn'];
        if (empty($urn)) {
            return false;
        }
        $defaults = array(
            'urn' => $urn,
            'description' => $_SESSION['login'] . ' updated the prospect',
            'status' => $formData['costatus'],
        );
        $data = array_merge($defaults, $historyData);
        return $this->log_history($data);
    }

    public function log_codetails_updated($formData, $historyData = array()) {
        $urn = $formData['urn'];
        $this->firephp->log($urn);
           if (empty($urn)) {
            return false;
        }
        //check if company details were updated or if general info was updated and set the history status accordingly
        
        
        if (!empty($formData['rep_group'])):
            $history_status = "General info updated";
            $history_desc = $_SESSION['login'] . ' updated the prospect';
        else:
            $history_status = "Company updated";
            $history_desc = $_SESSION['login'] . ' updated company details';
        endif;

        $defaults = array(
            'urn' => $urn,
            'description' => $history_desc,
            'status' => $history_status,
        );
        $data = array_merge($defaults, $historyData);
        //$this->firephp->log($data);
        return $this->log_history($data);
    }

    public function log_appointment_added($urn) {
        if (empty($urn)) {
            return false;
        }
        return $this->log_history(
                        array(
                            'urn' => $urn,
                            'description' => $_SESSION['login'] . ' added an appointment',
                            'status' => 'Appointment added'
                        )
        );
    }

    public function log_appointment_updated($urn,$logging=NULL) {
        if (empty($urn)) {
            return false;
        }
        return $this->log_history(
                        array(
                            'urn' => $urn,
                            'description' => $_SESSION['login'] . ' updated an appointment',
                            'status' => 'Appointment updated',
							'log_id' => $logging
                        )
        );
    }

    public function log_appointment_deleted($urn, $count) {
        if (empty($urn)) {
            return false;
        }
        return $this->log_history(
                        array(
                            'urn' => $urn,
                            'description' => $_SESSION['login'] . ' deleted ' . $count . ' appointment(s)',
                            'status' => 'Appointment deleted'
                        )
        );
    }

}
