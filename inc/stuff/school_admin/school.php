<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Security checking stuff
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

    function school_initialize () {
      if (config_get ('check-database')) {
        db_create_table_safe ('school', array (
            'id'         => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'name'      => 'TEXT',
            'status_id'    => 'INT',
            'index'       => 'TEXT',
            'region' => 'TEXT',
            'district'   => 'TEXT',
            'place_id'     => 'TEXT',
            'street'      => 'TEXT',
            'home_number'      => 'TEXT',
            'building' => 'TEXT',
            'appartments'  => 'TEXT',
          ));

         db_create_table_safe ('status', array (
                                'id'         => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
                                'name'       => 'TEXT',
                             ));
        db_create_table_safe ('region', array (
                                'id'         => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
                                'name'    => 'TEXT',
                             ));
        db_create_table_safe ('district', array (
                                'id'         => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
                                'name'    => 'TEXT',
                             ));
        db_create_table_safe ('place', array (
                                'id'         => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
                                'region_id'         => 'INT',
                                'status_id'         => 'INT',
                                'name'    => 'TEXT',
                             ));
      }
  }

    function school_get_by_id($id) {
    return db_row_value('school', "`id`=$id");
  }
?>
