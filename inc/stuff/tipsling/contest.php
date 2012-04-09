<?php

/**
 * Gate - Wiki engine and web-interface for WebTester Server
 *
 * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
 *
 * This program can be distributed under the terms of the GNU GPL.
 * See the file COPYING.
 */
global $IFACE, $current_contest;

if ($IFACE != "SPAWNING NEW IFACE" || $_GET['IFACE'] != '') {
  print ('HACKERS?');
  die;
}

function contest_initialize() 
{
  if (config_get('check-database')) 
  {
    if (!db_table_exists('family_contest')) 
    {
      db_create_table('family_contest', array(
        'id' => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'name' => 'TEXT'));
      
      db_insert ('family_contest', array('name' => '"Тризформашка"'));
    }
    if (!db_table_exists ('contest')) {
      db_create_table('contest', array(
        'id' => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'family_id' => 'INT',
        'name' => 'TEXT',
        'registration_start'=>'DATETIME',
        'registration_finish'=>'DATETIME',
        'contest_start'=>'DATETIME',
        'contest_finish'=>'DATETIME',
        'send_to_archive'=>'DATETIME'));      
    }
    
    if (!db_table_exists ('certificate')) {
      db_create_table('certificate', array(
        'id' => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'family_id' => 'INT',
        'name' => 'TEXT',
        'template'=>'TEXT'));
    }
  }
}

//----------------contestFamily functions--------------------

function contestFamily_get_by_id($id) {
  return db_row_value('family_contest', "`id`=$id");
}

function contestFamily_check_fields($name, $id=-1) {
    if ($name == '') {
      add_info("Поле \"Название\" обязательно для заполнения");
      return false;
    }
    
    if (db_count ('family_contest', '`name`="'.$name.'" AND `id`<>'.$id) > 0) {
          add_info ('Семейство конкурсов с таким именем уже существует.');
          return false;
        }

    return true;
  }

function contestFamily_create($name) {
    if (!contestFamily_check_fields($name)) {
      return false;
    }
    $contestFamily_name = db_string($name);
    db_insert('family_contest', array('name' => $contestFamily_name));
    return true;
}

function contestFamily_list() {
   return arr_from_query('SELECT * FROM `family_contest` ORDER BY `id`');
}

function contestFamily_update($id, $name) {

    if (!contestFamily_check_fields($name, $id)) {
      return false;
    }

    $contestFamily_name = db_string($name);
    
    $update = array('name' => $contestFamily_name);

    db_update('family_contest', $update, "`id`=$id");

    return true;
  }

//----------------contest functions--------------------

function contest_get_by_id($id) {
  return db_row_value('contest', "`id`=$id");
}

function contest_check_fields($name, $family_id, $registration_start = '', $registration_finish = '', 
            $contest_start='', $contest_finish='', $send_to_archive='', $id=-1) {
    if ($name == '') {
      add_info("Поле \"Название\" обязательно для заполнения");
      return false;
    }
    
    if ($family_id == '' || $family_id < 1) {
      add_info("Поле \"Семейство\" обязательно для заполнения");
      return false;
    }
    
    if (db_count ('contest', '`name`="'.$name.'" AND `family_id`='.$family_id.' AND `id`<>'.$id) > 0) 
    {
        add_info ('Конкурс с таким именем уже существует.');
        return false;
    }
    
    if ($registration_start!='' && $registration_finish!='' && $registration_start>$registration_finish)
    {
        add_info ('Начало регистрации не может быть позднее, чем ее окончание');
        return false;
    }
    
    if ($contest_start!='' && $contest_finish!='' && $contest_start>$contest_finish)
    {
        add_info ('Начало конкурса не может быть позднее, чем его окончание');
        return false;
    }
    
    if ($contest_finish!='' && $send_to_archive!='' && $contest_finish>$send_to_archive)
    {
        add_info ('Нельзя добавлять конкурс в архив до его окончания');
        return false;
    }

    return true;
  }

function contest_create($name, $family_id, $registration_start = '', $registration_finish = '', 
            $contest_start='', $contest_finish='', $send_to_archive='') {
    global $current_contest;
    if ($family_id==''||$family_id<1)
    {
        $it = contest_get_by_id($current_contest);
        $family_id = $it['family_id'];
    }
    if (!contest_check_fields($name, $family_id, $registration_start, $registration_finish,
            $contest_start, $contest_finish, $send_to_archive)) 
    { return false; }
    $contest_name = db_string($name);
    db_insert('contest', array('name' => $contest_name, 'family_id'=>$family_id, 
        'registration_start' => $registration_start!=''?db_string($registration_start):null,
        'registration_finish' => $registration_finish!=''?db_string($registration_finish):null,
        'contest_start' => $contest_start!=''?db_string($contest_start):null,
        'contest_finish' => $contest_finish!=''?db_string($contest_finish):null,
        'send_to_archive' => $send_to_archive!=''?db_string($send_to_archive):null));
    return true;
}

function contest_create_received() {
    // Get post data
    $name = stripslashes(trim($_POST['name']));
    $family_id = $_POST['family_id'];
    $registration_start = $_POST['registration_start'];
    $registration_finish = $_POST['registration_finish'];
    $contest_start = $_POST['contest_start'];
    $contest_finish = $_POST['contest_finish'];
    $send_to_archive = $_POST['send_to_archive'];
    if (contest_create($name, $family_id, $registration_start, $registration_finish,
            $contest_start, $contest_finish, $send_to_archive)) {
      $_POST = array();
      return true;
    }
    return false;
  }


function contest_list($family_id='') {
    if ($family_id=='')
        return arr_from_query('SELECT id, name, 
                               DATE_FORMAT(`registration_start`,"%d-%m-%Y") as registration_start,
                               DATE_FORMAT(`registration_finish`,"%d-%m-%Y") as registration_finish,
                               DATE_FORMAT(`contest_start`,"%d-%m-%Y") as contest_start,
                               DATE_FORMAT(`contest_finish`,"%d-%m-%Y") as contest_finish,
                               DATE_FORMAT(`send_to_archive`,"%d-%m-%Y") as send_to_archive
                               FROM `contest` ORDER BY `id`');
    else
        return arr_from_query('SELECT id, name, 
                               DATE_FORMAT(`registration_start`,"%d-%m-%Y") as registration_start,
                               DATE_FORMAT(`registration_finish`,"%d-%m-%Y") as registration_finish,
                               DATE_FORMAT(`contest_start`,"%d-%m-%Y") as contest_start,
                               DATE_FORMAT(`contest_finish`,"%d-%m-%Y") as contest_finish,
                               DATE_FORMAT(`send_to_archive`,"%d-%m-%Y") as send_to_archive
                               FROM `contest` where family_id='.$family_id.' ORDER BY `id`');
}

function contest_update($id, $name, $family_id, $registration_start = '', $registration_finish = '', 
            $contest_start='', $contest_finish='', $send_to_archive='') {
    global $current_contest;
    if ($family_id==''||$family_id<1)
    {
        $it = contest_get_by_id($current_contest);
        $family_id = $it['family_id'];
    }
    if (!contest_check_fields($name, $family_id, $registration_start, $registration_finish, 
            $contest_start, $contest_finish, $send_to_archive, $id)) {
      return false;
    }
    $it = contest_get_by_id($id);
    
    $contest_name = db_string($name);
    
    $update = array('name' => $contest_name, 'family_id'=>$family_id,
        'registration_start' => db_string($registration_start!=''?$registration_start:$it['registration_start']),
        'registration_finish' => db_string($registration_finish!=''?$registration_finish:$it['registration_finish']),
        'contest_start' => db_string($contest_start!=''?$contest_start:$it['contest_start']),
        'contest_finish' => db_string($contest_finish!=''?$contest_finish:$it['contest_finish']),
        'send_to_archive' => db_string($send_to_archive!=''?$send_to_archive:$it['send_to_archive']));

    db_update('contest', $update, "`id`=$id");

    return true;
  }

function contest_update_received($id) {
    // Get post data
    $name = stripslashes(trim($_POST['name']));
    $family_id = $_POST['family_id'];
    $registration_start = $_POST['registration_start'];
    $registration_finish = $_POST['registration_finish'];
    $contest_start = $_POST['contest_start'];
    $contest_finish = $_POST['contest_finish'];
    $send_to_archive = $_POST['send_to_archive'];
    if (contest_update($id, $name, $family_id, $registration_start, $registration_finish,
            $contest_start, $contest_finish, $send_to_archive)) {
      $_POST = array();
    }
  }
  
  function contest_can_delete($id) 
  {
    $c = contest_get_by_id($id);
    $query = db_query ("select count(*) ".
                                       "from Admin_FamilyContest ".
                                       "where family_contest_id=".$c['family_id']." and ".
                                       "user_id=".user_id());
    if ($query > 0)
      return true;
    
    add_info("Вы не имеете прав для удаления данного конкурса");
    return false;
  }

  function contest_delete($id) {
    if (!contest_can_delete($id)) {
      return false;
    }

    return db_delete('contest', 'id=' . $id);
  }
  
  function get_contest_status($id)
  {
      $status = 0; //"Предстоящий"
      $c = contest_get_by_id($id);
      
      $query = "select * from contest where DATE_FORMAT(registration_start,'%Y-%m-%d')<=DATE_FORMAT(".db_string(date("Y-m-d")).",'%Y-%m-%d') and id=".$id;
      $r = arr_from_query($query);
      if (count($r)>0)
      {
          $status = 1; //"Идет регистрация"
      }
      
      $query = "select * from contest where DATE_FORMAT(registration_finish,'%Y-%m-%d')<DATE_FORMAT(".db_string(date("Y-m-d")).",'%Y-%m-%d') and id=".$id;
      $r = arr_from_query($query);
      if (count($r)>0)
      {
          $status = 2; //"Регистрация закончилась"
      }
      
      $query = "select * from contest where DATE_FORMAT(contest_start,'%Y-%m-%d')<=DATE_FORMAT(".db_string(date("Y-m-d")).",'%Y-%m-%d') and id=".$id;
      $r = arr_from_query($query);
      if (count($r)>0)
      {
          $status = 3; //"Конкурс начался"
      }
      
      $query = "select * from contest where DATE_FORMAT(contest_finish,'%Y-%m-%d')<DATE_FORMAT(".db_string(date("Y-m-d")).",'%Y-%m-%d') and id=".$id;
      $r = arr_from_query($query);
      if (count($r)>0)
      {
          $status = 4; //"Конкурс завершился"
      }
      
      $query = "select * from contest where DATE_FORMAT(send_to_archive,'%Y-%m-%d')<DATE_FORMAT(".db_string(date("Y-m-d")).",'%Y-%m-%d') and id=".$id;
      $r = arr_from_query($query);
      if (count($r)>0)
      {
          $status = 5; //"Архивный"
      }
      return $status;
  }
  
  function get_contest_text_status($id)
  {
      $status = get_contest_status($id);
      if ($status == 0)
        return "Предстоящий";
      if ($status == 1)
        return "Идет регистрация";
      if ($status == 2)
        return "Регистрация закончилась";
      if ($status == 3)
        return "Конкурс начался";
      if ($status == 4)
        return "Конкурс завершился";
      if ($status == 5)
        return "Архивный";
      
      return "Невозможно определить статус";
  }
      
function manage_contest_get_list () {
      return arr_from_query ('SELECT * FROM `contest` ORDER BY `family_id`, `name`');
    }
    
function manage_contest_update_received ($id) {
    contest_update_received($id);
}

function manage_contest_delete ($id) {
    return db_delete('contest', 'id=' . $id);
}

//----------------certificate functions--------------------

function certificate_get_by_id($id) {
  return db_row_value('certificate', "`id`=$id");
}

function certificate_check_fields($name, $family_id, $id=-1) {
    if ($name == '') {
      add_info("Поле \"Название\" обязательно для заполнения");
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

function certificate_create($name, $family_id, $template = '') {
    global $current_contest;
    if ($family_id==''||$family_id<1)
    {
        $it = contest_get_by_id($current_contest);
        $family_id = $it['family_id'];
    }
    
    if (!certificate_check_fields($name, $family_id)) 
    { return false; }
    
    $certificate_name = db_string($name);
    db_insert('certificate', array('name' => $certificate_name, 'family_id'=>$family_id, 
        'template' => db_string($template)));
    return true;
}

function certificate_create_received() {
    // Get post data
    $name = stripslashes(trim($_POST['name']));
    $family_id = $_POST['family_id'];
    $template = $_POST['template'];
    if (certificate_create($name, $family_id, $template)) {
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
    
    return arr_from_query('SELECT id, name, template
                           FROM `certificate` where family_id='.$family_id.' ORDER BY `id`');
}

function certificate_update($id, $name, $family_id, $template = '') {
    global $current_contest;
    if ($family_id==''||$family_id<1)
    {
        $it = contest_get_by_id($current_contest);
        $family_id = $it['family_id'];
    }
    if (!certificate_check_fields($name, $family_id, $template, $id)) {
      return false;
    }
    $it = certificate_get_by_id($id);
    
    $certificate_name = db_string($name);
    
    $update = array('name' => $certificate_name, 'family_id'=>$family_id,
        'template' => db_string($template!=''?$template:$it['template']));

    db_update('certificate', $update, "`id`=$id");

    return true;
  }

function certificate_update_received($id) {
    // Get post data
    $name = stripslashes(trim($_POST['name']));
    $family_id = $_POST['family_id'];
    $template = $_POST['template'];
    if (certificate_update($id, $name, $family_id, $template)) {
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
      
function manage_certificate_get_list () {
      return arr_from_query ('SELECT * FROM `certificate` ORDER BY `family_id`, `name`');
    }
    
function manage_certificate_update_received ($id) {
    contest_certificate_received($id);
}

function manage_certificate_delete ($id) {
    return db_delete('certificate', 'id=' . $id);
}