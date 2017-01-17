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
        'is_out_of_contest' => 'BOOLEAN'
      ));
    }
  }
}



function teamType_get_by_id($id) {
  return db_row_value('team_type', "`id`=$id");
}

function teamType_check_fields($name, $id=-1) {
    if ($name == '') {
      add_info("Поле \"Название\" обязательно для заполнения");
      return false;
    }
    
    if (db_count ('team_type', '`name`="'.$name.'" AND `id`<>'.$id) > 0) {
          add_info ('Тип команды с таким названием уже существует.');
          return false;
        }

    return true;
  }

function teamType_create($name, $description, $is_out_of_contest) {
    if (!teamType_check_fields($name)) 
    {
      return false;
    }
    $teamType_name = db_string($name);
    $teamType_description = db_string($description);
    db_insert('team_type', array(
        'name' => $teamType_name,
        'description' => $teamType_description,
        'is_out_of_contest' => db_string($is_out_of_contest != null ? $is_out_of_contest : false)
    ));
    return true;
}

function teamType_create_received() {
    // Get post data
    $name = stripslashes(trim($_POST['name']));
    $description = stripslashes(trim($_POST['description']));
    $is_out_of_contest = $_POST['is_out_of_contest'];
    
    if (teamType_create($name, $description, $is_out_of_contest)) {
      $_POST = array();
      return true;
    }
    return false;
  }

function teamType_list() {
   return arr_from_query('SELECT * FROM `team_type` ORDER BY `id`');
}

function teamType_update($id, $name, $description, $is_out_of_contest) {

    if (!teamType_check_fields($name, $id)) {
      return false;
    }

    $teamType_name = db_string($name);
    $teamType_description = db_string($description);
    
    $update = array(
        'name' => $teamType_name,
        'description' => $teamType_description,
        'is_out_of_contest' => db_string($is_out_of_contest != null ? $is_out_of_contest : false)
    );
    db_update('team_type', $update, "`id`=$id");    

    return true;
  }
  
  function teamType_update_received($id) {
    // Get post data
    $name = stripslashes(trim($_POST['name']));
    $description = stripslashes(trim($_POST['description']));
    $is_out_of_contest = $_POST['is_out_of_contest'];
        
    if (teamType_update($id, $name, $description, $is_out_of_contest)) {
      $_POST = array();
      return true;
    }
    return false;
  }
  
function manage_teamType_get_list () {
    return arr_from_query ('SELECT * FROM `team_type` ORDER BY `id`');
}
    
function manage_teamType_update_received ($id) {
    teamType_update_received($id);
}

function manage_teamType_delete ($id) {
    return db_delete('team_type', 'id=' . $id);
}

?>