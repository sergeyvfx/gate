<?php

/**
 * Gate - Wiki engine and web-interface for WebTester Server
 *
 * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
 *
 * This program can be distributed under the terms of the GNU GPL.
 * See the file COPYING.
 */
global $IFACE;

if ($IFACE != "SPAWNING NEW IFACE" || $_GET['IFACE'] != '') {
  print ('HACKERS?');
  die;
}

function contest_initialize() {
  if (config_get('check-database')) {
    if (!db_table_exists ('contest')) {
      db_create_table_safe('contest', array(
          'id' => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
          'name' => 'TEXT'));
      db_insert ('contest', array('name' => '"Тризформашка-2011"'));
    }
  }
}

function contest_get_by_id($id) {
  return db_row_value('contest', "`id`=$id");
}

function contest_check_fields($name, $id=-1) {
    if ($name == '') {
      add_info("Поле \"Название\" обязательно для заполнения");
      return false;
    }
    
    if (db_count ('contest', '`name`="'.$name.'" AND `id`<>'.$id) > 0) {
          add_info ('Конкурс с таким именем уже существует.');
          return false;
        }

    return true;
  }

function contest_create($name) {
    if (!contest_check_fields($name)) {
      return false;
    }
    $contest_name = db_string($name);
    db_insert('contest', array('name' => $contest_name));
    return true;
}

function contest_create_received() {
    // Get post data
    $name = stripslashes(trim($_POST['name']));
    if (contest_create($name)) {
      $_POST = array();
      return true;
    }
    return false;
  }


function contest_list() {
   return arr_from_query('SELECT * FROM `contest` ORDER BY `id`');
}

function contest_update($id, $name) {

    if (!contest_check_fields($name, $id)) {
      return false;
    }

    $contest_name = db_string($name);
    
    $update = array('name' => $contest_name);

    db_update('contest', $update, "`id`=$id");

    return true;
  }

function contest_update_received($id) {
    // Get post data
    $name = stripslashes(trim($_POST['name']));
    if (contest_update($id, $name)) {
      $_POST = array();
    }
  }
      
function manage_contest_get_list () {
      return arr_from_query ('SELECT * FROM `contest` ORDER BY `name`');
    }
    
function manage_contest_update_received ($id) {
    contest_update_received($id);
}

function manage_contest_delete ($id) {
    return db_delete('contest', 'id=' . $id);
}