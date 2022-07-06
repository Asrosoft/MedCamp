<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Medcamp extends CI_Controller {

  function __construct() {
    parent::__construct();

    $this->load->database();
    $this->load->helper('url');
    $this->load->helper('cookie');
    $this->load->helper('form');

    $this->load->library('Grocery_CRUD');
    $this->load->model('Medcamp_model');

    $this->year = "";
    $this->currentYear = "";
    $this->location = "";
    $this->currentLocation = "";

    $this->currentYear = date("Y");
    if (isset($_COOKIE["currentyear"])) {
      $this->year = $_COOKIE["currentyear"];
    }
    else {
      $this->year = $this->currentYear;
    }
    $this->currentLocation = 0;
    if (isset($_COOKIE["currentlocation"])) {
      $this->location = $_COOKIE["currentlocation"];
    }
    else {
      $this->location = $this->currentLocation;
    }
  }

  function _medcamp_output($output = null) {
    $this->load->view('medcamp.php', $output);
  }

  function index() {
    $this->_medcamp_output((object) array('output' => '', 'js_files' => array(), 'css_files' => array()));
  }

  function locations() {
    try {
      /* This is only for the autocompletion */
      $crud = new Grocery_CRUD();

      $crud->set_table('locations');
      $crud->set_subject('Location');
      $crud->required_fields('location', 'year');
      $crud->columns('location', 'year');
      $crud->where('year', $this->year);

      $output = $crud->render();

      $this->_medcamp_output($output);
    }
    catch (Exception $e) {
      show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
    }
  }

  function backup() {
    try {
      // Load the DB utility class
      $this->load->dbutil();

      // Backup your entire database and assign it to a variable
      $backup = $this->dbutil->backup();

      // Load the file helper and write the file to your server
      $this->load->helper('file');
      $date = new DateTime();
      $filename = 'medcamp_db_'
          . $date->format('Y-m-d_H:i:s')
          . '.gz';
      write_file('/path/to/' . $filename, $backup);

      // Load the download helper and send the file to your desktop
      $this->load->helper('download');
      force_download($filename, $backup);
    }
    catch (Exception $e) {
      show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
    }
  }

  function races() {
    try {
      /* This is only for the autocompletion */
      $crud = new Grocery_CRUD();

      $crud->set_table('races');
      $crud->set_subject('Race');
      $crud->required_fields('race');
      $crud->columns('race');

      $output = $crud->render();

      $this->_medcamp_output($output);
    }
    catch (Exception $e) {
      show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
    }
  }

  function hospitals() {
    try {
      /* This is only for the autocompletion */
      $crud = new Grocery_CRUD();

      $crud->set_table('hospitals');
      $crud->set_subject('Hospitals');
      $crud->required_fields('hospital');
      $crud->columns('hospital');

      $output = $crud->render();

      $this->_medcamp_output($output);
    }
    catch (Exception $e) {
      show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
    }
  }

   function departments() {
    try {
      /* This is only for the autocompletion */
      $crud = new Grocery_CRUD();

      $crud->set_table('departments');
      $crud->set_subject('Departments');
      $crud->required_fields('department');
      $crud->columns('department');

      $output = $crud->render();

      $this->_medcamp_output($output);
    }
    catch (Exception $e) {
      show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
    }
  }
  function diagnosis() {
    try {
      /* This is only for the autocompletion */
      $crud = new Grocery_CRUD();

      $crud->set_table('diagnosis');
      $crud->set_subject('Diagnosis');
      $crud->required_fields('diagnosis', 'description');
      $crud->columns('diagnosis', 'description');
      $crud->fields('diagnosis', 'description');
      $crud->display_as('diagnosis', 'Diagnosis');
      $crud->display_as('description', 'Diagnosis Code');

      $output = $crud->render();

      $this->_medcamp_output($output);
    }
    catch (Exception $e) {
      show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
    }
  }

  function patients() {
    $this->output->enable_profiler(false);
    $this->load->library('session');

    $crud = new Grocery_CRUD();

    $crud->set_table('patients');
    $crud->where('year', $this->year);
    if ($this->location != 0) {
      $crud->where('patients.location', $this->location);
    }
    //$crud->set_relation_n_n('location', 'location', 'location', 'id', 'id', 'location');

    $crud->columns('id', 'name', 'birth_date', 'age', 'gender', 'race', 'address', 'phone_number', 'card_number', 'location', 'location_patient_number', 'diagnosis', 'referral', 'hospital','department', 'notes');
    $crud->required_fields('name');
    $crud->set_relation_n_n('diagnosises', 'patient_diagnosis', 'diagnosis', 'patient_id', 'diagnosis_id', 'description', 'priority');
    $crud->unset_columns('diagnosises');
    $crud->fields('name', 'gender', 'race', 'address', 'phone_number', 'birth_date', 'age', 'card_number', 'location', 'location_patient_number', 'diagnosis', 'diagnosises', 'referral', 'hospital','department', 'notes');
    $crud->field_type('gender', 'dropdown', array('Male' => 'Male', 'Female' => 'Female'));
    $crud->field_type('referral', 'true_false');
    $crud->set_relation('hospital', 'hospitals', 'hospital');
    $crud->set_relation('department', 'departments', 'department');
    $crud->set_relation('race', 'races', 'race');
    $crud->set_relation('location', 'locations', '{location} {year}', "year = '" . $this->year . "'");
    $crud->change_field_type('location_patient_number', 'invisible');
    $crud->change_field_type('diagnosis', 'invisible');
    //$crud->change_field_type('age','invisible');
    $crud->display_as('birth_date', 'Birth Date');
    $crud->display_as('card_number', 'Card Number');
    $crud->display_as('phone_number', 'Phone Number');
    $crud->display_as('diagnosises', 'Diagnosis');
    $crud->display_as('diagnosis_list', 'Diagnosis');
    $crud->display_as('location_patient_number', 'Patient Number');
    $crud->set_subject('Patient');
    $crud->callback_before_insert(array($this, 'before_patient_insert'));
    $crud->callback_before_update(array($this, 'update_diagnosis'));
    $crud->callback_column('diagnosis', array($this, 'wrap_diagnosis'));
    $crud->callback_column('notes', array($this, 'wrap_notes'));

    //$crud->set_lang_string('insert_success_message',
    //    'Patient has been successfully stored into the database. <script type="text/javascript">
    //    alert("' .$this->session->userdata('pname').'\r\nPatient number is ' .$this->session->userdata('pnum').'");
    //    </script>');
    $crud->set_lang_string('insert_success_message', 'Your data has been successfully stored into the database.<br/>Please wait while you are redirecting to the list page.
			<script type="text/javascript">
			window.location = "' . site_url(strtolower(__CLASS__) . '/' . strtolower(__FUNCTION__)) . '";
			</script>
			<div style="display:none">'
    );
    $crud->set_lang_string('update_success_message', 'Your data has been successfully stored into the database.<br/>Please wait while you are redirecting to the list page.
			<script type="text/javascript">
			window.location = "' . site_url(strtolower(__CLASS__) . '/' . strtolower(__FUNCTION__)) . '";
			</script>
			<div style="display:none">'
    );
    //$crud->unset_delete();
    $crud->unset_back_to_list();

    $output = $crud->render();

    $page = $this->uri->segment(count($this->uri->segments));
    if($page == 'add') {
        $js='<script>$(\'select[name="location"] option[value="' . $this->location . '"]\').attr("selected", "selected");</script>';
        $output->output .= $js;	
    }

    $this->_medcamp_output($output);
  }

  function wrap_notes($value, $row) {
    return $value = wordwrap($row->notes, 30, "<br>", true);
  }

  function wrap_diagnosis($value, $row) {
    return $value = wordwrap($row->diagnosis, 50, "<br>", true);
  }

  function before_patient_insert($post_array) {
    $post_array = $this->set_patient_number($post_array);
    $post_array = $this->update_diagnosis($post_array);
    return $post_array;
  }

  function set_patient_number($post_array) {
    $post_array["location_patient_number"] = $this->Medcamp_model->get_location_patient_number($post_array["location"]);
    $this->session->set_userdata('pname', $post_array["name"]);
    $this->session->set_userdata('pnum', $post_array["location_patient_number"]);
    return $post_array;
  }

  function update_diagnosis($post_array) {
    $post_array["diagnosis"] = $this->Medcamp_model->get_diagnosis($post_array["diagnosises"]);
    //$now = new DateTime();
    //$birth = new DateTime(str_replace('/','-',$post_array["birth_date"]));
    //$post_array["age"] = $birth->diff($now)->format('%r%y');
    if (strlen($post_array["age"]) == 0) {
      $birthDate = explode("/", $post_array["birth_date"]);
      $post_array["age"] = (date("md", date("U", mktime(0, 0, 0, $birthDate[1], $birthDate[0], $birthDate[2]))) > date("md") ? ((date("Y") - $birthDate[2]) - 1) : (date("Y") - $birthDate[2]));
    }
    return $post_array;
  }

  function statistics() {
    $currentYear = date("Y");
    if (isset($_COOKIE["currentyear"])) {
      $year = $_COOKIE["currentyear"];
    }
    else {
      $year = $currentYear;
    }
    $category = $this->input->post('categorySelect');
    $referrals = $this->input->post('Referrals');
    $data['locations'] = $this->Medcamp_model->locations($year);
    if (!$category) {
      $category = "diagnosis";
    }
    $data['category'] = $category;
    $data['referrals'] = $referrals;
    $data['year'] = $year;
    $data['statistics'] = $this->Medcamp_model->get_stats($year, $category, $referrals);
    $data['title'] = "Statistics";
    $data['output'] = '';
    $data['js_files'] = array(base_url('assets/grocery_crud/js/jquery-1.8.1.min.js'));
    $data['css_files'] = array(base_url('assets/grocery_crud/themes/flexigrid/css/flexigrid.css'));
    $this->load->view('statistics', $data);
  }
  
  function shutdown() {
    file_put_contents('/var/www/shutdown.txt', 'shutdown');
    $data['js_files'] = array(base_url('assets/grocery_crud/js/jquery-1.8.1.min.js'));
    $data['css_files'] = array(base_url('assets/grocery_crud/themes/flexigrid/css/flexigrid.css'));
    $this->load->view('shutdown', $data);
  }

}
