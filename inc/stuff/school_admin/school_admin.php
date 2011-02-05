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

    function schooladmin_initialize () {
      if (config_get ('check-database')) {
        db_create_table_safe ('schooladmin', array (
            'user_id'   => 'INT',
            'school_id' => 'INT',
            'second_mail'      => 'TEXT',
            'second_phone'    => 'INT',
            'comment'       => 'TEXT',
          ));
      }
  }
?>
