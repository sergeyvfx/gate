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