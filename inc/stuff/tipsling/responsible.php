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
        'school_id' => 'INT',
        'email' => 'TEXT',
        'phone' => 'TEXT',
        'comment' => 'TEXT',
    ));
  }
}

function is_responsible($id) {
  $id = user_id();
  return is_user_in_group($id, group_get_by_name("Ответственные")) || user_is_system($id);
}

function responsible_get_by_id($id) {
  return db_row_value('responsible', "`user_id`=$id");
}

?>
