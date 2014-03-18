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

function school_initialize() {
  if (config_get('check-database')) {
    db_create_table_safe('school', array(
        'id' => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'name' => 'TEXT',
        'status_id' => 'INT',
        'zipcode' => 'TEXT',
        'city_id' => 'INT',
        'street' => 'TEXT',
        'house' => 'TEXT',
        'building' => 'TEXT',
        'flat' => 'TEXT',
        'comment' => 'TEXT',
        'timezone_id' => 'INT'));

    if (!db_table_exists ('school_status')) {
    db_create_table('school_status', array(
        'id' => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'name' => 'TEXT'));
    db_insert('school_status', array ('name' => '"Средняя школа"'));
    db_insert('school_status', array ('name' => '"Гимназия"'));
    db_insert('school_status', array ('name' => '"Лицей"'));
    db_insert('school_status', array ('name' => '"Колледж"'));
    db_insert('school_status', array ('name' => '"Техникум"'));
    db_insert('school_status', array ('name' => '"Учреждение доп. образования"'));
    db_insert('school_status', array ('name' => '"Частная команда"'));
    db_insert('school_status', array ('name' => '"ВУЗ"'));
    }

    if (!db_table_exists ('city_status')) {
    db_create_table('city_status', array(
        'id' => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'name' => 'TEXT',
        'short_name' => 'VARCHAR(10)'));
    db_insert('city_status', array ('name' => '"Город"', 'short_name'=>'г.'));
    db_insert('city_status', array ('name' => '"Поселок городского типа"', 'short_name'=>'п.г.т.'));
    db_insert('city_status', array ('name' => '"Село"', 'short_name'=>'с.'));
    db_insert('city_status', array ('name' => '"Деревня"', 'short_name'=>'д.'));
    }

    db_create_table_safe('country', array(
        'id' => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'name' => 'TEXT'));
    db_create_table_safe('region', array(
        'id' => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'country_id' => 'INT',
        'name' => 'TEXT'));
    db_create_table_safe('area', array(
        'id' => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'region_id' => 'INT',
        'name' => 'TEXT'));
    db_create_table_safe('city', array(
        'id' => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'region_id' => 'INT',
        'area_id' => 'INT',
        'status_id' => 'INT',
        'name' => 'TEXT'));
  }
}

function school_get_by_id($id) {
  return db_row_value('school', "`id`=$id");
}

function school_get_city_name($id) {
  $s = school_get_by_id($id);
  $cid = $s['city_id'];
  $c = db_row_value('city', "`id`=$cid");
  return $c['name'];
}

function school_status_get_by_id($id) {
  return db_row_value('school_status', "`id`=$id");
}

function city_get_by_id($id) {
  return db_row_value('city', "`id`=$id");
}

function area_get_by_id($id) {
  return db_row_value('area', "`id`=$id");
}

function region_get_by_id($id) {
  return db_row_value('region', "`id`=$id");
}

function country_get_by_id($id) {
  return db_row_value('country', "`id`=$id");
}
?>
