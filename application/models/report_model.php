<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Report_model extends CI_Model {

  function __construct() {
    parent::__construct();
  }

  public function get_mi_data($filter) {

    $prospector = $this->db->escape($filter['prospector']);

    $dateFrom = $this->db->escape(to_mysql_datetime($filter['date_from']));
    $dateTo = $this->db->escape(to_mysql_datetime($filter['date_to']));

    $firstVisitsQry = "SELECT user,count(distinct urn) as first_visits
                           FROM history
                           WHERE user_group = 'Prospects'
                           and user  = $prospector
                           GROUP BY urn HAVING min( contact ) BETWEEN $dateFrom AND $dateTo";
    $firstVisits = $this->db->query($firstVisitsQry)->num_rows();

/*
    $totalVisitsQry = "SELECT user,urn,date(contact) as `date`,count(urn) as visits 
      FROM history WHERE user_group = 'Prospects' 
      AND user = $prospector GROUP BY concat( urn, date( contact ) ) 
      HAVING min( contact ) BETWEEN $dateFrom AND $dateTo";
    $totalVisits = $this->db->query($totalVisitsQry)->num_rows();
*/
    
    $allVisitsQry = "SELECT user,urn
                           FROM history
                           WHERE user_group = 'Prospects'
                           and user  = $prospector  group by urn having min(contact) between $dateFrom and $dateTo
                           ";

    $allVisits = $this->db->query($allVisitsQry)->result_array();

    $counts = array(
        'prospector' => $filter['prospector'],
        'first_visits' => 0,
        'renewal_dates' => 0,
        'policy_types' => 0,
        'policy_brokers' => 0,
        'policy_insurers' => 0,
        'policy_premiums' => 0,
        'turnover' => 0,
        'turnover_validations' => 0,
        'employees' => 0,
        'employee_validations' => 0,
        'consent_to_contacts' => 0,
        'bde_appointments' => 0,
        'cae_appointments' => 0,
        'total_visits' => 0
    );

    foreach ($allVisits as $visit) {


      $tmpCounts = $this->get_mi_counts($visit['urn'], $visit['user']);
      $counts['first_visits'] = $firstVisits;
      $counts['renewal_dates'] += $tmpCounts[0]['renewal_dates'];
      $counts['policy_types'] += $tmpCounts[0]['policy_types'];
      $counts['policy_brokers'] += $tmpCounts[0]['policy_brokers'];
      $counts['policy_insurers'] += $tmpCounts[0]['policy_insurers'];
      $counts['policy_premiums'] += $tmpCounts[0]['policy_premiums'];
      $counts['turnover'] += $tmpCounts[0]['turnover'];
      $counts['turnover_validations'] += $tmpCounts[0]['turnover_validations'];
      $counts['employees'] += $tmpCounts[0]['employees'];
      $counts['employee_validations'] += $tmpCounts[0]['employee_validations'];
      $counts['consent_to_contacts'] += $tmpCounts[0]['consent_to_contacts'];
      $counts['bde_appointments'] += $tmpCounts[0]['bde_appointments'];
      $counts['cae_appointments'] += $tmpCounts[0]['cae_appointments'];
      $counts['total_visits'] += $tmpCounts[0]['total_visits'];
    }
//$this->firephp->log($counts);
    return $counts;
  }

  public function get_mi_counts($urn, $user) {
    /*this is the old renewal_dates query, it looks at renewal inserts in the logs where the renewal id is still in the renewals table. so it ignores deleted renewals
     * 
     * SELECT count( * )
                FROM prospector_log
                LEFT JOIN prospector_log_data ON prospector_log.id = prospector_log_data.id
                WHERE prospector_log.urn = '$urn'
                AND user = '$user'
                AND prospector_log.change_table = 'renewals'
                and action = 'insert'
                AND prospector_log_data.change_field = 'date' and table_id in(select id from renewals left join renewal_ownership on id=renewal_ownership.renewal_id where urn = '$urn' and prospector = '$user')
           */             
    
    
    $countsQry =
            "SELECT 
            (
                SELECT count( * )
                FROM renewals left join renewal_ownership on id=renewal_ownership.renewal_id where urn = '$urn' and prospector = '$user'
            ) AS renewal_dates,
            (
                SELECT count( * )
                FROM prospector_log
                LEFT JOIN prospector_log_data ON prospector_log.id = prospector_log_data.id
                WHERE prospector_log.urn = '$urn'
                AND user = '$user'
                AND prospector_log.change_table = 'renewals'
                AND prospector_log_data.change_field = 'type'
            ) AS policy_types,
            (
                SELECT count( * )
                FROM prospector_log
                LEFT JOIN prospector_log_data ON prospector_log.id = prospector_log_data.id
                WHERE prospector_log.urn = '$urn'
                AND user = '$user'
                AND prospector_log.change_table = 'renewals'
                AND prospector_log_data.change_field = 'broker'
            ) AS policy_brokers,
            (
                SELECT count( * )
                FROM prospector_log
                LEFT JOIN prospector_log_data ON prospector_log.id = prospector_log_data.id
                WHERE prospector_log.urn = '$urn'
                AND user = '$user'
                AND prospector_log.change_table = 'renewals'
                AND prospector_log_data.change_field = 'insurer'
            ) AS policy_insurers,
            (
                SELECT count( * )
                FROM prospector_log
                LEFT JOIN prospector_log_data ON prospector_log.id = prospector_log_data.id
                WHERE prospector_log.urn = '$urn'
                AND user = '$user'
                AND prospector_log.change_table = 'renewals'
                AND prospector_log_data.change_field = 'premium'
            ) AS policy_premiums,
            (
                SELECT count( * )
                FROM prospector_log
                LEFT JOIN prospector_log_data ON prospector_log.id = prospector_log_data.id
                WHERE prospector_log.urn = '$urn'
                AND user = '$user'
                AND prospector_log.change_table = 'leads'
                AND prospector_log_data.change_field = 'turnover'
            ) AS turnover,
            (
                SELECT IF(count( * ) > 1, 1, count( * ))
                FROM prospector_log
                LEFT JOIN prospector_log_data ON prospector_log.id = prospector_log_data.id
                WHERE prospector_log.urn = '$urn'
                AND user = '$user'
                AND prospector_log.change_table = 'leads'
                AND prospector_log_data.change_field = 'turnover_validated'
            ) AS turnover_validations,
            (
                SELECT count( * )
                FROM prospector_log
                LEFT JOIN prospector_log_data ON prospector_log.id = prospector_log_data.id
                WHERE prospector_log.urn = '$urn'
                AND user = '$user'
                AND prospector_log.change_table = 'leads'
                AND prospector_log_data.change_field = 'employees'
            ) AS employees,
            (
                SELECT IF(count( * ) > 1, 1, count( * ))
                FROM prospector_log
                LEFT JOIN prospector_log_data ON prospector_log.id = prospector_log_data.id
                WHERE prospector_log.urn = '$urn'
                AND user = '$user'
                AND prospector_log.change_table = 'leads'
                AND prospector_log_data.change_field = 'employees_validated'
            ) AS employee_validations,
            (
                SELECT IF(count( * ) > 1, 1, count( * ))
                FROM prospector_log
                LEFT JOIN prospector_log_data ON prospector_log.id = prospector_log_data.id
                WHERE prospector_log.urn = '$urn'
                AND user = '$user'
                AND prospector_log.change_table = 'leads'
                AND prospector_log_data.change_field = 'consent_to_contact'
            ) AS consent_to_contacts,
            (
                SELECT count( * ) FROM events 
                WHERE urn = '$urn' 
                AND set_by = '$user'
                AND  `status` = 'BDE'
            ) AS bde_appointments,
                
                (
                SELECT count( * ) FROM events 
                WHERE urn = '$urn' 
                AND set_by = '$user'
                AND  `status` = 'Live'
            ) AS cae_appointments,
                
                (
                SELECT count(distinct date(contact)) FROM history 
                WHERE urn = '$urn' 
                AND user = '$user'
            ) AS total_visits";
    return $this->db->query($countsQry)->result_array();
  }

  public function load_tracking_data($prospector, $dateFrom, $dateTo) {

    $data = array();
    $qry = "SELECT id, lat, lng, postcode, locality, DATE_FORMAT(timestamp, '%d %b %Y') AS date, DATE_FORMAT(timestamp, '%h:%i %p') AS time,`timestamp`,date(timestamp) as day"
            . " FROM user_locations"
            . " WHERE user = " . $this->db->escape($prospector)
            . " AND (DATE(timestamp) BETWEEN " . $this->db->escape($dateFrom) . " AND " . $this->db->escape($dateTo) . ")"
            . " ORDER BY timestamp DESC";
    $result = $this->db->query($qry)->result_array();

    $numLocations = count($result) - 1;
    $data['total_dist'] = 0;
    $data['total_loc'] = count($result);
    $result[$numLocations]['duration'] = "-";
    for ($i = $numLocations; $i >= 0; $i--) {
      if ($i < $numLocations) {
        if ($result[$i]['day'] === $result[$i + 1]['day']) {
          $result[$i]['duration'] = rtrim(time_elapsed_string(strtotime($result[$i + 1]['timestamp']), strtotime($result[$i]['timestamp'])), "ago");
        } else {
          $result[$i]['duration'] = "-";
        }
      }


      $result[$i]['distance'] = $i !== $numLocations ? round(distance($result[$i + 1]['lat'], $result[$i + 1]['lng'], $result[$i]['lat'], $result[$i]['lng']), 2) : $result[$i]['distance'] = 0;
      $data['total_dist'] += $result[$i]['distance'];
    }

    $data['locations'] = $result;

    return $data;
  }

  function dateRange($first, $last, $step = '-1 day', $format = 'Y-m-d') {

    $dates = array();
    $current = strtotime($first);
    $last = strtotime($last);

    while ($current >= $last) {

      $dates[date($format, $current)] = array();
      $current = strtotime($step, $current);
    }

    return $dates;
  }

  public function get_full_activity($options) {


    $agent = $options["agent"];
    $from = $options["from"];
    $to = $options["to"];
    $dates = $options["dates"];


    $qry = "SELECT `date`, change_table, count( * ) count
                FROM (
                SELECT date(`timestamp`) as `date`, change_table, table_id, count( * ) count
                FROM prospector_log LEFT JOIN prospector_log_data
                USING ( id ) where date(`timestamp`) between '$from' and '$to' and user='$agent'
                GROUP BY `date`, change_table, table_id
                )ass
                GROUP BY `date`, change_table";

    $result = $this->db->query($qry)->result_array();
    $temp = array();
    $tables = array("leads" => "0", "contacts" => "0", "renewals" => "0");
    $new = array();
    foreach ($result as $v) {
      $temp[$v["date"]][$v["change_table"]] = $v["count"];
      $temp[$v["date"]]["formatted_date"] = date('d/m/y', strtotime($v["date"]));
    }

    foreach ($temp as $k => $v) {
      $new[$k] = array_merge($tables, $temp[$k]);
    }

    foreach ($dates as $k => $v) {
      $dates[$k] = $tables;
      $dates[$k]["formatted_date"] = date('d/m/y', strtotime($k));
    }

    foreach ($new as $k => $v) {
      $dates[$k] = $v;
    }

    return $dates;
  }

  public function get_contact_activity($options) {

    $agent = mysql_real_escape_string($options["agent"]);
    $from = mysql_real_escape_string($options["from"]);
    $to = mysql_real_escape_string($options["to"]);
    $prospector = $options["prospector"];

    $urns = array();
    //add the urns that had an contact updated
    $updated_urns = "SELECT table_id,pl.urn,concat(firstname,' ',lastname) name,count(*) count from prospector_log pl left join prospector_log_data pld on pl.id = pld.id left join contacts l on l.id = pld.table_id where date(`timestamp`) between '$from' and '$to' and user='$agent' and change_table ='contacts' and action = 'update' group by pl.urn";

    //$this->firephp->log($updated_urns);

    foreach ($this->db->query($updated_urns)->result_array() as $v) {
      $urns[$v['table_id']]["updated"] = $v['count'];
      $urns[$v['table_id']]["name"] = $v['name'];
      $urns[$v['table_id']]["urn"] = $v['urn'];
    }
    //add the urns that had an contact inserts
    $inserted_urns = "SELECT table_id,pl.urn,concat(firstname,' ',lastname) name,count(distinct pl.id) count from prospector_log pl left join prospector_log_data pld on pl.id = pld.id left join contacts l on l.id = pld.table_id where date(`timestamp`) between '$from' and '$to' and user='$agent' and change_table ='contacts' and action = 'insert' group by pl.urn";
    foreach ($this->db->query($inserted_urns)->result_array() as $v) {
      $urns[$v['table_id']]["inserted"] = $v['count'];
      $urns[$v['table_id']]["name"] = $v['name'];
      $urns[$v['table_id']]["urn"] = $v['urn'];
    }

    //add the urns that had an contact deleted
    $deleted_urns = "SELECT table_id,pl.urn,old_val name,count(distinct pl.id) count from prospector_log pl left join prospector_log_data pld on pl.id = pld.id where date(`timestamp`) between '$from' and '$to' and user='$agent' and change_table ='contacts' and action = 'delete' group by pl.urn";

    foreach ($this->db->query($deleted_urns)->result_array() as $v) {
      $urns[$v["table_id"]]["deleted"] = $v['count'];
      $urns[$v['table_id']]["name"] = $v['name'];
      $urns[$v['table_id']]["urn"] = $v['urn'];
    }

    foreach ($urns as $k => $v) {
      if (!array_key_exists("updated", $v)) {
        $urns[$k]["updated"] = 0;
      }
      if (!array_key_exists("inserted", $v)) {
        $urns[$k]["inserted"] = 0;
      }
      if (!array_key_exists("deleted", $v)) {
        $urns[$k]["deleted"] = 0;
      }
    }
    //$this->firephp->log($urns);
    return $urns;
  }

  public function get_company_activity($options) {

    $agent = $options["agent"];
    $from = $options["from"];
    $to = $options["to"];


    $urns = array();
    //add the urns that had an contact updated
    $updated_urns = "SELECT table_id urn,coname,count(*) count from prospector_log pl left join prospector_log_data pld on pl.id = pld.id left join leads l on l.urn = pld.table_id where date(`timestamp`) between '$from' and '$to' and user='$agent' and change_table ='leads' and action = 'update' group by table_id";
    //$this->firephp->log($updated_urns);
    foreach ($this->db->query($updated_urns)->result_array() as $v) {
      $urns[$v['urn']]["updated"] = $v['count'];
      $urns[$v['urn']]["name"] = $v['coname'];
      $urns[$v['urn']]["urn"] = $v['urn'];
    }
    //add the urns that had an contact inserts
    $inserted_urns = "SELECT table_id urn,coname,count(*) count from prospector_log pl left join prospector_log_data pld on pl.id = pld.id left join leads l on l.urn = pld.table_id where date(`timestamp`) between '$from' and '$to' and user='$agent' and change_table ='leads' and action = 'insert' group by table_id";
    foreach ($this->db->query($inserted_urns)->result_array() as $v) {
      $urns[$v['urn']]["inserted"] = $v['count'];
      $urns[$v['urn']]["name"] = $v['coname'];
      $urns[$v['urn']]["urn"] = $v['urn'];
    }

    //add the urns that had an contact deleted
    $deleted_urns = "SELECT table_id urn,coname,count(*) count from prospector_log pl left join prospector_log_data pld on pl.id = pld.id left join leads l on l.urn = pld.table_id where date(`timestamp`) between '$from' and '$to' and user='$agent' and change_table ='leads' and  action = 'delete' group by table_id";

    foreach ($this->db->query($deleted_urns)->result_array() as $v) {
      $urns[$v["urn"]]["deleted"] = $v['count'];
      $urns[$v['urn']]["name"] = $v['coname'];
      $urns[$v['urn']]["urn"] = $v['urn'];
    }

    foreach ($urns as $k => $v) {
      if (!array_key_exists("updated", $v)) {
        $urns[$k]["updated"] = 0;
      }
      if (!array_key_exists("inserted", $v)) {
        $urns[$k]["inserted"] = 0;
      }
      if (!array_key_exists("deleted", $v)) {
        $urns[$k]["deleted"] = 0;
      }
    }
   // $this->firephp->log($urns);
    return $urns;
  }

  public function get_policy_activity($options) {

    $agent = $options["agent"];
    $from = $options["from"];
    $to = $options["to"];


    $urns = array();
    //add the urns that had an contact updated
    $updated_urns = "SELECT l.urn,type,count(*) count from prospector_log pl left join prospector_log_data pld on pl.id = pld.id left join renewals l on l.id = pld.table_id where date(`timestamp`) between '$from' and '$to' and user='$agent' and change_table ='renewals' and action = 'update' group by urn";
    //$this->firephp->log($updated_urns);
    foreach ($this->db->query($updated_urns)->result_array() as $v) {
      $urns[$v['urn']]["updated"] = $v['count'];
      $urns[$v['urn']]["name"] = $v['type'];
      $urns[$v['urn']]["urn"] = $v['urn'];
    }
    //add the urns that had an contact inserts
    $inserted_urns = "SELECT l.urn,type,count(*) count from prospector_log pl left join prospector_log_data pld on pl.id = pld.id left join renewals l on l.id = pld.table_id where date(`timestamp`) between '$from' and '$to' and user='$agent' and change_table ='renewals' and action = 'insert' group by urn";
    foreach ($this->db->query($inserted_urns)->result_array() as $v) {
      $urns[$v['urn']]["inserted"] = $v['count'];
      $urns[$v['urn']]["name"] = $v['type'];
      $urns[$v['urn']]["urn"] = $v['urn'];
    }

    //add the urns that had an contact deleted
    $deleted_urns = "SELECT l.urn,type,count(*) count from prospector_log pl left join prospector_log_data pld on pl.id = pld.id left join renewals l on l.id = pld.table_id where date(`timestamp`) between '$from' and '$to' and user='$agent' and change_table ='renewals' and  action = 'delete' group by urn";

    foreach ($this->db->query($deleted_urns)->result_array() as $v) {
      $urns[$v["urn"]]["deleted"] = $v['count'];
      $urns[$v['urn']]["name"] = $v['type'];
      $urns[$v['urn']]["urn"] = $v['urn'];
    }

    foreach ($urns as $k => $v) {
      if (!array_key_exists("updated", $v)) {
        $urns[$k]["updated"] = 0;
      }
      if (!array_key_exists("inserted", $v)) {
        $urns[$k]["inserted"] = 0;
      }
      if (!array_key_exists("deleted", $v)) {
        $urns[$k]["deleted"] = 0;
      }
    }
    //$this->firephp->log($urns);
    return $urns;
  }

  public function get_log($id) {
    $this->db->select("*,DATE_FORMAT(timestamp, '%d/%m/%Y %H:%i') AS uk_date", FALSE);
    $this->db->from('prospector_log');
    $this->db->join('prospector_log_data', 'prospector_log.id=prospector_log_data.id');
    $this->db->where_in('prospector_log.id', $id);
    $result = $this->db->get()->result_array();
    return $result;
  }

  public function acturis_matchback() {
    $qry = "SELECT dupeval, max( urn ) urn, max( clientkey ) clientkey
FROM (

SELECT replace( mobile, ' ', '' ) dupeval, urn, NULL AS clientkey
FROM contacts
GROUP BY dupeval
UNION ALL SELECT replace( mobile, ' ', '' ) dupeval, NULL AS urn, clientkey
FROM acturis_clients
GROUP BY dupeval
)
FULL GROUP BY dupeval
HAVING count( * ) >1";
  }

  public function get_renewal_types() {
    $this->db->select('distinct `type`', false);
    $this->db->from('renewals');
    $this->db->like('urn', 'pro', 'after');
    $result = $this->db->get()->result_array();
    foreach ($result as $k => $v) {
      $array[] = $v['type'];
    }
    return $array;
  }

  public function renewal_data($options) {
    $_SESSION['export_renewals'] = array();
    $type = $options["type"];
    $from = $options["date_from"];
    $to = $options["date_to"];

    $query = "select ro.prospector,date_format(r.date,'%b') as month,count(*) as count from renewals r left join renewal_ownership ro on r.id = ro.renewal_id left join leads l using(urn) where ro.prospector is not null ";
    if (!empty($from) && !empty($to)):
      $query .= " and date(ro.`date_added`) between '$from' and '$to' ";
    endif;
    if (!empty($type)):
      $query .= " and r.`type` = '$type'";
    endif;
    $query .= " group by month(date),ro.prospector";
	//$this->firephp->log($query);
    $data = array();
    $renewal_data = $this->db->query($query)->result_array();

    $exportHeaders = array("BDE");
    //create an empty array of months
    for ($x = 1; $x < 13; $x++) {
      $monthName = date('M',mktime(0, 0, 0, $x, 10));
      $months[$monthName] = 0;
      $exportHeaders[] = $monthName;
    }
    $months['Total'] = 0;
    //assign the empty values to each prospector
    foreach ($renewal_data as $k => $v) {
      $data[$v['prospector']] = $months;
    }
    $data['Total'] = $months;
    $grand_total = 0;
    $exportHeaders[] = "Total";
    
    
    //fill it with the results
    foreach ($renewal_data as $k => $v) {
      $data[$v['prospector']][$v['month']] = $v['count'];
      $data[$v['prospector']]['Total'] += $v['count'];
      $data['Total'][$v['month']] += $v['count']; 
      $grand_total += $v['count']; 
    }
      $data['Total']['Total'] = $grand_total;
   $_SESSION['export_renewals'][] = $exportHeaders;   
     foreach($data as $k=>$v){ 
   $tmp = array('BDE'=>$k);
   $_SESSION['export_renewals'][] = array_merge($tmp,$v);   
     }
    return $data;
  }

  public function appointment_years(){
    $qry = "select distinct year(begin_date) as year from events where year(begin_date) > '2013' and set_by is not null";
    return $this->db->query($qry)->result_array();
  }
  
   public function appointment_data($options) {
    $_SESSION['export_appointments'] = array();
    $type = $options["type"];
    $year = $options["year"];

    $query = "select e.set_by as prospector,date_format(e.`$type`,'%b') as month,count(distinct ea.event_id) as count from events e left join events_attendees ea on e.id = ea.event_id left join leads l using(urn) where set_by is not null and set_by <> '' and `status` = 'Live' and cancelled is null ";
    if (!empty($year)):
      $query .= " and year(e.`$type`) = '$year' ";
    endif;
    $query .= " group by month(e.`$type`),e.set_by";
    $data = array();
    $appointment_data = $this->db->query($query)->result_array();
	
    $exportHeaders = array("BDE");
    //create an empty array of months
    for ($x = 1; $x < 13; $x++) {
      $monthName = date('M',mktime(0, 0, 0, $x, 10));
      $months[$monthName] = 0;
      $exportHeaders[] = $monthName;
    }
    $months['Total'] = 0;
    //assign the empty values to each prospector
    foreach ($appointment_data as $k => $v) {
      $data[$v['prospector']] = $months;
    }
    $data['Total'] = $months;
    $grand_total = 0;
    $exportHeaders[] = "Total";
    
    
    //fill it with the results
    foreach ($appointment_data as $k => $v) {
      $data[$v['prospector']][$v['month']] = $v['count'];
      $data[$v['prospector']]['Total'] += $v['count'];
      $data['Total'][$v['month']] += $v['count']; 
      $grand_total += $v['count']; 
    }
      $data['Total']['Total'] = $grand_total;
   $_SESSION['export_appointments'][] = $exportHeaders;   
     foreach($data as $k=>$v){ 
   $tmp = array('BDE'=>$k);
   $_SESSION['export_appointments'][] = array_merge($tmp,$v);   
     }
    return $data;
  }
  
}

?>