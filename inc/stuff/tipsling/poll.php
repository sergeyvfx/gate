<?php

function poll_get_by_id($id) {
  return db_row_value('allvoits', "`id`=$id");
}

function poll_check_fields($name, $type, $id=-1) {
    if ($name == '') {
      add_info("Поле \"Вопрос\" обязательно для заполнения");
      return false;
    }
    
    if ($type == '' || $type < 1 || $type > 1) {
      add_info("Поле \"Тип\" обязательно для заполнения");
      return false;
    }
    
    return true;
  }

function poll_create($name, $type) {
    global $current_contest;
    if (!poll_check_fields($name, $type)) 
    { return false; }
    $poll_name = db_string($name);
    db_insert('allvoits', array('name' => $poll_name, 'type'=>$type));
    return true;
}

function poll_create_received() {
    // Get post data
    $num = $_POST['num'];
    $typeopr = $_POST['typeopr'];
    $zagol = stripslashes(trim($_POST['zagol']));
    
    $numers=$num;
    if ($typeopr=="1" or $typeopr=="2") {
        while ($numers!="0") {
            $postname = 'otvet'.$numers;
            $otvet = $_POST[$postname];
            if ($otvet == "" or $otvet == " ") 
                {   } 
            else {
                $otv[]=$otvet;
            }
            $numers--;
        }
        $num_elsements=count($otv);
        if ($num_elsements<2) 
            echo "Обычно в вопросах не меньше 2 ответов"; 
        else {
            $dats=time();
            mysql_query("INSERT INTO allvoits VALUES ( '', '$zagol', '$typeopr') ") or die("Ошибка");
            $id=mysql_insert_id();
            $num_elsements=count($otv);
            for ($idx=0; $idx<$num_elsements; ++$idx) {
                $insert=mysql_query("INSERT INTO voit VALUES ('','$otv[$idx]','0','$id') ") or die("Ошибка2"); 
            }
        }
    }
    $_POST = array();
    return true;
}


function poll_list() {
    return arr_from_query('SELECT id, vopros, type FROM `allvoits` ORDER BY `id`');    
}

function poll_update($id, $name, $type) {
    global $current_contest;
    if (!poll_check_fields($name, $type)) {
      return false;
    }
    $it = poll_get_by_id($id);
    
    $poll_name = db_string($name);
    
    $update = array('name' => $poll_name, 'type'=>$type);

    db_update('allvoits', $update, "`id`=$id");

    return true;
  }

function poll_update_received($id) {
    // Get post data
    $name = stripslashes(trim($_POST['name']));
    $type = $_POST['type'];
    if (poll_update($id, $name, $type)) {
      $_POST = array();
    }
  }
  
  function poll_can_delete($id) 
  {
      return true; //TODO: add contest_id field and check rigths for deleting
    $c = poll_get_by_id($id);
    $query = db_query ("select count(*) ".
                                       "from Admin_FamilyContest ".
                                       "where family_contest_id=".$c['family_id']." and ".
                                       "user_id=".user_id());
    if ($query > 0)
      return true;
    
    add_info("Вы не имеете прав для удаления данного конкурса");
    return false;
  }

  function poll_delete($id) {
    if (!poll_can_delete($id)) {
      return false;
    }

    return db_delete('allvoits', 'id=' . $id);
  }
  
       
function manage_poll_get_list () {
      return arr_from_query ('SELECT * FROM `allvoits` ORDER BY `name`');
    }
    
function manage_poll_update_received ($id) {
    poll_update_received($id);
}

function manage_poll_delete ($id) {
    return db_delete('allvoits', 'id=' . $id);
}
?>
