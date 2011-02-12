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

function tipsling_initialize() {
  school_initialize();
  responsible_initialize();
  if (config_get('check-database')) {
    db_create_table_safe('team', array(
        'id' => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'number' => 'INT',
        'responsible_id' => 'INT',
        'contest_id' => 'INT',
        'payment_id' => 'INT',
        'grade' => 'INT',
        'teacher_full_name' => 'TEXT',
        'pupil1_full_name' => 'TEXT',
        'pupil2_full_name' => 'TEXT',
        'pupil3_full_name' => 'TEXT',
        'is_payment' => 'BOOLEAN',
        'comment' => 'TEXT'));

    db_create_table_safe('payment', array(
        'id' => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'responsible_id' => 'INT',
        'date' => 'DATE',
        'cheque_number' => 'TEXT',
        'payer_full_name' => 'TEXT',
        'amount' => 'DOUBLE',
        'comment' => 'TEXT'));
    if (!db_table_exists ('contest')) {
      db_create_table_safe('contest', array(
          'id' => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
          'name' => 'TEXT'));
      db_insert ('contest', array('name' => '"Тризформашка-2011"'));
    }
  }
}

?>
