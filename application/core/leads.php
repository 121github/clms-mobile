<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Leads extends CI_Controller {

    private $_searchDropdownData = array(
        'postrange' => array(5, 10, 25, 50, 100),
        'call_status' => array(
            'Quote Given',
            'Existing Customer',
            'Duplicate Record',
            'Contact Unavailable',
            'Not Interested',
            'Supression Request',
            'Ceased Trading',
            'Bankrupt',
            'Telemarketing Only'
        ),
        'lead_status' => array(
            'Live','Exclude'
        ),
        'employees' => array(
            '1-5', '6-10', '11-20', '21-30', '31-40', '41-50', '51-100', '100+'
        ),
        'turnover' => array(
            'U: Unknown', 'A: <&pound;50k', 'B: &pound;50k - &pound;100k', 'C: £100k - £250k',
            'D: £250k - £500k', 'E: £500k - £1M', 'F: £1M - £5M', 'G: £5M - £10M', 'H: £10M - £20M', 'I: £20M - £50M'
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
        ),
        'order_by' => array(
            'Distance',
            'Next Action',
            'Last Action',
            'Renewal Date'),
    );
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
            $this->form_validation->set_rules('order_by', 'Order By', 'trim');

            /*
             * Run the validation. If it passes load the results.
             */
            if ($this->form_validation->run()) {

                $validFields = $this->_get_valid_search_fields($this->input->post());
                
                if (count($validFields) === 1 && isset($validFields['urn'])) {
                    if ($this->Leads_model->get_lead_by_urn($validFields['urn'])) {
                        redirect('leads/detail/' . $validFields['urn']);
                    }
                } else if (!empty($validFields)) {
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

    //this is the same function as above but stripped down for the view page
    public function view() {
        $_SESSION['page'] = "view";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
                    $_SESSION['view_leads_filter'] = $this->input->post();
                    $_SESSION['view_leads_qry'] = $this->Leads_model->build_view_leads_qry($_SESSION['view_leads_filter']);
        } else {
            if(!$_SESSION['view_leads_filter']){
            $_SESSION['view_leads_filter'] = array("prospector"=>null,"call_status"=>null,"order_by"=>null);    
            }
            $_SESSION['view_leads_qry'] = $this->Leads_model->build_view_leads_qry($_SESSION['view_leads_filter']);   
        }

        $options = array();
        $searchData = $this->_searchDropdownData;
        //add the prospectors to the search data array
        $searchData["prospector"] = $this->Leads_model->get_prospectors();

        foreach ($this->_quickViewFields as $quickField) {
            if (array_key_exists($quickField, $searchData)) {
                $options[$quickField] = $searchData[$quickField];
            }
        }

        $_SESSION['view_leads_qry']['limitStart'] = "0";

        $title = 'No results';
        $qryData = $_SESSION['view_leads_qry'];
        $qry = $qryData['qry'] . " LIMIT " . $qryData['limitStart'] . ", 50";

        $leads = $this->db->query($qry)->result_array();

        $showing = $qryData['total'] < 50 ? $qryData['total'] : 50;
        if (!empty($leads)) {
            $title = "Showing $showing of " . $qryData['total'] . " results";
        }

        //Load the data into the view.
        $data = array(
            'pageId' => 'view-leads',
            'title' => $title,
            'leads' => $leads,
            'filter' => $_SESSION['view_leads_filter'],
            'options' => $options,
            'total' => $qryData['total']
        );
        $this->template->load('default', 'leads/view', $data);
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
        $_SESSION['page'] = "search";
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
    public function add_new_prospect() {
        if ($this->input->is_ajax_request()) {

            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('', '');

            $this->form_validation->set_rules('coname', 'Company Name', 'trim|required');
            $this->form_validation->set_rules('postcode', 'Postcode', 'trim|required');
            $this->form_validation->set_rules('telephone', 'Telephone', 'trim|numeric');

            if (!$this->form_validation->run()) {
                echo json_encode(array('success' => false, 'message' => validation_errors()));
                exit;
            }


            //if postcode or telephone number have been entered check for duplicates
            $dupes = $this->Leads_model->check_duplicates($this->input->post());

            if (!$dupes) {

                $urn = $this->Leads_model->get_new_urn();
                $coname = $this->input->post('coname');
                $data = array(
                    "urn" => $urn,
                    "coname" => $coname,
                    "manager" => $_SESSION['login'],
                    "date_added" => date('Y-m-d H:i:s'),
                    "lead_source" => "Self Generated"
                );

                if ($this->Leads_model->create_prospect($data)) {
                    echo json_encode(array("success" => false, "message" => "Could not add new record", "urn" => $urn));
                    exit;
                } else {
                    $this->Logging_model->log_create_prospect($urn, $coname);
                    echo json_encode(array("success" => true, "message" => "OK", "urn" => $urn));
                    exit;
                }
            } else {
                echo json_encode(array("success" => false, "message" => "Duplicates records were found", "dupes" => $dupes));
            } exit;
        }
    }

    //show create form
    public function create() {
        $data = array(
            'pageId' => 'create-new',
            'title' => 'Create New Prospect'
        );
        $this->template->load('default', 'leads/create', $data);
    }

    /**
     * This function will be called by ajax request when the user scrolls to the
     * bottom of the page. The next set of results (50 at a time) will be loaded
     * and passed back to the view for the javascript to display.
     */
    public function paginate_leads_results() {
        if ($this->input->is_ajax_request()) {
            if ($_SESSION['page'] == "search") {
                $data = $_SESSION['search_leads_qry'];
            } else if ($_SESSION['page'] == "view") {
                $data = $_SESSION['view_leads_qry'];
            }
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
        if (isset($data['adv_search_switch'])) {
            $advSearch = $data['adv_search_switch'] == 'on' ? true : false;
        } else {
            $advSearch = false;
        }
        $validFields = array();
        foreach ($data as $key => $val) {
            if (!empty($val) && $val != 'no_selection_made' &&
                    (in_array($key, $this->_quickViewFields)) || !empty($val) && $val != 'no_selection_made' && $key != 'adv_search_switch' &&
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
            'acturis' => array('label' => 'Acturis Ref'),
            'lead_status' => array('label' => 'Prospect Status'),
            'costatus' => array('label' => 'Last Outcome'),
            'nextcall' => array('label' => 'Next Action'),
            'lead_source' => array('label' => 'Lead Source'),
            'other_lead_source' => array('label' => 'Other Lead Source'),
            'date_added' => array('label' => 'Date Added'),
            'date_updated' => array('label' => 'Date Updated'),
            'rep_group' => array('label' => 'Region', 'options' => $this->Leads_model->get_regions()),
            'prospector' => array('label' => 'Prospector', 'options' => $this->Leads_model->get_prospectors()),
            'manager' => array('label' => 'Manager / Executive', 'options' => $this->Leads_model->get_repgroup_managers($info['rep_group']))
        );

        foreach ($generalInfo as $key => &$val) {
            if (array_key_exists($key, $info)) {
                $val['value'] = $info[$key];
            }
        }
        //if the prospect is now owned then just remove it from the display
        if ($generalInfo['prospector']['value'] == "1") {
            $generalInfo['prospector']['value'] = "";
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
                'established' => array('label' => 'Established (YYYY)', 'type' => 'number', 'attr' => 'maxLength="4"'),
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

    public function load_policy_info_view() {
        if ($this->input->is_ajax_request()) {

            $urn = $this->input->post('urn');
            $options = $this->Leads_model->get_policy_options();



            $data = array(
                'urn' => $urn,
                'policies' => $this->Leads_model->get_policies($urn),
                'options' => $options
            );
            $this->load->view('leads/detail_views/policy_info', $data);
        }
    }

    /**
     * Accessed vua ajax request to update the company details
     */
    public function update_company_details() {
        if ($this->input->is_ajax_request()) {
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('', '');
            $this->form_validation->set_rules('established', 'Established', 'trim|numeric');

            if (!$this->form_validation->run()) {
                echo json_encode(array('success' => false, 'message' => validation_errors()));
                exit;
            }


            $historyData = array();
            $formData = array();
            foreach ($this->input->post() as $key => $val) {
 
                //the rest of the form only adds changes to the array if something was entered
                if (!empty($val) && trim($val) != '' && $val != 'no_selection_made') {

                    switch ($key) {
                        case 'nextcall':
                            $formData[$key] = $historyData['nextcall'] = to_mysql_datetime(trim($val));
                            break;

                        case 'comments':
                            $historyData['comments'] = trim($val);
                            break;

                        default:
                            $formData[$key] = trim($val);
                    }
                }
              
                   
              //if they dont have the acturis reference we set it as pending  
              if($this->input->post('acturis_later')):
                $formData['acturis']="Pending";
              unset($formData['acturis_later']);
              endif;
            }

            //$this->firephp->log($formData);

            if (empty($formData)) {
                echo json_encode(array('success' => true, 'message' => 'Nothing was updated.'));
                exit;
            }

            //we only want to log data changes, not updates to the lead status
            if (!array_key_exists('costatus', $formData)) {
                $logging = $this->Logging_model->log_codetails_updated($formData);
                $historyData['log_id'] = $logging;
                if ($logging) {
                    $this->History_model->log_codetails_updated($formData, $historyData);
                }
            } else {
                $this->History_model->log_lead_updated($formData, $historyData);
            }
            
            if(isset($formData['costatus'])){
            //certain outcomes will exclude the record
            $exclude_outcomes = array("Supression Request","Duplicate Record","Ceased Trading","Bankrupt");
            if(in_array($formData['costatus'],$exclude_outcomes)){
                $formData['lead_status']="Exclude";
                $formData['status_reason']=$formData['costatus'];
                $formData['acturis']=NULL;
            } else {
                $formData['lead_status']="Live";
            }
            
            //This outcome will send to telemarketing
            if($formData['costatus']=="Telemarketing Only"){
                $formData['prospector']="NULL";
                $formData['lead_status']="Exclude";
                $formData['acturis']=NULL;
                $formData['status_reason']=$formData['costatus'];
                $formData['costatus']="Transfer to telemarketing";
                $response = $this->Leads_model->transfer_telemarketing($formData['urn']);
                if ($response) {
                echo json_encode(array('success' => false, 'message' => $response));
                exit;
            }  
            }
            }
            
            if (!$this->Leads_model->update_company_details($formData)) {
                echo json_encode(array('success' => false, 'message' => 'Update failed. Please try again.'));
                exit;
            }

            echo json_encode(array('success' => true, 'message' => 'Update successful.', 'fields' => $formData));
            exit;
        }
    }

    public function update_policy() {
        if ($this->input->is_ajax_request()) {

            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('', '');

            $this->form_validation->set_rules('policy', 'Policy', 'trim|required');
            $this->form_validation->set_rules('renewal', 'Renewal Date', 'trim|required');
            $this->form_validation->set_rules('premium', 'Premium', 'trim|numeric');
            $this->form_validation->set_rules('insurer', 'Insurer', 'trim');
            $this->form_validation->set_rules('broker', 'Broker', 'trim');
            $this->form_validation->set_rules('urn', 'URN', 'trim|required|max_length[10]');

            if (!$this->form_validation->run()) {
                echo json_encode(array('success' => false, 'message' => validation_errors()));
                exit;
            }


            $renewalDate = $this->input->post('renewal');
            if (!date_is_future($renewalDate)) {
                echo json_encode(array('success' => false, 'message' => 'Invalid renewal date.'));
                exit;
            }

            $data = array(
                'urn' => $this->input->post('urn'),
                'id' => $this->input->post('id'),
                'type' => $this->input->post('policy'),
                'premium' => $this->input->post('premium'),
                'date' => to_mysql_datetime($renewalDate),
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
                $this->History_model->log_policy_added($data['urn'], $logging);
            } else if ($data['id'] > 0) {
                $logging = $this->Logging_model->log_policy_update($data);
                if (!$this->Leads_model->update_policy($data)) {
                    echo json_encode(array('success' => false, 'message' => 'Failed to update policy. Please try again.'));
                    exit;
                }
                if ($logging) {
                    $this->History_model->log_policy_updated($data['urn'], $logging);
                }
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
                echo json_encode(array('success' => false, 'message' => 'Invalid policy selection(s)', 'data' => $ids));
                exit;
            }
            $log = $this->Logging_model->log_policy_deleted(json_decode($ids, true));

            if (!$this->Leads_model->delete_policy(json_decode($ids, true))) {
                echo json_encode(array('success' => false, 'message' => 'Failed to delete policy selection(s). Please try again.'));
                exit;
            }

            $this->History_model->log_policy_deleted($this->input->post('urn'), count(json_decode($ids, true)), $log);

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
            $this->form_validation->set_rules('fax', 'Fax Number', 'trim|numeric');
            $this->form_validation->set_rules('firstname', 'First Name', 'trim|required');
            $this->form_validation->set_rules('keydm', 'Key Decision Maker', 'trim');
            $this->form_validation->set_rules('lastname', 'Last Name', 'trim|required');
            $this->form_validation->set_rules('mobile', 'Mobile', 'trim|numeric');
            $this->form_validation->set_rules('position', 'Position', 'trim');
            $this->form_validation->set_rules('positiondetails', 'Position Details', 'trim');
            $this->form_validation->set_rules('telephone', 'Telephone', 'trim|numeric');
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
                if ($logging) {
                    $this->History_model->log_contact_added($data['urn'], $logging);
                }
            } else if ($data['id'] > 0) {
                $logging = $this->Logging_model->log_contact_update($data);
                if (!$this->Leads_model->update_contact($data)) {
                    echo json_encode(array('success' => false, 'message' => 'Failed to update contact. Please try again.'));
                    exit;
                }
                $this->History_model->log_contact_updated($data['urn'], $logging);
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
                echo json_encode(array('success' => false, 'message' => 'Failed to delete contact(s). Please try again.', 'data' => $this->input->post()));
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

    public function broker() {
        if ($this->input->is_ajax_request()) {
            $brokers = $this->Leads_model->get_brokers($this->input->post('q'));
            if (count($brokers) > 0) {
                echo json_encode($brokers);
            }
        }
    }

    public function insurer() {
        if ($this->input->is_ajax_request()) {
            $insurers = $this->Leads_model->get_insurers($this->input->post('q'));
            if (count($insurers) > 0) {
                echo json_encode($insurers);
            }
        }
    }

}

/* End of file leads.php */
/* Location: ./application/controllers/leads.php */