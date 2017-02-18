<?php

global $IFACE;

if ($IFACE != "SPAWNING NEW IFACE" || $_GET['IFACE'] != '') {
  print ('HACKERS?');
  die;
}

function teamType_initialize() 
{
  if (config_get('check-database')) 
  {
    if (!db_table_exists('team_type')) 
    {
      db_create_table('team_type', array(
        'id' => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'name' => 'TEXT',
        'description' => 'TEXT',
        'grade_name' => 'TEXT',
        'grade_start_number' => 'INT',
        'grade_max_number' => 'INT',
        'grade_offset_number' => 'INT',
        'is_out_of_contest' => 'BOOLEAN'
      ));
    }
  }
}

function teamType_get_by_id($id) {
  return db_row_value('team_type', "`id`=$id");
}

function teamType_get_next($id) {
  $list = teamType_list();
  $n = count ($list);  
  for ($i = 0; $i < $n; $i++) {
    $it = $list[$i];
    if ($it['id'] === $id){
        return $i === $n ? $it : $list[$i + 1];
    }
  }
  
  return null;
}

function teamType_check_fields($name, $grade_start_number, $grade_max_number, 
                $grade_offset_number, $id=-1) {
    if ($name == '') {
      add_info("Поле \"Название\" обязательно для заполнения");
      return false;
    }
    
    if (db_count ('team_type', '`name`="'.$name.'" AND `id`<>'.$id) > 0) {
      add_info ('Тип команды с таким названием уже существует.');
      return false;
    }
    
    if (!isIntNumber($grade_start_number)) {
      add_info('"Минимальный номер" должен быть целым положительным числом');
      return false;
    }
    
    if (!isIntNumber($grade_max_number)) {
      add_info('"Максимальный номер" должен быть целым положительным числом');
      return false;
    }
    
    if (!isIntNumber($grade_offset_number)) {
      add_info('"Смещение номера" должно быть целым положительным числом');
      return false;
    }

    return true;
  }

function teamType_create($name, $description, $grade_name, $grade_start_number, $grade_max_number, 
                $grade_offset_number, $is_out_of_contest) {
    if (!teamType_check_fields($name, $grade_start_number, $grade_max_number, $grade_offset_number)) {
      return false;
    }
    
    $teamType_name = db_string($name);
    $teamType_description = db_string($description);
    $teamType_grade_name = db_string($grade_name);
    db_insert('team_type', array(
        'name' => $teamType_name,
        'description' => $teamType_description,
        'grade_name' => $teamType_grade_name,
        'grade_start_number' => $grade_start_number,
        'grade_max_number' => $grade_max_number,
        'grade_offset_number' => $grade_offset_number,
        'is_out_of_contest' => db_string($is_out_of_contest != null ? $is_out_of_contest : false)
    ));
    return true;
}

function teamType_create_received() {
    // Get post data
    $name = stripslashes(trim($_POST['name']));
    $description = stripslashes(trim($_POST['description']));
    $grade_name = stripslashes(trim($_POST['grade_name']));
    $grade_start_number = stripslashes(trim($_POST['grade_start_number']));
    $grade_max_number = stripslashes(trim($_POST['grade_max_number']));
    $grade_offset_number = stripslashes(trim($_POST['grade_offset_number']));
    $is_out_of_contest = $_POST['is_out_of_contest'];
    
    if (teamType_create($name, $description, $grade_name, $grade_start_number, 
            $grade_max_number, $grade_offset_number, $is_out_of_contest)) {
      $_POST = array();
      return true;
    }
    return false;
  }

function teamType_list() {
   return arr_from_query('SELECT * FROM `team_type` ORDER BY `grade_offset_number`, `grade_start_number`, `id`');
}

function teamType_update($id, $name, $description, $grade_name, $grade_start_number, 
        $grade_max_number, $grade_offset_number, $is_out_of_contest) {

    if (!teamType_check_fields($name, $grade_start_number, $grade_max_number, $grade_offset_number, $id)) {
      return false;
    }

    $teamType_name = db_string($name);
    $teamType_description = db_string($description);
    $teamType_grade_name = db_string($grade_name);
    
    $update = array(
        'name' => $teamType_name,
        'description' => $teamType_description,
        'grade_name' => $teamType_grade_name,
        'grade_start_number' => $grade_start_number,
        'grade_max_number' => $grade_max_number,
        'grade_offset_number' => $grade_offset_number,
        'is_out_of_contest' => db_string($is_out_of_contest != null ? $is_out_of_contest : false)
    );
    db_update('team_type', $update, "`id`=$id");    

    return true;
  }
  
  function teamType_update_received($id) {
    // Get post data
    $name = stripslashes(trim($_POST['name']));
    $description = stripslashes(trim($_POST['description']));
    $grade_name = stripslashes(trim($_POST['grade_name']));
    $grade_start_number = stripslashes(trim($_POST['grade_start_number']));
    $grade_max_number = stripslashes(trim($_POST['grade_max_number']));
    $grade_offset_number = stripslashes(trim($_POST['grade_offset_number']));
    $is_out_of_contest = $_POST['is_out_of_contest'];
        
    if (teamType_update($id, $name, $description, $grade_name, $grade_start_number, 
            $grade_max_number, $grade_offset_number, $is_out_of_contest)) {
      $_POST = array();
      return true;
    }
    return false;
  }
  
function manage_teamType_get_list () {
    return arr_from_query ('SELECT * FROM `team_type` ORDER BY `grade_offset_number`, `grade_start_number`, `id`');
}
    
function manage_teamType_update_received ($id) {
    teamType_update_received($id);
}

function manage_teamType_delete ($id) {
    return db_delete('team_type', 'id=' . $id);
}

?>