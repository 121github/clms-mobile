<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Leads_model extends CI_Model {

  /**
   * This array acts as a lookup for input name -> actual field name & table 
   * used to construct the search leads query
   *
   * @var array $_fieldLookup
   */
  private $_fieldLookup = array(
      'urn' => array('field' => 'urn', 'table' => 'leads', 'exp' => "LIKE '%%%s%%'"),
      'prospector' => array('field' => 'prospector', 'table' => 'leads'),
      'coname' => array('field' => 'coname', 'table' => 'leads', 'exp' => "LIKE '%%%s%%'"),
      'postcode' => array('field' => 'p_postcode', 'table' => 'leads'),
      'postrange' => array('field' => '', 'table' => 'locations'),
      'postarea' => array('field' => 'p_postcode', 'table' => 'leads', 'exp' => "LIKE '%s%%'"),
      'acturis' => array('field' => 'acturis', 'table' => 'leads', 'exp' => "LIKE '%%%s%%'"),
      'lastname' => array('field' => 'lastname', 'table' => 'contacts', 'exp' => "LIKE '%%%s%%'"),
      'call_status' => array('field' => 'costatus', 'table' => 'leads'),
      'lead_status' => array('field' => 'lead_status', 'table' => 'leads'),
      'employees' => array('field' => 'employees', 'table' => 'leads'),
      'turnover' => array('field' => 'turnover', 'table' => 'leads'),
      'insurance_type' => array('field' => 'type', 'table' => 'renewals'),
      'lastcontact' => array('field' => 'contact', 'table' => 'history'),
      'renewal' => array('field' => 'renewal', 'table' => 'leads'),
      'leadadd' => array('field' => 'date_added', 'table' => 'leads'),
      'date_updated' => array('field' => 'date_updated', 'table' => 'leads'),
      'nextcontact' => array('field' => 'nextcall', 'table' => 'leads'),
      'region' => array('field' => 'rep_group', 'table' => 'leads'),
      'manager' => array('field' => 'manager', 'table' => 'leads'),
      'activity_origin' => array('field' => 'activity_originator', 'table' => 'leads'),
      'sector' => array('field' => 'cosector', 'table' => 'leads'),
      'lead_source' => array('field' => 'lead_source', 'table' => 'leads'),
      'quote_status' => array('field' => 'status', 'table' => 'quotes'),
  );

  /**
   *
   * Contains all the join information for the search leads qry builder
   * 
   * @var array
   */
  private $_joinLookup = array(
      'leads' => array(
          'contacts' => 'leads.urn = contacts.urn',
          'renewals' => 'leads.urn = renewals.urn',
          'history' => 'leads.urn = history.urn',
          'locations' => 'leads.p_postcode = locations.postcode'
      ),
      'contacts' => array(
          'leads' => 'contacts.urn = leads.urn',
          'renewals' => 'contacts.urn = renewals.urn',
          'history' => 'contacts.urn = history.urn'
      ),
      'renewals' => array(
          'leads' => 'renewals.urn = leads.urn',
          'contacts' => 'renewals.urn = contacts.urn',
          'history' => 'renewals.urn = history.urn',
          'quotes' => 'renewals.id = quotes.renewal'
      ),
      'history' => array(
          'leads' => 'history.urn = leads.urn',
          'renewals' => 'history.urn = renewals.urn',
          'leads' => 'history.urn = leads.urn'
      ),
      'quotes' => array(
          'renewals' => 'quotes.renewal = renewals.id'
      ),
      'locations' => array(
          'leads' => 'locations.postcode = leads.p_postcode'
      )
  );

  function __construct() {
    parent::__construct();
  }

  public function get_leads_pending_acturis() {
    return $this->db->get_where('leads', array('acturis' => 'Pending', 'lead_status' => 'Live'))->result_array();
  }

  public function get_lead_by_urn($urn) {
    return $this->db->get_where('leads', array('urn' => $urn))->result_array();
  }

  //the default query for the view page
  public function build_view_leads_qry($data) {

    $qry = "select l.urn,
            l.lead_status,
            l.prospector,
            l.rep_group AS region,
            IF(l.coname = '', 'Company name unavailable', l.coname) AS coname,
            l.cosector AS sector,
            l.p_postcode AS postcode,
            l.costatus AS quote_status,
            l.manager,
            l.nextcall AS nextcontact,
            date_format(l.nextcall,'%d/%m/%y') AS nextcontact_formatted,
            l.date_updated,
            date_format(l.date_updated,'%d/%m/%y') as date_updated_formatted,
            datediff(date(nextcall),curdate()) as days,
            nearest_renewal,
            (SELECT id FROM planner p WHERE p.manager = " . $this->db->escape($_SESSION['login']) . " AND p.urn = l.urn) AS plan_id ";
    if (isset($_SESSION['current_postcode'])) {
      $coords = postcode_to_coords($_SESSION['current_postcode']);
      $qry .= ",(((ACOS(SIN((" .
              $coords['lat'] . "*PI()/180)) * SIN((lo.lat*PI()/180))+COS((" .
              $coords['lat'] . "*PI()/180)) * COS((lo.lat*PI()/180)) * COS(((" .
              $coords['lng'] . "- lo.lng)*PI()/180))))*180/PI())*60*1.1515) AS distance ";
    }
    $qry .= " from leads l left join locations lo on l.p_postcode = lo.postcode left join (SELECT urn, min( `date` ) nearest_renewal
FROM renewals left join leads using(urn)
WHERE concat( year( curdate( ) ) , month( `date` ) , day( `date` ) ) > concat( year( curdate( ) ) , month( curdate( ) ) , day( curdate( ) ) ) and prospector is not null GROUP BY urn) nr on nr.urn = l.urn";
    $qry .= " where l.prospector is not null ";

    if ($data['order_by'] == "Last Action"):
      $qry .= " and date_updated is not null ";
      $qry .= " and nextcall is not null ";
      $order_by = " order by date_updated desc";
    elseif ($data['order_by'] == "Distance"):
      $qry .= " and nextcall is not null ";
      $qry .= " and p_postcode is not null ";
      $order_by = " order by distance asc";
    elseif ($data['order_by'] == "Renewal Date"):
      $qry .= " and nearest_renewal is not null ";
      //$qry .= " and nextcall is not null ";
      $order_by = " order by DATE_FORMAT(nearest_renewal,'%m%d')";
    else:
      $qry .= " and nextcall is not null ";
      $order_by = " order by nextcall asc";
    endif;

    if (!empty($data['prospector'])):
      $prospector = $data['prospector'];
      $qry .= " and prospector = '$prospector' ";
    endif;

    if (!empty($data['call_status'])):
      $costatus = $data['call_status'];
      $qry .= " and costatus = '$costatus' ";
    //by default it only shows live leads but they can select dead leads using the relevant costatus. eg. bankrupt.
    else:
      $qry .= " and lead_status = 'Live'";
    endif;

    $qry .= " group by urn ";

    $qry .= $order_by;

    $total = $this->db->query($qry)->num_rows();

    return array(
        'qry' => $qry,
        'total' => $total
    );
  }

  public function build_search_leads_qry($data) {
    $select = array(
        'leads.urn',
        'leads.prospector',
        'leads.rep_group AS region',
        'leads.date_updated',
        "IF(leads.coname = '', 'Company name unavailable', leads.coname) AS coname",
        'leads.cosector AS sector',
        'leads.p_postcode AS postcode',
        'leads.costatus AS quote_status',
        'leads.manager',
        'leads.lead_status',
        'leads.nextcall AS nextcontact',
        'date_format(leads.nextcall,"%d/%m/%y") AS nextcontact_formatted',
        'leads.date_updated',
        'date_format(leads.date_updated,"%d/%m/%y") as date_updated_formatted',
        'datediff(date(nextcall),curdate()) as days',
        "(SELECT id FROM planner WHERE planner.manager = " . $this->db->escape($_SESSION['login']) . " AND planner.urn = leads.urn) AS plan_id ",
        'nearest_renewal'
    );
    
    //Always select from the leads table
    $joinTables = array('leads');
    $where = $having = array();
    $exclude_nulls = '';
    //the default order

    $order_by = 'leads.coname asc';
    if (isset($data['order_by'])) {
      if ($data['order_by'] == "Distance") {
        $order_by = "distance asc";
        //if they order by distance then we need to set a default post range
        if (!isset($data['postrange'])) {
          $data['postrange'] = 100;
        }
      } else if ($data['order_by'] == "Next Action") {
        $order_by = "leads.nextcall ASC";
        $exclude_nulls = " and nextcall is not null";
      } else if ($data['order_by'] == "Last Action") {
        $order_by = "leads.date_updated ASC";
        $exclude_nulls = " and leads.date_updated is not null";
      } else if ($data['order_by'] == "Renewal Date") {
        $order_by = "DATE_FORMAT(nearest_renewal,'%m%d')";
        $exclude_nulls = " and nearest_renewal is not null";
      }
    }

    $i = 0;
    $keys = array_keys($data);
    $totalQry = null;

    foreach ($data as $key => $val) {

      $pieces = explode('_', $key);
      /*
       * Get the where conditions for date ranges.
       */
      if (isset($pieces[1]) && $pieces[1] == 'from' && array_key_exists($pieces[0], $this->_fieldLookup)) {

        $field = $this->_fieldLookup[$pieces[0]]['field'];
        $table = $this->_fieldLookup[$pieces[0]]['table'];
        $dateFrom = to_mysql_datetime($val);
        $dateTo = to_mysql_datetime($data[$keys[$i + 1]]);
        $where[] = "$table.$field >= " . $this->db->escape($dateFrom) . " AND $table.$field <= " . $this->db->escape($dateTo);
      } else if (array_key_exists($key, $this->_fieldLookup)) {

        $val = strtolower($val);
        $field = $this->_fieldLookup[$key]['field'];
        $table = $this->_fieldLookup[$key]['table'];

        /**
         * The postrange is set so we need to search by distance from
         * the supplied postcode.
         */
        if ($key === 'postrange') {
          $range = $this->db->escape($val);
          $coords = postcode_to_coords($data['postcode']);
          $select[] = "locations.postcode, locations.lat, locations.lng, (((ACOS(SIN((" .
                  $coords['lat'] . "*PI()/180)) * SIN((locations.lat*PI()/180))+COS((" .
                  $coords['lat'] . "*PI()/180)) * COS((locations.lat*PI()/180)) * COS(((" .
                  $coords['lng'] . "- locations.lng)*PI()/180))))*180/PI())*60*1.1515) AS distance";
          $having[] = "distance <= " . $range;

          if (!isset($data['order_by'])) {
            $order_by = " distance asc ";
          }


          /*
           * The qry to get the total results for this scenario is
           * different because of the distance calculation. the sprintf
           * function is used later to insert the join qry so have
           * placed '%s' after FROM so it can be inserted here.
           * Any additional WHERE conditions will also be added later.
           */
          $rad = intval($val) * 1.1515;
        } else if ($key === 'postcode' && !isset($data['postrange'])) {

          /*
           * The postcode is set but the postrange is not so strip the
           * space from the poscode and search whatever they have
           * given us. This means that they can supply a part postcode.
           */
          $val = str_replace(' ', '', $val);
          $where[] = "TRIM(LOWER(REPLACE($table.$field, ' ' , ''))) LIKE '" . $this->db->escape_like_str($val) . "%'";
        } else if ($key !== 'postcode') {

          /*
           * If a custom expression is given in the lookup use it,
           * otherwise assume that we want to use =
           */
          $exp = isset($this->_fieldLookup[$key]['exp']) ? sprintf($this->_fieldLookup[$key]['exp'], addslashes($val)) : "= " . $this->db->escape($val);
          $where[] = "TRIM(LOWER($table.$field)) $exp";
        }
      }

      //Keep track of which tables are required for joins
      if (!in_array($table, $joinTables)) {
        $joinTables[] = $table;
      }
      $i++;
    }

    /*
     * The quotes table can only join on the renewals table so if the 
     * quotes table is present & the renewals table is not, add the 
     * renewals table to the $joinTables array
     */
    if (in_array('quotes', $joinTables) && !in_array('renewals', $joinTables)) {
      $joinTables[] = 'renewals';
    }


    /*
     * Use the _joinLookup array to join the fields
     * that are able join on each other.
     */
    $joinQry = '';
    $joined = array();

    foreach ($joinTables as $table) {
      foreach ($this->_joinLookup[$table] as $joinTo => $joinOn) {
        if (in_array($joinTo, $joinTables) && !in_array($joinTo, $joined)) {
          $joinQry .= " LEFT JOIN " . $joinTo . " ON " . $joinOn;
          array_push($joined, $table, $joinTo);
        }
      }
    }
    
      $joinQry .= " left join (SELECT urn, min( `date` ) nearest_renewal
FROM renewals left join leads using(urn) where prospector is not null and concat( year( curdate( ) ) , month( `date` ) , day( `date` ) ) > concat( year( curdate( ) ) , month( curdate( ) ) , day( curdate( ) ) ) 
GROUP BY urn) nearest_renewals on nearest_renewals.urn = leads.urn";
    
    /*
     * Create the where & having parts of the query by imploding the arrays
     */
    $whereQry = " where leads.prospector is not null ";
    $whereQry .=!empty($where) ? '  and ' . implode(' AND ', $where) : '';
    //by default only show live leads unless a specific costatus is selected
    if (!isset($data['costatus']) && !isset($data['lead_status'])) {
      $whereQry .= " and lead_status = 'Live' ";
    }


    $whereQry .= $exclude_nulls;

    $radQry = !empty($having) ? " and " . $coords['lat'] . "
BETWEEN lat - " . $rad . "
AND lat + " . $rad . "
AND " . $coords['lng'] . " 
BETWEEN lng - " . $rad . "
AND lng + " . $rad . " and 
((((ACOS(SIN((" .
            $coords['lat'] . "*PI()/180)) * SIN((locations.lat*PI()/180))+COS((" .
            $coords['lat'] . "*PI()/180)) * COS((locations.lat*PI()/180)) * COS(((" .
            $coords['lng'] . "- locations.lng)*PI()/180))))*180/PI())*60*1.1515)) <= " . $range : '';


    //build the complete query  
    $full_qry = "SELECT " . implode(', ', $select) . " FROM leads $joinQry $whereQry $radQry group by urn ORDER BY $order_by";
    /*
     * return the query & the total number of results.
     */
    $total = $this->db->query($full_qry)->num_rows();

    return array(
        'qry' => $full_qry,
        'total' => $total
    );
  }

  /**
   *
   * Get a list of distinct insurance types from the renewals table
   *
   * @return array $types formatted for use as select options in the search
   * leads view
   */
  public function get_insurance_types() {
    $this->db->distinct();
    $this->db->select('prod1');
    $this->db->where("type = 'core'");
    $this->db->order_by('prod1', 'ASC');
    $query = $this->db->get('acturis_prodmap');
    $types = array();
    foreach ($query->result_array() as $row) {
      if (!is_numeric($row['prod1'])) {
        $types[] = $row['prod1'];
      }
    }
    return $types;
  }

  public function get_brokers($string) {
    $this->db->distinct();
    $this->db->select('broker');
    $this->db->like('broker', $string);
    $query = $this->db->get('brokers');
    $brokers = array();
    foreach ($query->result_array() as $row) {
      if (!is_numeric($row['broker'])) {
        $brokers[] = $row['broker'];
      }
    }
    return $brokers;
  }

  public function get_insurers($string) {
    $this->db->distinct();
    $this->db->select('insurer');
    $this->db->like('insurer', $string);
    $query = $this->db->get('insurers');
    $insurers = array();
    foreach ($query->result_array() as $row) {
      if (!is_numeric($row['insurer'])) {
        $insurers[] = $row['insurer'];
      }
    }
    return $insurers;
  }

  public function get_regions() {
    $this->db->distinct();
    $this->db->select('rep_group');
    $this->db->where("rep_group != '121' AND rep_group != 'octopus' AND rep_group != ''");
    $this->db->order_by('rep_group', 'ASC');
    $query = $this->db->get('users');
    $groups = array();
    foreach ($query->result_array() as $row) {
      $groups[] = $row['rep_group'];
    }
    return $groups;
  }

  public function get_repgroup_managers($repgroup = null) {
    $this->db->select('login, user');

    if (!is_null($repgroup)) {
      $this->db->where("rep_group = '$repgroup' AND active = 1 and login <> '".$_SESSION['login']."'");
    } else {
      $this->db->where("rep_group != '121' AND active = 1 and login <> '".$_SESSION['login']."'");
    }

    $this->db->order_by('user', 'ASC');

    $query = $this->db->get('users');

    $managers = array($_SESSION['login']);
    foreach ($query->result_array() as $row) {
      $managers[] = $row['login'];
    }
    return $managers;
  }

  public function get_prospectors() {
    $this->db->select('login, user');
    $this->db->where("rep_group = 'Prospects' AND active = 1");
    $this->db->order_by('user', 'ASC');
    $query = $this->db->get('users');

    $prospectors = array();
    foreach ($query->result_array() as $row) {
      $prospectors[] = $row['login'];
    }
    return $prospectors;
  }

  public function get_lead_source() {
    $this->db->distinct();
    $this->db->select('lead_source');
    $this->db->where("lead_source IS NOT NULL AND lead_source != '' AND lead_source NOT LIKE 'SBC%'");
    $this->db->order_by('lead_source', 'ASC');
    $query = $this->db->get('leads');
    $sources = array();
    foreach ($query->result_array() as $row) {
      $sources[] = $row['lead_source'];
    }
    return $sources;
  }

  public function get_general_info($urn) {
    $qry = "SELECT urn, acturis,prospector, rep_group, lead_source, other_lead_source, costatus,
                DATE_FORMAT(nextcall, '%d/%m/%Y') AS nextcall,
                DATE_FORMAT(date_added, '%d/%m/%Y') AS date_added,
                DATE_FORMAT(date_updated, '%d/%m/%Y') AS date_updated,
                lead_status, status_reason, manager
                FROM leads
                WHERE urn = '$urn'";
    $data = $this->db->query($qry)->result_array();

    if ($data && isset($data[0])) {
      return $data[0];
    }
    return false;
  }

  public function get_company_details($urn) {
    $qry = "SELECT coname, cotype, cofsa, cosector, other_cosector, 
                cotrades, p_add1, p_add2, p_add3, p_town, p_county, p_postcode, 
                p_country, s_add1, s_add2, s_add3, s_town, s_county, s_postcode, 
                s_country, turnover, turnover_validated, employees, employees_validated, established, website, branches, 
                specialist, franchise, director_info, sic_desc, consent_to_contact
                FROM leads
                WHERE urn = '$urn'";
    $data = $this->db->query($qry)->result_array();

    if ($data && isset($data[0])) {
      return $data[0];
    }
    return false;
  }

  public function get_company_appointments($urn) {
    $this->db->select(
            array('id', 'attendee', 'begin_date', 'begin_hour', 'end_hour', 'begin_mins',
                'end_mins','cancelled', 'title', 'text', 'urn', 'manager', 'status', 'set_by')
    );
    $this->db->from('events');
    $this->db->join('events_attendees', 'events.id = events_attendees.event_id', 'left');
    $this->db->where('urn',$urn);
    //$this->db->where('status !=','rejected');
    $attendees = array();
    $array = array();
    foreach ($this->db->get()->result_array() as $appointment) {
      $array[$appointment['id']] = $appointment;
      $attendees[$appointment['id']][] = $appointment['attendee'];
      $array[$appointment['id']]['attendees'] = $attendees[$appointment['id']];
    }

    return $array;
  }

  public function get_contacts($urn) {
    $this->db->select(
            array('id', 'urn', 'priority', 'title', 'firstname', 'lastname',
                'position', 'other_position', 'positiondetails', 'telephone',
                'mobile', 'work', 'fax', 'email', 'keydm')
    );
    $this->db->where(array('urn' => $urn));
    $this->db->order_by('priority', 'ASC');
    return $this->db->get('contacts')->result_array();
  }

  public function get_history($urn) {
    $qry = "SELECT id, date_format(contact,'%d/%m/%Y %H:%i') AS fmtcontact,
                description, status, comments,
                date_format(nextcall,'%d/%m/%Y') AS nextcall,log_id
                FROM history
                WHERE urn = '$urn'
                AND status != 'Sent to 121'
                ORDER BY contact DESC";
    return $this->db->query($qry)->result_array();
  }

  public function update_company_details($data) {
    //if no prospector is assigned then assign it to the person that is updating the record
    $this->db->where(array('urn' => $data['urn'], 'prospector' => '1'));
    $this->db->update('leads', array('prospector' => $_SESSION['login']));
    
    $data['date_updated'] = date('Y-m-d H:i:s');
    
    $this->db->where(array('urn' => $data['urn']));
    return $this->db->update('leads', $data);
  }

  public function insert_contact(&$insert) {
    $res = $this->db->insert('contacts', $insert);
    if (!$res) {
      return false;
    }
    $insert['id'] = $this->db->insert_id();
    return $insert['id'];
  }

  public function insert_policy(&$insert) {
    $res = $this->db->insert('renewals', $insert);
    if (!$res) {
      return false;
    }
    $insert['id'] = $this->db->insert_id();
    $res = $this->db->insert('renewal_ownership', array("renewal_id"=>$insert['id'],"prospector"=>$_SESSION['login']));
    return $insert['id'];
  }

  public function update_policy(&$data) {
    $this->db->where('id', $data['id']);
    if ($this->db->update('renewals', $data)) {
      return $data['id'];
    }
  }

  public function update_contact($data) {
    $this->db->where('id', $data['id']);
    if ($this->db->update('contacts', $data)) {
      return $data['id'];
    }
  }

  public function delete_contacts($ids) {
    $this->db->where_in('id', $ids);
    if ($this->db->delete('contacts')) {
      return $ids;
    }
  }

  public function delete_policy($ids) {
      $this->db->where_in('renewal_id', $ids);
      $this->db->delete('renewal_ownership');
      $this->db->where_in('id', $ids);
    if ($this->db->delete('renewals')) {
      return $ids;
    }
  }

  public function get_policies($urn) {
    $qry = "SELECT *,DATE_FORMAT(`date`,'%d/%m/%Y') AS `date`
                FROM renewals WHERE urn = '$urn'";
    return $this->db->query($qry)->result_array();
  }

  public function get_policy_options() {
    $this->db->distinct();
    $this->db->select('prod1');
    $query = $this->db->get('acturis_prodmap');
    $options = array();
    foreach ($query->result_array() as $row) {
      $options[] = $row['prod1'];
    }
    return $options;
  }

  public function get_new_urn() {
    $this->db->select('urn');
    $this->db->like('urn', 'PRO', 'after');
    $this->db->order_by('urn', 'desc');
    $this->db->limit(1);
    $result = $this->db->get('leads')->row()->urn;
    if (!empty($result)) {
      $urn = substr($result, 3);

      $urn++;
    } else {
      $urn = 1;
    }

    $newurn = "PRO" . str_pad($urn, 6, "0", STR_PAD_LEFT);
    return $newurn;
  }

  public function create_prospect($data = array()) {
    $this->db->insert("leads", $data);
    $log_id = $this->db->insert_id();
    return $log_id;
  }

  public function get_duplicates($urn = "") {
    $this->db->select('leads.urn,coname,p_postcode,firstname,lastname,telephone,mobile');
    $this->db->from('leads');
    $this->db->join('contacts', 'leads.urn=contacts.urn','left');
    $this->db->where('leads.urn', $urn);
    $row = $this->db->get()->row();
    $firstname = $this->db->escape_str($row->firstname);
    $lastname = $this->db->escape_str($row->lastname);
    $coname = preg_replace("/[^A-Za-z0-9]/", '', $row->coname);
    $postcode = $this->db->escape_str(substr(str_replace(" ", "", $row->p_postcode), 0, -3));
    $telephone = $this->db->escape_str(trim($row->telephone));
    $mobile = $this->db->escape_str(trim($row->mobile));

//check the leads table for matches
    $qry_leads = "select leads.urn,coname,p_add1,p_postcode,firstname,lastname,telephone,lead_status as type from leads left join contacts on leads.urn=contacts.urn where leads.urn <> '$urn' and ( ";
    if (!empty($coname)) {
      $qry_leads .= " replace(coname,' ','') = '$coname'  ";
    }
    if (!empty($postcode) && !empty($firstname) && !empty($lastname)) {
      $qry_leads .= " or (trim(replace(p_postcode,substring(p_postcode,-3),'')) = '$postcode' and concat(firstname,lastname) = '$firstname$lastname')  ";
    }
    if (!empty($telephone)) {
      $qry_leads .= " or telephone='$telephone' ";
    }
    if (!empty($mobile)) {
      $qry_leads .= " or mobile='$mobile' ";
    }
    $qry_leads .= " ) limit 5";
    //$this->firephp->log($qry);
    $leads_result = $this->db->query($qry_leads)->result_array();

//check the live policy table for matches
    $qry_live = "select clientkey as urn,coname,add1,postcode,forename,surname,homephone,prodtarget as type from acturis_live where (";
    if (!empty($coname)) {
      $qry_live .= " replace(coname,' ','') = '$coname'  ";
    }
    if (!empty($postcode) && !empty($firstname) && !empty($lastname)) {
      $qry_live .= " or (postcode = '$postcode' and concat(forename,forename) = '$firstname$lastname') ";
    }

    if (!empty($telephone)) {
      $qry_live .= " or homephone='$telephone' ";
    }
    if (!empty($telephone)) {
      $qry_live .= " or workphone='$telephone' ";
    }
    if (!empty($mobile)) {
      $qry_live .= " or mobile='$mobile' ";
    }
    $qry_live .= " )  limit 5";
    $live_result = $this->db->query($qry_live)->result_array();
//put the results together into a single array      
    foreach ($leads_result as $v) {
      $live_result[] = $v;
    }

    return $live_result;
  }

  public function get_local($urn = "") {
    $this->db->select('p_postcode');
    $this->db->from('leads');
    $this->db->where('leads.urn', $urn);
    $row = $this->db->get()->row();

    $postcode = substr(str_replace(" ", "", $row->p_postcode), 0, -3);

    //check the leads table for matches
    $qry = "select clientkey,coname,add1 as p_add1,postcode as p_postcode,forename as firstname,surname as lastname,prodtarget as type,count(*),concat(forename,surname) as gp from acturis_live where trim(replace(postcode,substring(postcode,-3),'')) = '$postcode' and coname <> '' and addon = 'No' group by gp limit 5";
    $result = $this->db->query($qry)->result_array();
    //$this->firephp->log($postcode);
    //$this->firephp->log($result);
    return $result;
  }

//this function is ran if an update fails to prevent it being added to history
  public function clean_db($logging = "1") {
    if (intval($logging) > 1) {
      $this->db->delete('history', array('log_id' => $logging));
      $this->db->delete('prospector_log', array('id' => $logging));
      $this->db->delete('prospector_log_data', array('id' => $logging));
    }
  }

  public function transfer_telemarketing($urn) {
    //get lead details 
    $lead_detail_q = "select * from leads where urn='$urn' ";
    $lead_array = $this->db->query($lead_detail_q)->result_array();
    $lead_detail = $lead_array[0];
    //get contact details
    $contact_detail_q = "select * from contacts where urn='$urn' ";
    $contact_array = $this->db->query($contact_detail_q)->result_array();
    $contact_detail = $contact_array[0];
    //get renewals
    $renewal_q = "select * from renewals where urn='$urn' ";
    $renewal_array = $this->db->query($renewal_q)->result_array();

    $i = 0;
    foreach ($renewal_array as $row) {
      $i++;
      $renewal_detail[$i]['date'] = $row['date'];
      $renewal_detail[$i]['type'] = $row['type'];
    }
    $query = "insert into `db-clmstotelemarketing` 
            SET urn = '{$urn}',
            company = '" . mysql_escape_string($lead_detail['coname']) . "',
            title = '" . mysql_escape_string($contact_detail['title']) . "',
            forename = '" . mysql_escape_string($contact_detail['firstname']) . "',
            surname = '" . mysql_escape_string($contact_detail['lastname']) . "',
            add1 = '" . mysql_escape_string($lead_detail['p_add1']) . "',
            add2 = '" . mysql_escape_string($lead_detail['p_add2']) . "',
            add3 = '" . mysql_escape_string($lead_detail['p_add3']) . "',
            town = '" . mysql_escape_string($lead_detail['p_town']) . "',
            county = '" . mysql_escape_string($lead_detail['p_county']) . "',
            postcode = '" . mysql_escape_string($lead_detail['p_postcode']) . "',
            position = '" . mysql_escape_string($contact_detail['position']) . "',
            position2 = '" . mysql_escape_string($contact_detail['other_position']) . "',
            position_desc = '" . mysql_escape_string($contact_detail['positiondetails']) . "',
            sector='',sector2='',
            website= '" . mysql_escape_string($lead_detail['website']) . "',
            phone = '" . mysql_escape_string($contact_detail['telephone']) . "',
            mobile = '" . mysql_escape_string($contact_detail['mobile']) . "',
            email = '" . mysql_escape_string($contact_detail['email']) . "',
            renewal  = '" . mysql_escape_string($renewal_detail[1]['date']) . "',
            renewaltype  = '" . mysql_escape_string($renewal_detail[1]['type']) . "',
            renewal2  = '" . mysql_escape_string($renewal_detail[2]['date']) . "',
            renewaltype2  = '" . mysql_escape_string($renewal_detail[2]['type']) . "',
            renewal3  = '" . mysql_escape_string($renewal_detail[3]['date']) . "',
            renewaltype3  = '" . mysql_escape_string($renewal_detail[3]['type']) . "',
            source='CLMS', transferno='08009542935', dials='0',`exit`='',postdist='',branchno='',branchdesc='',pmc_transferno='',hh_transferno='',cv_transferno='',
            comments='Transfer from CLMS'";

    // Connect to one2one Data Centre
    $db_connect = mysql_connect('84.19.44.186', 'clms', base64_decode('Q0xtc2lOZjA=')) or die(mysql_error());
    $db_database = mysql_select_db("calldev", $db_connect);
    $result = mysql_query($query, $db_connect);

    if (!mysql_errno()) {
      mail("jon-man@121customerinsight.co.uk,dougf@121customerinsight.co.uk", "CLMS - Transfer to telemarketing", $query);
    } else if (mysql_errno() == 1062) {
      return "Record already with telemarketing";
    } else {
      return "Unable to send to telemarketing";
    }
  }

  public function check_duplicates($post) {
    //strip all non alphanumeric chars from postcode
    $coname = preg_replace("/[^A-Za-z0-9]/", '', $post['coname']);
    $postcode = preg_replace("/[^A-Za-z0-9]/", '', $post['postcode']);
    $telephone = preg_replace("/[^A-Za-z0-9]/", '', $post['telephone']);
    $dupes = array();

    //check the CLMS leads table for duplicates first
    $qry1 = "select urn,coname,date_format(date_updated,'%d/%m/%y') as date_updated from leads left join contacts using(urn) where concat(replace(coname,' ',''),replace(p_postcode,' ','')) = concat('$coname','$postcode')";
    if (!empty($telephone)) {
      $qry1 .= " or (replace(telephone,' ','') = '$telephone' or replace(mobile,' ','') = '$telephone') ";
    }
    $qry1 .= " and prospector is null";
    $result1 = $this->db->query($qry1)->result_array();
    foreach ($result1 as $row) {
      $dupes[] = array("type" => "CLMS", "id" => $row['urn'], "coname" => $row['coname'],"last_updated" => $row['date_updated']);
    }

    //check the CLMS leads table for duplicates first
    $qry2 = "select urn,coname,date_format(date_updated,'%d/%m/%y') as date_updated from leads left join contacts using(urn) where concat(replace(coname,' ',''),replace(p_postcode,' ','')) = concat('$coname','$postcode') ";

    if (!empty($telephone)) {
      $qry2 .= " or (replace(telephone,' ','') = '$telephone' or replace(mobile,' ','') = '$telephone') ";
    }
    $qry2 .= " and prospector is not null";
    $result2 = $this->db->query($qry2)->result_array();
    foreach ($result2 as $row) {
      $dupes[] = array("type" => "Prospector", "id" => $row['urn'], "coname" => $row['coname'],"last_updated" => $row['date_updated']);
    }
    //check the acturis clients table
    $qry3 = "select clientkey,coname from acturis_clients where concat(replace(coname,' ',''),replace(postcode,' ','')) = concat('$coname','$postcode')";
    if (!empty($telephone)) {
      $qry3 .= " or (replace(homephone,' ','') = '$telephone' or replace(mobile,' ','') = '$telephone') ";
    }

    $result3 = $this->db->query($qry3)->result_array();

    foreach ($result3 as $row) {
      $dupes[] = array("type" => "CPH", "id" => $row['clientkey'], "coname" => $row['coname'],"last_updated"=>"Live Customer");
    }
    if (count($dupes) > 0) {
      return $dupes;
      exit;
    }
    return false;
  }

  public function get_coname($urn){
    $this->db->select('coname');
    $this->db->from('leads');
    $this->db->where('leads.urn', $urn);
    return $this->db->get()->row('coname');
  }
  
   public function reset_record($urn){
     $data = array(
               'prospector' => $_SESSION['login'],
               'manager' => '',
               'nextcall' => null,
               'costatus' => 'New',
               'status_reason' => 'Reset Record',
               'date_updated' => date('Y-m-d H:i:s'),
               'lead_status' => 'Live',
               'activity_originator' => 'Prospectors',
            );
    $this->db->where('urn', $urn);
    $this->db->update('leads',$data);
    
    $history =   array('urn' => $urn,
                       'description' => $_SESSION['login'] . ' reset the record',
                       'contact' => date('Y-m-d H:i:s'),
                       'status' => 'Record Reset',
                       'user' => $_SESSION['login'],
                       'user_group' => 'Prospects',
                       'log_id' => null);
    $this->db->insert('history',$history);
  }
  
}
