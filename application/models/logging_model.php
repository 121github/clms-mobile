<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Logging_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * log_user_location method
     * 
     * Select the last entry for the session user. If this was entered today, 
     * check if the postcode matches the postcode the user is currently at. 
     * If it does match, do not insert a new row. Otherwise they have moved
     * so log their current location.
     * 
     * @param float $lat
     * @param float $lng
     * @param string $postcode
     */
    public function log_user_location($lat = 0, $lng = 0, $postcode = '', $locality = '') {
        
        if (empty($postcode)) {
            return false;
        }
        
        $qry = "SELECT postcode
                FROM user_locations 
                WHERE user = " . $this->db->escape($_SESSION['login']) . "
                AND DATE(timestamp) = CURDATE() 
                ORDER BY timestamp DESC 
                LIMIT 1";
        $query = $this->db->query($qry);

        $lastLocPcode = $query->num_rows() > 0 ? $query->row()->postcode : null;

        if (is_null($lastLocPcode) || $lastLocPcode !== $postcode) {
            $data = array(
                'user'     => $_SESSION['login'],
                'lat'      => $lat,
                'lng'      => $lng,
                'postcode' => $postcode,
                'locality' => $locality,
            );
            $this->db->insert('user_locations', $data);
        }
    }


  
    public function log_codetails_updated($data = array()) {

        $urn = $data['urn'];
        $qry = "SELECT * from leads WHERE urn = '$urn'";
        $original = $this->db->query($qry)->result_array();

        foreach ($original[0] as $key => $value) {
            if (!array_key_exists($key, $data)) {
                unset($original[0][$key]);
            }
        }
        //compare the new data with the old data to see what has changed
        $diff = array_diff($data, $original[0]);
        //$this->firephp->log($original[0]);
        $log_id = null;

        //if something has changed we log the change
        if (count($diff) > 0) {
            $details = array(
                'id' => 0,
                'user' => $_SESSION['login'],
                'action' => "update",
                'change_table' => 'leads',
                'urn' => $urn
            );
            $this->db->insert('prospector_log', $details);
            $log_id = $this->db->insert_id();
        }
        //we also log the associated values in the log data table
        foreach ($diff as $column => $value) {
            $oldval = (empty($original[0][$column]) ? "" : $original[0][$column]);

            $fields = array(
                'id' => $log_id,
                'change_field' => $column,
                'old_val' => $oldval,
                'new_val' => $value,
                'table_id' => $urn
            );

            $this->db->insert('prospector_log_data', $fields);
        }

        return $log_id;
    }

    public function log_contact_insert($data = array(), $insert_id = '') {

        $details = array(
            'id' => 0,
            'user' => $_SESSION['login'],
            'action' => "insert",
            'change_table' => 'contacts',
            'urn' => $data["urn"]
        );
        $this->db->insert('prospector_log', $details);
        $log_id = $this->db->insert_id();

        foreach ($data as $column => $value) {
            if (!empty($value)) {
                $fields = array(
                    'id' => $log_id,
                    'change_field' => $column,
                    'old_val' => '',
                    'new_val' => $value,
                    'table_id' => $insert_id
                );
                $this->db->insert('prospector_log_data', $fields);
            }
        }
        return $log_id;
    }

    public function log_policy_insert($data = array(), $insert_id = '') {

        $details = array(
            'id' => 0,
            'user' => $_SESSION['login'],
            'action' => "insert",
            'change_table' => 'renewals',
            'urn' => $data["urn"]
        );
        $this->db->insert('prospector_log', $details);
        $log_id = $this->db->insert_id();

        foreach ($data as $column => $value) {
            if (!empty($value)) {
                $fields = array(
                    'id' => $log_id,
                    'change_field' => $column,
                    'old_val' => '',
                    'new_val' => $value,
                    'table_id' => $insert_id
                );
                $this->db->insert('prospector_log_data', $fields);
            }
        }
        return $log_id;
    }

    public function log_policy_update($data = array()) {

        $policy = $data['id'];
        $qry = "SELECT * from renewals WHERE id = '$policy'";
        $original = $this->db->query($qry)->result_array();
        $urn = $original[0]['urn'];

        foreach ($original[0] as $key => $value) {
            if (!array_key_exists($key, $data)) {
                unset($original[0][$key]);
            }
        }

        $diff = array_diff($data, $original[0]);

        if (count($diff) > 0) {
            $details = array(
                'id' => 0,
                'user' => $_SESSION['login'],
                'action' => "update",
                'change_table' => 'renewals',
                'urn' => $urn
            );
            $this->db->insert('prospector_log', $details);
            $log_id = $this->db->insert_id();
            foreach ($diff as $column => $value) {
                $fields = array(
                    'id' => $log_id,
                    'change_field' => $column,
                    'old_val' => $original[0][$column],
                    'new_val' => $value,
                    'table_id' => $policy
                );
                $this->db->insert('prospector_log_data', $fields);
            }

            return $log_id;
        }
    }

    public function log_contact_update($data = array()) {


        $contact = $data['id'];
        $qry = "SELECT * from contacts WHERE id = '$contact'";
        $original = $this->db->query($qry)->result_array();
        $urn = $original[0]['urn'];

        foreach ($original[0] as $key => $value) {
            if (!array_key_exists($key, $data)) {
                unset($original[0][$key]);
            }
        }
        $diff = array_diff($data, $original[0]);

        if (count($diff) > 0) {
            $details = array(
                'id' => 0,
                'user' => $_SESSION['login'],
                'action' => "update",
                'change_table' => 'contacts',
                'urn' => $urn
            );
            $this->db->insert('prospector_log', $details);
            $log_id = $this->db->insert_id();
            foreach ($diff as $column => $value) {
                $fields = array(
                    'id' => $log_id,
                    'change_field' => $column,
                    'old_val' => $original[0][$column],
                    'new_val' => $value,
                    'table_id' => $contact
                );
                $this->db->insert('prospector_log_data', $fields);
            }

            return $log_id;
        }
    }

    public function log_contact_deleted($data = array()) {

        $urn_query = "SELECT * from contacts WHERE id = '$data[0]'";
        $urn_result = $this->db->query($urn_query)->result_array();

        $details = array(
            'id' => 0,
            'user' => $_SESSION['login'],
            'action' => "delete",
            'change_table' => "contacts",
            'urn' => $urn_result[0]["urn"]
        );

        $this->db->insert('prospector_log', $details);
        $log_id = $this->db->insert_id();

        foreach ($data as $contact) {
            $qry = "SELECT * from contacts WHERE id = '$contact'";
            $original = $this->db->query($qry)->result_array();

            $fields = array(
                'id' => $log_id,
                'change_field' => $original[0]["urn"],
                'old_val' => $original[0]["firstname"] . " " . $original[0]["lastname"],
                'new_val' => "",
                'table_id' => $original[0]["id"],
            );
            $this->db->insert('prospector_log_data', $fields);
        }

        return $log_id;
    }

    public function log_policy_deleted($data = array()) {
        $urn_query = "SELECT * from renewals WHERE id = '$data[0]'";
        $urn_result = $this->db->query($urn_query)->result_array();

        $details = array(
            'id' => 0,
            'user' => $_SESSION['login'],
            'action' => "delete",
            'change_table' => " renewals",
            'urn' => $urn_result[0]["urn"]
        );

        $this->db->insert('prospector_log', $details);
        $log_id = $this->db->insert_id();

        foreach ($data as $policy) {
            $qry = "SELECT * from  renewals WHERE id = '$policy'";
            $original = $this->db->query($qry)->result_array();

            $fields = array(
                'id' => $log_id,
                'change_field' => $original[0]["urn"],
                'old_val' => $original[0]["type"],
                'new_val' => "",
                'table_id' => $original[0]["id"],
            );
            $this->db->insert('prospector_log_data', $fields);
        }

        return $log_id;
    }

    public function log_create_prospect($urn, $coname) {

        $details = array(
            'id' => 0,
            'user' => $_SESSION['login'],
            'action' => "insert",
            'change_table' => "leads",
            'urn' => $urn
        );
        $this->db->insert('prospector_log', $details);
        $log_id = $this->db->insert_id();

        $fields = array(
            'id' => $log_id,
            'change_field' => "coname",
            'old_val' => "",
            'new_val' => $coname,
            'table_id' => $urn,
        );
        $this->db->insert('prospector_log_data', $fields);
    }

		//urn test PRO102971
public function log_event_update($data = array(),$id) {
		$data['id'] = $id;
        $event = $data['id'];
        $qry = "SELECT * from events WHERE id = '$event'";
        $original = $this->db->query($qry)->result_array();
        $urn = $original[0]['urn'];
        foreach ($original[0] as $key => $value) {
            if (!array_key_exists($key, $data)) {
                unset($original[0][$key]);
            }
        }
$this->firephp->log($data);
$this->firephp->log($original[0]);

        $diff = array_diff($data, $original[0]);

        if (count($diff) > 0) {
            $details = array(
                'id' => 0,
                'user' => $_SESSION['login'],
                'action' => "update",
                'change_table' => 'events',
                'urn' => $urn
            );
			
            $this->db->insert('prospector_log', $details);
            $log_id = $this->db->insert_id();
			
            foreach ($diff as $column => $value) {
                $fields = array(
                    'id' => $log_id,
                    'change_field' => $column,
                    'old_val' => $original[0][$column],
                    'new_val' => $value,
                    'table_id' => $event
                );
                $this->db->insert('prospector_log_data', $fields);
            }

            return $log_id;
        }
    }


}
