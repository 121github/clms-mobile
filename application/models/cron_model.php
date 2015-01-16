<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Cron_model extends CI_Model {

  function __construct() {
    parent::__construct();
  }

  public function get_upcoming_renewals($days) {

    $data = array();
    $days = $this->db->escape($days);

    $qry = "SELECT
                IF(
                    (
                        YEAR(r.date) <= YEAR(CURDATE()) + 1
                        AND
                        (
                            DATEDIFF(
                                DATE(CONCAT(YEAR(CURDATE()) + 1, DATE_FORMAT(r.date, '-%m-%d'))),
                                CURDATE()
                            ) >= 0
                            AND
                            DATEDIFF(
                                DATE(CONCAT(YEAR(CURDATE()) + 1, DATE_FORMAT(r.date, '-%m-%d'))),
                                CURDATE()
                            ) <= $days
                        )
                    ),
                    #THEN
                    DATEDIFF(
                        DATE(CONCAT(YEAR(CURDATE()) + 1, DATE_FORMAT(r.date, '-%m-%d'))),
                        CURDATE()
                    ),
                    #ELSE
                    IF(
                        (
                            YEAR(r.date) <= YEAR(CURDATE())
                            AND
                            (
                                DATEDIFF(
                                    DATE(CONCAT(YEAR(CURDATE()), DATE_FORMAT(r.date, '-%m-%d'))),
                                    CURDATE()
                                ) >= 0
                                AND
                                DATEDIFF(
                                    DATE(CONCAT(YEAR(CURDATE()), DATE_FORMAT(r.date, '-%m-%d'))),
                                    CURDATE()
                                ) <= $days
                            )
                        )
                        ,
                        #THEN
                        DATEDIFF(
                            DATE(CONCAT(YEAR(CURDATE()), DATE_FORMAT(r.date, '-%m-%d'))),
                            CURDATE()
                        ),
                        #ELSE
                        -1
                    )
                ) AS days_until_renewal,
                u.user, u.login, u.email, l.coname, l.urn, DATE_FORMAT(r.date, '%d %b') AS date, r.type, r.insurer, r.broker
                FROM users u
                LEFT JOIN leads l ON u.login = l.prospector
                LEFT JOIN renewals r ON l.urn = r.urn
                WHERE u.rep_group = 'Prospects'
                HAVING days_until_renewal >= 0
                ORDER BY days_until_renewal ASC";

    $result = $this->db->query($qry)->result_array();

    if (!empty($result)) {
      foreach ($result as $row) {
        if (!isset($data[$row['login']])) {
          $data[$row['login']] = array(
              'name' => $row['user'],
              'email' => $row['email']
          );
        }
        $data[$row['login']]['Policy'][] = array(
            'urn' => $row['urn'],
            'coname' => $row['coname'],
            'renewal_date' => $row['date'],
            'type' => $row['type'],
            'insurer' => $row['insurer'],
            'broker' => $row['broker'],
            'days_until_renewal' => $row['days_until_renewal'],
        );
      }
    }

    return $data;
  }

  public function get_unallocated() {
    $query = "select id,coname,date_format(begin_date,'%d/%m/%y') begin_date,title,set_by,urn,date_format(events.date_added,'%d/%m/%y') date_added,email,datediff(begin_date, curdate()) days from events left join leads using(urn) left join users on set_by = login where set_by is not null and set_by <> '' and status = 'Live' and id not in(select event_id from events_attendees where attendee in(select login from users where rep_group <> 'prospects')) and cancelled is null and begin_date > curdate() and datediff(begin_date, curdate()) < 61 order by days ";
    $result = $this->db->query($query)->result_array();
    $data = array();

    if (!empty($result)) {
      foreach ($result as $row) {
        if (!isset($data[$row['set_by']])) {
          $data[$row['set_by']] = array(
              'name' => $row['set_by'],
              'email' => $row['email']
          );
        }
        $data[$row['set_by']]['Appointment'][] = array(
            'urn' => $row['urn'],
            'coname' => $row['coname'],
            'title' => $row['title'],
            'date' => $row['begin_date'],
            'created' => $row['date_added'],
            'days' => $row['days']
        );
      }
    }

    return $data;
  }

}
