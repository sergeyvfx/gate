<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Developer's stuff (settings, datatests, etc...)
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

  if ($_manage_dev_included_ != '#manage_dev_Included#') {
    $_manage_dev_included_ = '#manage_dev_Included#'; 

    include $DOCUMENT_ROOT.'/inc/m_dev/include.php';

    function manage_check_tables () {
        db_create_table_safe ('datatypes', array (
          'id'         => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
          'name'       => 'TEXT',
          'class'      => 'TEXT',
          'refcount'   => 'INT DEFAULT 0',
          'settings'   => 'TEXT DEFAULT ""'
        ));

        db_create_table_safe ('dataset', array (
          'id'         => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
          'name'       => 'TEXT',
          'refcount'   => 'INT DEFAULT 0'
        ));

        db_create_table_safe ('dataset_assoc', array (
          'id'         => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
          'order'      => 'INT',
          'dataset'    => 'INT',
          'datatype'   => 'INT',
          'title'      => 'TEXT',
          'field'      => 'TEXT',
          'settings'   => 'TEXT DEFAULT ""'
        ));

        if (!db_table_exists ('settings')) {
          db_create_table ('settings', array (
            'id'         => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'section'    => 'TEXT',
            'name'       => 'TEXT',
            'ident'      => 'TEXT',
            'class'      => 'TEXT',
            'used'       => 'INT DEFAULT 0',
            'settings'   => 'TEXT DEFAULT ""'
          ));

          db_insert ('settings', array ('section'=>'"Системные"', 'name'=>'"Максимальная длина логина пользователя"', 'ident'=>'"max_user_login_len"', 'class'=>'"CSCNumber"', 'settings'=>'"'.addslashes ('a:1:{s:5:"value";s:2:"14";}').'"', 'used'=>'1'));
          db_insert ('settings', array ('section'=>'"Системные"', 'name'=>'"Максимальная длина имени пользователя"',  'ident'=>'"max_user_name_len"', 'class'=>'"CSCNumber"', 'settings'=>'"'.addslashes ('a:1:{s:5:"value";s:2:"32";}').'"', 'used'=>'1'));
          db_insert ('settings', array ('section'=>'"Системные"', 'name'=>'"Максимальная длина пароля пользователя"', 'ident'=>'"max_user_passwd_len"', 'class'=>'"CSCNumber"', 'settings'=>'"'.addslashes ('a:1:{s:5:"value";s:2:"16";}').'"', 'used'=>'1'));
          db_insert ('settings', array ('section'=>'"Системные"', 'name'=>'"Количество записей на странице &laquo;Пользователи и группы&raquo;"', 'ident'=>'"user_count"', 'class'=>'"CSCNumber"', 'settings'=>'"'.addslashes ('a:1:{s:5:"value";s:2:"15";}').'"', 'used'=>'1'));
          db_insert ('settings', array ('section'=>'"Системные"', 'name'=>'"Блокировать сайт"',  'ident'=>'"site_lock"', 'class'=>'"CSCCheckBox"', 'settings'=>'"'.addslashes ('a:1:{s:5:"value";b:0;}').'"', 'used'=>'1'));
          db_insert ('settings', array ('section'=>'"Системные"', 'name'=>'"Стартовый каталог"', 'ident'=>'"start_root"', 'class'=>'"CSCText"', 'settings'=>'"'.addslashes ('a:1:{s:5:"value";s:1:"/";}').'"',  'used'=>'1'));
        }

        db_create_table_safe ('storage', array (
          'id'         => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
          'name'       => 'TEXT',
          'path'       => 'TEXT',
          'refcount'   => 'INT DEFAULT 0'
        ));

        if (!db_table_exists ('templates')) {
          db_create_table_safe ('templates', array (
            'id'         => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'name'       => 'TEXT',
            'text'       => 'LONGTEXT',
            'refcount'   => 'INT DEFAULT 0',
            'settings'   => 'TEXT DEFAULT ""'
          ));
        }
        manage_template_register_default ();
    }

    function manage_initialize () {
      if (config_get ('check-database')) {
        manage_check_tables ();
      }
    }
  }
?>
