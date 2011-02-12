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

function responsible_initialize() {
  if (config_get('check-database')) {
    db_create_table_safe('responsible', array(
        'user_id' => 'INT',
        'school_id' => 'INT DEFAULT -1',
        'email' => 'TEXT DEFAULT ""',
        'phone' => 'TEXT DEFAULT ""',
        'comment' => 'TEXT DEFAULT ""',
    ));
  }
}

function is_responsible($id) {
  $id = user_id();
  $g = group_get_by_name("Ответственные");
  return is_user_in_group($id, $g['id']);
}

function responsible_get_by_id($id) {
  return db_row_value('responsible', "`user_id`=$id");
}

function is_responsible_has_school($rid) {
  $r = responsible_get_by_id($rid);
  return $r['school_id'] > 0;
}

?>
