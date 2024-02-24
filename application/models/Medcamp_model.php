<?php

class Medcamp_model extends CI_Model {

  function get_stats($year, $category, $referrals) {
    $sql = "select ";
    $query = $this->db->get('locations');
    if ($query->num_rows() > 0) {
      foreach ($query->result() as $loc) {
        $sql .= "sum(case when p.location = " . $loc->id . " then 1 else 0 end) as `" . $loc->location . "`,";
      }
    }
    switch ($category) {
      case 'gender':
        $sql .= "p.gender";
        break;
      case 'race':
        $sql .= "r.race";
        break;
      case 'age':
        $sql .= "case when age < 16 then '0 to 15' when age < 31 then '16 to 30' when age < 61 then '31 to 60' else 'Over 60' end";
        break;
      case 'diagnosis':
        $sql .= "d.description";
        break;
      case 'referral':
        $sql .= "COALESCE(CONCAT(h.hospital,\" \",dp.department), CASE WHEN referral=1 THEN 'Referral' ELSE 'Not Referred' END)";
        break;
    }
    $sql .= " as `category`, count(*) as `total` ";
    if ($category == 'diagnosis') {
      $sql .= "from patient_diagnosis pd "
          . "inner join diagnosis d on pd.diagnosis_id = d.id "
          . "inner join patients p on p.id = pd.patient_id "
          . "inner join locations l on p.location = l.id ";
    }
    else {
      $sql .= "from patients p "
          . "inner join locations l on p.location = l.id "
          . "left join races r on p.race = r.id "              
          . "left join hospitals h on p.hospital = h.id "
          . "left join departments dp on p.department = dp.id ";
    }
    $sql .= "where l.year = " . $year;
    if ($referrals) {
      $sql .= " and referral = 1 ";
    }
    switch ($category) {
      case 'gender':
        $sql .= " group by p.gender";
        break;
      case 'race':
        $sql .= " group by p.race";
        break;
      case 'age':
        $sql .= " group by ";
        $sql .= "case when age < 16 then '0 to 15' when age < 31 then '16 to 30' when age < 61 then '31 to 60' else 'Over 60' end";
        break;
      case 'diagnosis':
        $sql .= " group by d.description";
        $sql .= " order by count(*) desc";
        break;
      case 'referral':
        $sql .= " group by ";
        $sql .= " p.referral, h.hospital, dp.department";
        break;
    }
    $query = $this->db->query($sql);
    return $query->result_array();
  }

  function get_diagnosis($refs) {
    $sql = "select diagnosis from diagnosis "
        . "where id in (" . rtrim(implode(',', $refs), ',')
        . ") order by diagnosis";
    $query = $this->db->query($sql);
    $result = $query->result_array();
    $csv = '';
    foreach ($result as $item):
      $csv .= $item['diagnosis'] . ',';
    endforeach;
    return rtrim($csv, ',');
  }

  function locations($year) {
    $this->db->where('year', $year);
    $this->db->select('location, id, 0 as total from locations', FALSE);
    $query = $this->db->get();
    return $query->result_array();
  }

  function get_location_patient_number($patientData) {
    $this->db->trans_start();
    $this->db->select('patient_number');
    $this->db->from('locations');
    $this->db->where('id', $patientData["location"]);
    $patient = $this->db->get()->row()->patient_number;
    $patient += 1;
    $this->db->where('id', $patientData["location"]);
    $this->db->update('locations', array('patient_number' => $patient));
    
    $this->db->where('unique_id',$patientData["unique_id"]);
    $this->db->update('patients', array('location_patient_number' => $patient));
    $this->db->trans_complete();
    return $patient;
  }

  function years() {
    $this->db->select('year');
    $this->db->group_by('year');
    $this->db->order_by('year', 'desc');
    $query = $this->db->get('locations');
    return $query->result_array();
  }

}
