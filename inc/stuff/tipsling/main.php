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
  contest_initialize();
  bookkeepers_initialize();
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
        'place' => 'INT DEFAULT NULL',
        'common_place' => 'INT DEFAULT NULL',
        'mark' => 'VARCHAR(11) DEFAULT NULL',));

    db_create_table_safe('payment', array(
        'id' => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'responsible_id' => 'INT',
        'date' => 'DATE',
        'date_arrival' => 'DATE',
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

    if (!db_table_exists('timezone')) {
      db_create_table('timezone', array(
          'id' => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
          'name' => 'TEXT',
          'offset' => 'INT'
      ));
      db_insert('timezone', array('name' => '"Калининград"', 'offset' => '-1'));
      db_insert('timezone', array('name' => '"Москва"', 'offset' => '0'));
      db_insert('timezone', array('name' => '"Самара"', 'offset' => '1'));
      db_insert('timezone', array('name' => '"Ижевск"', 'offset' => '1'));
      db_insert('timezone', array('name' => '"Екатеринбург"', 'offset' => '2'));
      db_insert('timezone', array('name' => '"Пермь"', 'offset' => '2'));
      db_insert('timezone', array('name' => '"Новосибирск"', 'offset' => '3'));
      db_insert('timezone', array('name' => '"Омск"', 'offset' => '3'));
      db_insert('timezone', array('name' => '"Красноярск"', 'offset' => '4'));
      db_insert('timezone', array('name' => '"Новокузнецк"', 'offset' => '4'));
      db_insert('timezone', array('name' => '"Иркутск"', 'offset' => '5'));
      db_insert('timezone', array('name' => '"Якутск"', 'offset' => '6'));
      db_insert('timezone', array('name' => '"Чита"', 'offset' => '6'));
      db_insert('timezone', array('name' => '"Хабаровск"', 'offset' => '7'));
      db_insert('timezone', array('name' => '"Владивосток"', 'offset' => '7'));
      db_insert('timezone', array('name' => '"Магадан"', 'offset' => '8'));
      db_insert('timezone', array('name' => '"Петропавловск-Камчатский"', 'offset' => '9'));
    }
  }
}

?>
