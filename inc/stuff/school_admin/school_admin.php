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
            'second_phone'    => 'TEXT',
            'comment'       => 'TEXT',
          ));
      }
  }

  function check_admin($id)
  {
      /*$q = db_query('SELECT * FROM `usergroup` WHERE `user_id`='.$id);
      if (db_affected () > 0) {
          $arr = db_row_array ($q);
          for ($i = 0; $i < count($arr); $i++)
          {
              $row = $arr[$i];
              if ($row['group_id']=='1')
                  return true;
          }
      }
      return false;*/
   return true;
  }

  function school_admin_get_by_id($id) {
    return db_row_value('schooladmin', "`user_id`=$id");
  }


?>
