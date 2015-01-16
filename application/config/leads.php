<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Leads extends CI_Controller {

    private $_searchDropdownData = array(
        'postrange' => array(5, 10, 25, 50, 100),
        'call_status' => array(
            'New',
            'Attempted to contact',
            'Attempted to contact - final attempt',
            'Initial contact made',
            'Telephoned Client - Gathering Risk Details',
            'Emailed contact',
            'Faxed contact',
            'Letter mailed',
            'Confirmed appointment',
            'Appointment made',
            'Followup required',
            'Customer cancelled appointment',
            'Existing customer',
            'Visited Client - Gathering Risk Details',
            'Call back arranged'
        ),
        'lead_status' => array(
            'Live', 'Existing Client', 'Exclude', 'Sent to SBC'
        ),
        'employees' => array(
            '1-5', '6-10', '11-20', '21-30', '31-40', '41-50', '51-100', '100+'
        ),
        'turnover' => array(
            '£0-£99k', '£100k-£249k', '£250k-£499k', '£500k-£999k',
            '£1m-£1.9m', '£2m-£4.9m', '£5m+'
        ),
        'activity_origin' => array(
            'Swinton', '121', 'TBO', 'one2one+TBO'
        ),
        'sector' => array(
            'Motor Trade',
            'Shops and retail',
            'Manufacturing',
            'Wholesale',
            'Engineering',
            'Building and Allied Trades',
            'Hospitality and Leisure',
            'Surgeries',
            'Office',
            'Transport',
            'Professions',
            'Agriculture',
            'Other Business Services',
            'Social Care',
            'Other'
        ),
        'quote_status' => array(
            'Submitted details to insurer',
            'Received quote from insurer',
            'Offered quote to client',
            'Customer declined quote',
            'Insurer declined client',
            'Accepted quote'
        )
    );
    private $_quickSearchFields = array(
        'urn',
        'coname',
        'postcode',
        'postrange'
    );

    public function __construct() {
        parent::__construct();
        user_auth_check();
        $this->load->model('Leads_model');
        $this->load->model('History_model');
        $this->load->model('Logging_model');
    }

    /**
     * If the request method is not 'POST' then load the search view to display
     * the search leads form.
     * 
     * If the request method is 'POST' then form has been submitted so do some
     * validation. If validation fails, re-load the search view to display errors.
     * If validation is successful, strip out an fields that have not been
     * completed and pass the ramianing fields into the 'build_search_leads_qry'
     * function to create the qry.
     * 
     * When the qry has been created cache it in the session and redirect to the
     * 'search_results' view. The qry is cached so that it can be executed again
     * for pagination or on page refresh.
     */
    public function search() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $this->load->library('form_validation');
            //Wrap error messages in a div so that we can style the container.
            $this->form_validation->set_error_delimiters('<div class=\'error\'>', '</div>');
            /*
             * Each form field needs to have some sort of validation so that it's
             * value can be inserted back into the form if the validation fails.
             */
            $this->form_validation->set_rules('adv_search_switch', 'Advanced Search', 'trim');
            $this->form_validation->set_rules('urn', 'URN', 'trim');
            $this->form_validation->set_rules('coname', 'Company Name', 'trim');
            $this->form_validation->set_rules('postcode', 'Postcode', 'trim');
            $this->form_validation->set_rules('postrange', 'Postcode Range', 'trim|callback_postrange_check');
            $this->form_validation->set_rules('acturis', 'Acturis Reference', 'trim');
            $this->form_validation->set_rules('lastname', 'Last Name', 'trim');
            $this->form_validation->set_rules('call_status', 'Call Status', 'trim');
            $this->form_validation->set_rules('lead_status', 'Lead Status', 'trim');
            $this->form_validation->set_rules('employees', 'Employees', 'trim');
            $this->form_validation->set_rules('turnover', 'Turnover', 'trim');
            $this->form_validation->set_rules('insurance_type', 'Insurance Type', 'trim');
            $this->form_validation->set_rules('lastcontact_from', 'Last Contacted', "trim|callback_date_range_check[" . $this->input->post('lastcontact_to') . "]");
            $this->form_validation->set_rules('lastcontact_to', 'Last Contacted', "trim");
            $this->form_validation->set_rules('renewal_from', 'Renewal Date', "trim|callback_date_range_check[" . $this->input->post('renewal_to') . "]");
            $this->form_validation->set_rules('renewal_to', 'Renewal Date', "trim");
            $this->form_validation->set_rules('leadadd_from', 'Lead Added', "trim|callback_date_range_check[" . $this->input->post('leadadd_to') . "]");
            $this->form_validation->set_rules('leadadd_to', 'Lead Added', "trim");
            $this->form_validation->set_rules('nextcontact_from', 'Next Contact', "trim|callback_date_range_check[" . $this->input->post('nextcontact_to') . "]");
            $this->form_validation->set_rules('nextcontact_to', 'Next Contact', "trim");
            $this->form_validation->set_rules('region', 'Region', 'trim');
            $this->form_validation->set_rules('manager', 'Manager', 'trim');
            $this->form_validation->set_rules('activity_origin', 'Activity Origin', 'trim');
            $this->form_validation->set_rules('sector', 'Sector / Industry', 'trim');
            $this->form_validation->set_rules('lead_source', 'Lead Source', 'trim');
            $this->form_validation->set_rules('quote_status', 'Quote Status', 'trim');

            /*
             * Run the validation. If it passes load the results.
             */
            if ($this->form_validation->run()) {

                $validFields = $this->_get_valid_search_fields($this->input->post());

                if (!empty($validFields)) {
                    $_SESSION['search_leads_qry'] = $this->Leads_model->build_search_leads_qry($validFields);
                    redirect('leads/search_results');
                }
            }
        }

        /*
         * Loop through the array of quick search fields & only pick out the
         * data we need for the quick search for the initial page load. An ajax
         * request will be fired after the page has loaded to pick up the rest.
         * I am doing this because loading the whole lot of data will perform
         * badly on a 3G connection.
         */
        $options = array();
        foreach ($this->_quickSearchFields as $quickField) {
            if (array_key_exists($quickField, $this->_searchDropdownData)) {
                $options[$quickField] = $this->_searchDropdownData[$quickField];
            }
        }

        $data = array(
            'pageId' => 'search-leads',
            'title' => 'Search Leads',
            'options' => $options
        );
        $this->template->load('default', 'leads/search', $data);
    }

    /**
     * This function is called via ajax after the search leads page has loaded.
     * This will fetch all the data for the advanced search fields in the 
     * background to save on page load time.
     */
    public function load_advanced_search_data() {
        if ($this->input->is_ajax_request()) {

            $options = array();
            foreach ($this->_searchDropdownData as $key => $val) {
                if (!in_array($key, $this->_quickSearchFields)) {
                    $options[$key] = $val;
                }
            }

            $options['insurance_type'] = $this->Leads_model->get_insurance_types();
            $options['region'] = $this->Leads_model->get_regions();
            $options['manager'] = $this->Leads_model->get_repgroup_managers();
            $options['lead_source'] = $this->Leads_model->get_lead_source();

            echo json_encode(array('options' => $options));
            exit;
        }
    }

    /**
     * Take the search leads qry data out of the session, add the limits and
     * execute. Pass the resulting leads to the view.
     */
    public function search_results() {
        $_SESSION['search_leads_qry']['limitStart'] = 0;

        $title = 'No results';
        $qryData = $_SESSION['search_leads_qry'];
        $qry = $qryData['qry'] . " LIMIT " . $qryData['limitStart'] . ", 50";

        $leads = $this->db->query($qry)->result_array();

        $showing = $qryData['total'] < 50 ? $qryData['total'] : 50;
        if (!empty($leads)) {
            $title = "Showing $showing of " . $qryData['total'] . " results";
        }

        //Load the data into the view.
        $data = array(
            'pageId' => 'search-results',
            'title' => $title,
            'leads' => $leads,
            'total' => $qryData['total']
        );
        $this->template->load('default', 'leads/search_results', $data);
    }

    //create a new prospect
    public function create() {

        if ($this->input->post('coname')) {
            $urn = $this->Leads_model->get_new_urn();
            $coname = $this->input->post('coname');
            $data = array("urn" => $urn, "coname" => $coname, "manager" => $_SESSION['login'],"date_added"=>date('Y-m-d H:i:s'),"lead_source"=>"Self Generated");
            if ($this->Leads_model->create_prospect($data)) {
                echo json_encode(array("success" => false, "message" => "Could not add new record", "urn" => $urn));
                exit;
            } else {
                $this->Logging_model->log_create_prospect($urn,$coname);
                echo json_encode(array("success" => true, "message" => "OK", "urn" => $urn));
                exit;
            }
        } else {
            $title = "Create New Prospect";
            //Load the data into the view.
            $data = array(
                'pageId' => 'create-new',
                'title' => $title
            );

            $this->template->load('default', 'leads/create', $data);
        }
    }

    /**
     * This function will be called by ajax request when the user scrolls to the
     * bottom of the page. The next set of results (50 at a time) will be loaded
     * and passed back to the view for the javascript to display.
     */
    public function paginate_leads_results() {
        if ($this->input->is_ajax_request()) {

            $data = $_SESSION['search_leads_qry'];
            $data['limitStart'] += 50;
            $qry = $data['qry'] . " LIMIT " . $data['limitStart'] . ", 50";
            $leads = $this->db->query($qry)->result_array();
            $upper = $data['limitStart'] + 50 < $data['total'] ? $data['limitStart'] + 50 : $data['total'];
            $title = 'Showing ' . $upper . ' of ' . $data['total'] . ' results';
            $_SESSION['search_leads_qry'] = $data;

            echo json_encode(array('leads' => $leads, 'title' => $title));
            exit;
        }
    }

    /**
     *
     * Go through the posted form fields and strip out any empty fields, or if
     * the advanced search is off, strip out any advanced fields that have values.
     *
     * @param array $data contains all fields posted from the search form.
     * @return array containing searchable field => value pairs.
     */
    private function _get_valid_search_fields($data) {
        $advSearch = $data['adv_search_switch'] == 'on' ? true : false;

        $validFields = array();
        foreach ($data as $key => $val) {
            if (!empty($val) && $val != 'no_selection_made' && $key != 'adv_search_switch' &&
                    (in_array($key, $this->_quickSearchFields) || (!in_array($key, $this->_quickSearchFields) && $advSearch))) {
                $validFields[$key] = $val;
            }
        }

        return $validFields;
    }

    /**
     *
     * This function checks that both dates of a range are set & that the 'from'
     * date is not more recent that the 'to' date
     *
     * @param string $dateFrom lower date of the range. If one of these is set,
     * so must the other.
     * @param string $dateTo upper date of the range.
     * @return boolean. true if the date range is valid, otherwide false.
     */
    public function date_range_check($dateFrom, $dateTo) {
        $dateTo = to_mysql_datetime($dateTo);
        $dateFrom = to_mysql_datetime($dateFrom);

        if ((!$dateFrom && $dateTo || $dateFrom && !$dateTo) ||
                ($dateFrom && $dateTo && strtotime($dateFrom) > strtotime($dateTo))) {
            $this->form_validation->set_message('date_range_check', "%s date range is invalid.");
            return false;
        }

        return true;
    }

    /**
     *
     * This function checks that if a postrange is set then a postcode must also
     * be set. It also checks that the postcode supplied is of a valid format.
     * These checks only happen when the range is supplied so that users can
     * still provide a part postcode when no range is selected. If they want to
     * search by range, the postcode must be of a valid format because we are
     * going to find the coordinates from google.
     *
     * @param int $range
     * @return boolean true if validation is successfull, otherwise false.
     */
    public function postrange_check($range) {
        if (is_numeric($range)) {
            $postcode = $this->input->post('postcode');
            if (empty($postcode)) {
                $this->form_validation->set_message('postrange_check', "Please provide a postcode.");
                return false;
            } else if (!validate_postcode($postcode)) {
                $this->form_validation->set_message('postrange_check', "The postcode provided is invalid.");
                return false;
            }
        }
        return true;
    }

    public function detail() {
        $urn = $this->uri->segment(3);

        $this->load->model('Planner_model');

        $data = array(
            'pageId' => 'lead-detail-' . uniqid(),
            'pageClass' => 'lead-detail',
            'title' => 'Lead Detail',
            'urn' => $urn,
            'inPlanner' => $this->Planner_model->lead_is_in_planner($urn),
            'generalInfo' => $this->get_formatted_general_info($urn)
        );

        $this->template->load('default', 'leads/detail', $data);
    }

    public function get_formatted_general_info($urn) {

        $info = $this->Leads_model->get_general_info($urn);
        if (!$info) {
            redirect('leads/search');
        }

        $generalInfo = array(
            'urn' => array('label' => 'URN'),
            'lead_status' => array('label' => 'Prospect Status'),
            'status_reason' => array('label' => 'Status Reason'),
            'costatus' => array('label' => 'Last Outcome'),
            'nextcall' => array('label' => 'Next Action'),
            'lead_source' => array('label' => 'Lead Source'),
            'other_lead_source' => array('label' => 'Other Lead Source'),
            'date_added' => array('label' => 'Date Added'),
            'date_updated' => array('label' => 'Date Updated'),
            'rep_group' => array('label' => 'Region', 'options' => $this->Leads_model->get_regions()),
            'manager' => array('label' => 'Manager / Executive', 'options' => $this->Leads_model->get_repgroup_managers($info['rep_group']))
        );

        foreach ($generalInfo as $key => &$val) {
            if (array_key_exists($key, $info)) {
                $val['value'] = $info[$key];
            }
        }

        return $generalInfo;
    }

    public function load_general_info_view() {
        if ($this->input->is_ajax_request()) {
            $data = array(
                'generalInfo' => $this->get_formatted_general_info($this->input->post('urn'))
            );
            $this->load->view('leads/detail_views/general_info', $data);
        }
    }

    public function load_company_details_view() {
        if ($this->input->is_ajax_request()) {

            $urn = $this->input->post('urn');

            $info = $this->Leads_model->get_company_details($urn);
            if (!$info) {
                redirect('leads/search');
            }

            $companyDetails = array(
                'coname' => array('label' => 'Company Name'),
                'cotype' => array('label' => 'Company Type', 'options' => array('Limited', 'Sole Proprietor', 'Partnership', 'PLC')),
                'cofsa' => array('label' => 'FCA Classification', 'options' => array('Consumer', 'Commercial Customer')),
                'cosector' => array('label' => 'Sector / Industry', 'options' => $this->_searchDropdownData['sector']),
                'other_cosector' => array('label' => 'Other Sector / Industry'),
                'cotrades' => array('label' => 'Types of Trade'),
                'p_add1' => array('label' => 'Address Line 1'),
                'p_add2' => array('label' => 'Address Line 2'),
                'p_add3' => array('label' => 'Address Line 3'),
                'p_town' => array('label' => 'Town'),
                'p_county' => array('label' => 'County'),
                'p_postcode' => array('label' => 'Postcode'),
                'p_country' => array('label' => 'Country'),
                's_add1' => array('label' => 'Secondary Address Line 1'),
                's_add2' => array('label' => 'Secondary Address Line 2'),
                's_add3' => array('label' => 'Secondary Address Line 3'),
                's_town' => array('label' => 'Secondary Town'),
                's_county' => array('label' => 'Secondary County'),
                's_postcode' => array('label' => 'Secondary Postcode'),
                's_country' => array('label' => 'Secondary Country'),
                'turnover' => array('label' => 'Turnover', 'options' => $this->_searchDropdownData['turnover']),
                'employees' => array('label' => 'Employees', 'options' => $this->_searchDropdownData['employees']),
                'established' => array('label' => 'Established (YYYY)'),
                'website' => array('label' => 'Website'),
                'branches' => array('label' => 'No. of Branches', 'options' => array('1', '2', '3', '4', '5+')),
                'specialist' => array('label' => 'Specialist'),
                'franchise' => array('label' => 'Franchise', 'options' => array('Yes', 'No')),
                'director_info' => array('label' => 'Director Information'),
                'sic_desc' => array('label' => 'SIC Description')
            );

            foreach ($companyDetails as $key => &$val) {
                if (array_key_exists($key, $info)) {
                    $val['value'] = $info[$key];
                }
            }

            $data = array(
                'urn' => $urn,
                'companyDetails' => $companyDetails
            );
            $this->load->view('leads/detail_views/company_details', $data);
        }
    }

    public function load_contacts_view() {
        if ($this->input->is_ajax_request()) {
            $urn = $this->input->post('urn');
            $data = array(
                'urn' => $urn,
                'contacts' => $this->Leads_model->get_contacts($urn),
            );
            $this->load->view('leads/detail_views/contacts', $data);
        }
    }

    public function load_history_view() {
        if ($this->input->is_ajax_request()) {
            $urn = $this->input->post('urn');
            $data = array(
                'urn' => $urn,
                'history' => $this->Leads_model->get_history($urn)
            );
            $this->load->view('leads/detail_views/history', $data);
        }
    }

    public function load_documents_view() {
        if ($this->input->is_ajax_request()) {

            $this->load->helper('file');
            $this->load->model('File_model');

            $urn = $this->input->post('urn');
            $data = array(
                'urn' => $urn,
                'documents' => $this->File_model->get_lead_docs($urn)
            );
            $this->load->view('leads/detail_views/documentation', $data);
        }
    }

    public function load_appointments_view() {
        if ($this->input->is_ajax_request()) {
            $urn = $this->input->post('urn');
            $data = array(
                'urn' => $urn,
                'appointments' => $this->Leads_model->get_company_appointments($urn)
            );
            $this->load->view('leads/detail_views/appointments', $data);
        }
    }

   
    
    public function load_policy_info_view() 
    {
        if ($this->input->is_ajax_request()) {
            
            $urn     = $this->input->post('urn');
            $options = $this->Leads_model->get_policy_options();

            

            $data = array(
                'urn'      => $urn,
                'policies' => $this->Leads_model->get_policies($urn),
                'options'  => $options
            );
            $this->load->view('leads/detail_views/policy_info', $data);
        }
    }
    
    public function update_policy() 
    {
        if ($this->input->is_ajax_request()) {
            
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('', '');

            $this->form_validation->set_rules('policy', 'Policy', 'trim|required');
            $this->form_validation->set_rules('renewal', 'Renewal Date', 'trim|required');
            $this->form_validation->set_rules('premium', 'Premium', 'trim');
            $this->form_validation->set_rules('insurer', 'Insurer', 'trim');
            $this->form_validation->set_rules('broker', 'Broker', 'trim');
            $this->form_validation->set_rules('urn', 'URN', 'trim|required|max_length[10]');
            
            if (!$this->form_validation->run()) {
                echo json_encode(array('success' => false, 'message' => validation_errors()));
                exit;
            }
            
 
        $data = array(
            'urn' => $this->input->post('urn'),
            'id' =>  $this->input->post('id'),
            'policy' =>  $this->input->post('policy'),
            'premium' =>  intval($this->input->post('premium')),
            'renewal' => to_mysql_datetime($this->input->post('renewal')),
            'insurer' => $this->input->post('insurer'),
            'broker' => $this->input->post('broker')
        );
 
 

            if ($data['id'] == 0) {
                $insert_id = $this->Leads_model->insert_policy($data);
                if (!$insert_id) {
                    echo json_encode(array('success' => false, 'message' => 'Failed to add policy. Please try again.'));
                    exit;
                }
                $logging = $this->Logging_model->log_policy_insert($data, $insert_id);
                $this->History_model->log_policy_added($data['urn'],$logging);
            } else if ($data['id'] > 0) {
                $logging = $this->Logging_model->log_policy_update($data);
                if (!$this->Leads_model->update_policy($data)) {
                    echo json_encode(array('success' => false, 'message' => 'Failed to update policy. Please try again.'));
                    exit;
                }
                $this->History_model->log_policy_updated($data['urn'],$logging);
            }

            echo json_encode(array('success' => true, 'message' => 'OK', 'policy' => $data));
            exit;
        }
    }

    
        /**
     * Delete contacts by the ids provided.
     */
    public function delete_policy() {
        if ($this->input->is_ajax_request()) {

            $ids = $this->input->post('ids');
            if (!$ids) {
                echo json_encode(array('success' => false, 'message' => 'Invalid policy selection(s)','data'=>$ids));
                exit;
            }
            $log = $this->Logging_model->log_policy_deleted(json_decode($ids, true));
            
            if (!$this->Leads_model->delete_policy(json_decode($ids, true))) {
                echo json_encode(array('success' => false, 'message' => 'Failed to delete policy selection(s). Please try again.'));
                exit;
            }
            
            $this->History_model->log_policy_deleted($this->input->post('urn'),count(json_decode($ids, true)), $log);

            echo json_encode(array('success' => true, 'message' => 'OK'));
            exit;
        }
    }
    
    /**
     * This function is responsibe for updating/inserting contacts depending on
     * the id passed from the ui. If the id is 0, this is not an existing
     * contact and so will be inserted. If the id is > 0, the contact with this
     * id will be updated.
     */
    public function update_contact() {

        if ($this->input->is_ajax_request()) {

            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('', '');

            $this->form_validation->set_rules('email', 'Email Address', 'trim|valid_email');
            $this->form_validation->set_rules('fax', 'Fax Number', 'trim');
            $this->form_validation->set_rules('firstname', 'First Name', 'trim|required');
            $this->form_validation->set_rules('keydm', 'Key Decision Maker', 'trim');
            $this->form_validation->set_rules('lastname', 'Last Name', 'trim|required');
            $this->form_validation->set_rules('mobile', 'Mobile', 'trim');
            $this->form_validation->set_rules('position', 'Position', 'trim');
            $this->form_validation->set_rules('positiondetails', 'Position Details', 'trim');
            $this->form_validation->set_rules('telephone', 'Telephone', 'trim');
            $this->form_validation->set_rules('title', 'Title', 'trim');
            $this->form_validation->set_rules('urn', 'URN', 'trim|required|max_length[10]');

            if (!$this->form_validation->run()) {
                echo json_encode(array('success' => false, 'message' => validation_errors()));
                exit;
            }

            $data = array(
                'id' => 0, 'urn' => '', 'priority' => '', 'title' => '', 'firstname' => '', 'lastname' => '',
                'position' => '', 'other_position' => '', 'positiondetails' => '', 'telephone' => '',
                'mobile' => '', 'work' => '', 'fax' => '', 'email' => '', 'keydm' => ''
            );

            foreach ($this->input->post() as $key => $value) {
                if ($value != '' && $value != 'no_selection_made') {
                    $data[$key] = $value;
                }
            }

            if ($data['id'] == 0) {
                $insert_id = $this->Leads_model->insert_contact($data);
                if (!$insert_id) {
                    echo json_encode(array('success' => false, 'message' => 'Failed to add contact. Please try again.'));
                    exit;
                }
                $logging = $this->Logging_model->log_contact_insert($data, $insert_id);
                $this->History_model->log_contact_added($data['urn'],$logging);
            } else if ($data['id'] > 0) {
                $logging = $this->Logging_model->log_contact_update($data);
                if (!$this->Leads_model->update_contact($data)) {
                    echo json_encode(array('success' => false, 'message' => 'Failed to update contact. Please try again.'));
                    exit;
                }
                $this->History_model->log_contact_updated($data['urn'],$logging);
            }

            echo json_encode(array('success' => true, 'message' => 'OK', 'contact' => $data));
            exit;
        }
    }

    /**
     * Delete contacts by the ids provided.
     */
    public function delete_contacts() {
        if ($this->input->is_ajax_request()) {

            $ids = $this->input->post('ids');
            if (!$ids) {
                echo json_encode(array('success' => false, 'message' => 'Invalid contact(s).'));
                exit;
            }

            $log = $this->Logging_model->log_contact_deleted(json_decode($ids, true));
            
            if (!$this->Leads_model->delete_contacts(json_decode($ids, true))) {
                echo json_encode(array('success' => false, 'message' => 'Failed to delete contact(s). Please try again.','data'=>$this->input->post()));
                exit;
            }

            $this->History_model->log_contact_deleted($this->input->post('urn'), count(json_decode($ids, true)), $log);

            echo json_encode(array('success' => true, 'message' => 'OK'));
            exit;
        }
    }

    //get all the execs for a particular region/rep_group
    public function get_managers() {
        if ($this->input->is_ajax_request()) {

            $managers = $this->Leads_model->get_repgroup_managers($this->input->post('rep_group'));

            if ($managers) {
                echo json_encode(array('success' => true, 'managers' => $managers));
                exit;
            }

            echo json_encode(array('success' => false, 'message' => 'Unable to retrive managers. Please try again.'));
            exit;
        }
    }

    
        //get all the execs for a particular region/rep_group
    public function duplicates() {
        if ($this->input->is_ajax_request()) {
            $urn = $this->input->post('urn');
            $dupes = $this->Leads_model->get_duplicates($urn);
            if (count($dupes) > 0 && is_array($dupes)) {
                echo json_encode(array("success" => true, "message" => "Potential duplicates were found", "data" => $dupes));
            } else {
                echo json_encode(array("success" => false, "message" => "No duplicates were found", "data" => $dupes));
            }
        }
    }

    public function local() {
        if ($this->input->is_ajax_request()) {
            $urn = $this->input->post('urn');
            $locals = $this->Leads_model->get_local($urn);
            if (count($locals) > 0) {
                echo json_encode(array("success" => true, "message" => "Local customers were found", "data" => $locals));
            }
            if (!count($locals)) {
                echo json_encode(array("success" => false, "message" => "No local customers were found", "data" => $locals));
            }
        }
    }

}

/* End of file leads.php */
/* Location: ./application/controllers/leads.php */