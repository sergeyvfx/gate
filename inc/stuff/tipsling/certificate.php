<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function certificate_get_by_id($id) {
  return db_row_value('certificate', "`id`=$id");
}

function certificate_check_fields($name, $family_id, $for, $id=-1) {
    if ($name == '') {
      add_info("Поле \"Название\" обязательно для заполнения");
      return false;
    }
    
    if ($for == '') {
      add_info("Поле \"Для кого\" обязательно для заполнения");
      return false;
    }
        
    if ($family_id == '' || $family_id < 1) {
      add_info("Поле \"Семейство\" обязательно для заполнения");
      return false;
    }
    
    if (db_count ('certificate', '`name`="'.$name.'" AND `family_id`='.$family_id.' AND `id`<>'.$id) > 0) 
    {
        add_info ('Сертификат с таким именем уже существует.');
        return false;
    }
    
    return true;
  }

function certificate_create($name, $family_id, $for, $template = '', $limit='', $actual=1) {
    global $current_contest;
    if ($family_id==''||$family_id<1)
    {
        $it = contest_get_by_id($current_contest);
        $family_id = $it['family_id'];
    }
    if ($limit=='')
        $limit = 'null';
    else
        $limit = db_string($limit);
    if ($actual == ''){
        $actual = 0;
    }
    
    if (!certificate_check_fields($name, $family_id, $for)) 
    { return false; }
    
    $certificate_name = db_string($name);
    db_insert('certificate', array('name' => $certificate_name, 'family_id'=>$family_id, 
        'template' => db_string($template), 'limit_id'=>$limit, 'for'=>  db_string($for),
        'actual' => $actual));
    return true;
}

function certificate_create_received() {
    // Get post data
    $name = stripslashes(trim($_POST['name']));
    $family_id = $_POST['family_id'];
    $template = $_POST['template'];
    $limit = $_POST['limit'];
    $for = $_POST['for'];
    $actual = $_POST['actual'];
    if (certificate_create($name, $family_id, $for, $template, $limit, $actual)) {
      $_POST = array();
      return true;
    }
    return false;
  }


function certificate_list($family_id) {
    
    global $current_contest;
    if ($family_id==''||$family_id<1)
    {
        $it = contest_get_by_id($current_contest);
        $family_id = $it['family_id'];
    }
    
    return arr_from_query('SELECT `certificate`.id, `certificate`.name, `certificate`.template, `limit`.`name` as limit_name, `certificate`.`for`
                           FROM `certificate` left join `limit` on `limit`.`id`=`certificate`.`limit_id`
                           WHERE family_id='.$family_id.' ORDER BY `id`');
}

function certificate_update($id, $name, $for, $family_id, $template = '', $limit='', $actual=1) {
    global $current_contest;
    if ($family_id==''||$family_id<1)
    {
        $it = contest_get_by_id($current_contest);
        $family_id = $it['family_id'];
    }
    if ($limit=='')
        $limit = 'null';
    else
        $limit = db_string($limit);
    if ($actual == ''){
        $actual = 0;
    }
    
    if (!certificate_check_fields($name, $family_id, $for, $id)) {
      return false;
    }
    $it = certificate_get_by_id($id);
    
    $certificate_name = db_string($name);
    
    $update = array('name' => $certificate_name, 'family_id'=>$family_id,
        'template' => db_string($template!=''?$template:$it['template']),
        'limit_id'=>$limit, 'for'=>db_string($for), 'actual'=>$actual);

    db_update('certificate', $update, "`id`=$id");

    return true;
  }

function certificate_update_received($id) {
    // Get post data
    $name = stripslashes(trim($_POST['name']));
    $family_id = $_POST['family_id'];
    $template = $_POST['template'];
    $limit = $_POST['limit'];
    $for = $_POST['for'];
    $actual = $_POST['actual_value'];
    if (certificate_update($id, $name, $for, $family_id, $template, $limit, $actual)) {
      $_POST = array();
    }
  }
  
  function certificate_can_delete($id) 
  {
    $it = certificate_get_by_id($id);
    $query = db_query ("select count(*) ".
                                       "from Admin_FamilyContest ".
                                       "where family_contest_id=".$it['family_id']." and ".
                                       "user_id=".user_id());
    if ($query > 0)
      return true;
    
    add_info("Вы не имеете прав для удаления данного сертификатат");
    return false;
  }

  function certificate_delete($id) {
    if (!certificate_can_delete($id)) {
      return false;
    }

    return db_delete('certificate', 'id=' . $id);
  }
  
  function certificate_get_sql($id, $current_contest=-1, $param='', $include_template = true)
  {
      $cert = certificate_get_by_id($id);
      $for = $cert['for'];
      $limit_id = $cert['limit_id'];
      $limit = limit_get_by_id($limit_id);
      $template = $cert['template'];
            
      $select = 'SELECT DISTINCT `team`.`id` as "id_команды", ';
      if ($current_contest != -1)
      {
          $from = 'FROM `team`, ';
          $where = 'WHERE `team`.`contest_id`='.$current_contest.' AND `team`.`is_payment`=1 AND ';
          if (!check_contestadmin_rights()) {
              $where .= '`team`.`responsible_id`='.user_id().' AND ';
          }
          $order = 'ORDER BY `team`.`grade` ASC, `team`.`number` ASC';
          $table_team_id = db_field_value('visible_table', 'id', "`table`='team'");
          $tables = array($table_team_id);
      }
      else
      {
          $from = 'FROM ';
          $where = ($limit['limit']==''?'':'WHERE ');
          $tables = array();
      }
      $fields = array();
      
      preg_match_all("/#([^#]+)#/", $for, $matchesarray, PREG_SET_ORDER);
      foreach ($matchesarray as $value) {
          $field = visible_field_get_by_caption($value[1]);
          $table = visible_table_get_by_id($field['table_id']);
          
          if (!inarr($tables, $table['id']))
          {
              $tables[count($tables)]=$table['id'];
              $from .= '`'.$table['table'].'`, ';
              if ($table['table'] == 'pupil_team'){
                  $order .= ', `pupil_team`.`number` ASC';
              }                  
              if ($table['table'] == 'teacher_team'){
                  $order .= ', `teacher_team`.`number` ASC';
              }
          }
          if (!inarr($fields, $value[1]))
          {
              $fields[count($fields)]=$value[1];
              $select .= '`'.$table['table'].'`.`'.$field['field'].'` as '.db_string($value[1]).', ';
          }
      }
      
      if ($include_template) {
          preg_match_all("/#([^#]+)#/", $template, $matchesarray, PREG_SET_ORDER);
          foreach ($matchesarray as $value) {
            $field = visible_field_get_by_caption($value[1]);
            $table = visible_table_get_by_id($field['table_id']);
            if (!inarr($tables, $table['id']))
            {
              $tables[count($tables)]=$table['id'];
              $from .= '`'.$table['table'].'`, ';
              if ($table['table'] == 'pupil_team'){
                  $order .= ', `pupil_team`.`number` ASC';
              }                  
              if ($table['table'] == 'teacher_team'){
                  $order .= ', `teacher_team`.`number` ASC';
              }
            }
            if (!inarr($fields, $value[1]))
            {
              $fields[count($fields)]=$value[1];
              $select .= '`'.$table['table'].'`.`'.$field['field'].'` as '.db_string($value[1]).', ';
            }
          }
      }
                
      preg_match_all('/(\d+) (<|<=|=|>|>=|<>|is null|is not null) (\S*) (OR|AND)/', $limit['limit'], $limits, PREG_SET_ORDER);
      $i=0;
      foreach ($limits as $value) {
        $field_id = $value[1];
        $operation = $value[2];
        $val = $value[3];
        $connection = $value[4];
        
        $field = visible_field_get_by_id($field_id);
        $table = visible_table_get_by_id($field['table_id']);
        if (!inarr($tables, $table['id']))
        {
            $tables[count($tables)]=$table['id'];
            $from .= $table['table'].', ';
            if ($table['table'] == 'pupil_team'){
                $order .= ', `pupil_team`.`number` ASC';
            }                  
              if ($table['table'] == 'teacher_team'){
                $order .= ', `teacher_team`.`number` ASC';
            }
        }        
        $where .= '`'.$table['table'].'`.`'.$field['field'].'`'.$operation.db_string($val).' '.$connection.' ';
      }
      
      if ($where != '')
      {
          $where = substr($where, 0, strlen($where)-4);
          $where .= ' AND ';
      }
      
      $have_new=true;
      while ($have_new)
      {
          $have_new=false;
          $n = count($tables);
          for ($i=0; $i<$n; $i++)
            for ($j=$i+1; $j<$n; $j++)
            {
                $connection = db_row_value('table_connections', "`table1_id`=".$tables[$i]." AND `table2_id`=".$tables[$j]);
                if (!$connection)
                {
                    $connection = db_row_value('table_connections', "`table1_id`=".$tables[$j]." AND `table2_id`=".$tables[$i]);
                }
                if ($connection!=''){
                $connect = $connection['connection'];
                    if ($connect=='')
                    {
                        $table = db_row_value("visible_table", "`id`=".$connection['connect_table_id']);
                    
                        if (!inarr($tables, $table['id']))
                        {
                            $have_new = true;
                            $tables[count($tables)]=$table['id'];
                            $from .= '`'.$table['table'].'`, ';
                            if ($table['table'] == 'pupil_team'){
                                $order .= ', `pupil_team`.`number` ASC';
                            }                  
                            if ($table['table'] == 'teacher_team'){
                                $order .= ', `teacher_team`.`number` ASC';
                            }
                        }
                    }
                }
            }
      }
      $n = count($tables);
      for ($i=0; $i<$n; $i++)
          for ($j=$i+1; $j<$n; $j++)
          {
              $connection = db_row_value('table_connections', "`table1_id`=".$tables[$i]." AND `table2_id`=".$tables[$j]);
              if (!$connection)
                  $connection = db_row_value('table_connections', "`table1_id`=".$tables[$j]." AND `table2_id`=".$tables[$i]);
              $connect = $connection['connection'];
              
              if ($connect != '')
                $where .= $connect.' AND ';
          }
      
      $select = substr($select, 0, strlen($select) - 2);
      $from = substr($from, 0, strlen($from)-2);
      if ($param != '')
          $where .= $param;
      else if ($where != '')
          $where = substr ($where, 0, strlen($where)-5);
      
      $sql = $select.' '.$from.' '.$where.' '.$order;
      return $sql;
  }
  
  function certificate_get_html($id, $current_contest=-1, $param='')
  {
    $c = certificate_get_by_id($id);
    if ($c==false)
        return;
    $template = $c['template'];
    $sql = certificate_get_sql($id, $current_contest, $param, true);
    
    $result = db_query($sql);
    $t = db_row_array($result);
    //TODO: It's hack. Need to find normal solution for printing details with separator between them.
    if ($t['ФИО ученика'] != "" || $t['ФИО учителя'] != "")
    {
        while ($row = db_row_array($result)) 
        {
            if ($row['ФИО ученика'] != "" && strpos($t['ФИО ученика'], $row['ФИО ученика'])===false){
                $t['ФИО ученика'] .= '<br/>'.$row['ФИО ученика'];
            }
            if ($row['ФИО учителя'] != "" && strpos($t['ФИО учителя'], $row['ФИО учителя'])===false){
                $t['ФИО учителя'] .= ', '.$row['ФИО учителя'];
            }
        }
    }

    if ($t==false)
        return;

    if ($t['Место в параллели']==1)
        $t['Место в параллели'] = I;
    else if ($t['Место в параллели']==2)
        $t['Место в параллели'] = II;
    else if ($t['Место в параллели']==3)
        $t['Место в параллели'] = III;

    $matchearray = array();
    preg_match_all("/#[^#]+#/", $template, $matchearray);
    $n = count($matchearray[0]);
    $i=0;
    while ($i<$n)
    {
        $match = substr($matchearray[0][$i], 1, count($matchearray[0][$i])-2);
        $template = str_replace($matchearray[0][$i], $t[$match], $template);
        $i++;
    }
    return $template;
  }
  
  //----------------limit functions--------------------

function limit_get_by_id($id) {
  return db_row_value('limit', "`id`=$id");
}

function limit_check_fields($name, $id=-1) {
    if ($name == '') {
      add_info("Поле \"Название\" обязательно для заполнения");
      return false;
    }
    
    if (db_count ('limit', '`name`="'.$name.'" `id`<>'.$id) > 0) 
    {
        add_info ('Ограничение с таким именем уже существует.');
        return false;
    }
    
    return true;
  }

function limit_create($name, $limit = '') {
    if (!limit_check_fields($name)) 
    { return false; }
    
    $limit_name = db_string($name);
    db_insert('limit', array('name' => $limit_name, 
              'limit' => db_string($limit)));
    return true;
}

function limit_create_received() {
    // Get post data
    $name = stripslashes(trim($_POST['name']));
    $limit = $_POST['result_limit'];
    if (limit_create($name, $limit)) {
      $_POST = array();
      return true;
    }
    return false;
  }


function limit_list() {
    return arr_from_query('SELECT * 
                           FROM `limit`
                           ORDER BY `id`');
}

function limit_update($id, $name, $limit = '') {
    if (!limit_check_fields($name, $limit, $id)) {
      return false;
    }
    $it = limit_get_by_id($id);
    
    $limit_name = db_string($name);
    $update = array('name' => $limit_name, 'limit' => db_string($limit!=''?$limit:$it['limit']));

    db_update('limit', $update, "`id`=$id");

    return true;
  }

function limit_update_received($id) {
    // Get post data
    $name = stripslashes(trim($_POST['name']));
    $limit = $_POST['result_limit'];
    if (limit_update($id, $name, $limit)) {
      $_POST = array();
    }
  }
  
  function limit_delete($id) {
    return db_delete('limit', 'id=' . $id);
  }
  
  
    //----------------visible_field functions--------------------

function visible_field_get_by_id($id) {
  return db_row_value('visible_field', "`id`=$id");
}

function visible_field_get_by_caption($caption) {
  return db_row_value('visible_field', "`caption`=".db_string($caption));
}

function visible_field_check_fields($table_id, $field, $caption, $id=-1) {
    if ($table_id == '') {
      add_info("Поле \"Таблица\" обязательно для заполнения");
      return false;
    }
    
    if ($field == '') {
      add_info("\"Поле\" обязательно для заполнения");
      return false;
    }
    
    if ($caption == '') {
      add_info("Поле \"Заголовок\" обязательно для заполнения");
      return false;
    }
    
    if (db_count ('visible_field', '`table_id`='.$table_id.' AND `field`='.db_string($field).' AND `id`<>'.$id) > 0) 
    {
        add_info ('Это поле уже есть в таблице.');
        return false;
    }
    
    return true;
  }

function visible_field_create($table_id, $field, $caption) {
    if (!visible_field_check_fields($table_id, $field, $caption)) 
    { return false; }
    
    db_insert('visible_field', array('table_id' => $table_id, 
              'field' => db_string($field),
              'caption' => db_string($caption)));
    
    return true;
}

function visible_field_create_received() {
    // Get post data
    $table_id = stripslashes(trim($_POST['table_id']));
    $field = $_POST['field'];
    $caption = $_POST['caption'];
    if (visible_field_create($table_id, $field, $caption)) {
      $_POST = array();
      return true;
    }
    return false;
  }


function visible_field_list() {
    return arr_from_query('SELECT * 
                           FROM `visible_field`
                           ORDER BY `table_id`,`id`');
}

function visible_field_update($id, $table_id, $field, $caption) {
    if (!visible_field_check_fields($table_id, $field, $caption, $id)) {
      return false;
    }
    $it = visible_field_get_by_id($id);
    
    $update = array('table_id' => $table_id, 'field' => db_string($field), 'caption'=> db_string($caption));

    db_update('limit', $update, "`id`=$id");

    return true;
  }

function visible_field_update_received($id) {
    // Get post data
    $table_id = stripslashes(trim($_POST['table_id']));
    $field = $_POST['field'];
    $caption = $_POST['caption'];
    if (visible_field_update($id, $table_id, $field, $caption)) {
      $_POST = array();
    }
  }
  
  function visible_field_delete($id) {
    return db_delete('visible_field', 'id=' . $id);
  }

  
  //----------------visible_table functions--------------------

function visible_table_get_by_id($id) {
  return db_row_value('visible_table', "`id`=$id");
}

?>
