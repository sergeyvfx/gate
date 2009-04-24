<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Groups manipulating stuff
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

  if ($_sec_group_included_ != '#sec_group_Included#') {
    $_sec_group_included_ = '#sec_group_Included#';

    function group_create ($name, $default = false) {
      $name = htmlspecialchars (trim ($name));

      if ($name == '') {
        return;
      }

      if (db_count ('group', '`name`="'.$name.'"') > 0) {
        add_info ('Группа с таким именем уже существует.');
        return false;
      } else {
        db_insert ('group', array ('name' => '"'.addslashes ($name).'"',
                                   'default' => ($default)?(1):(0)));
        return true;
      }
    }

    function group_received_create () {
      if (group_create (stripslashes ($_POST['name']),
                        manage_setting_get_received ('default_group'))) {
        $_POST = array ();
      }
    }

    function group_get_by_id ($id) { return db_row_value ('group', "`id`=$id"); }

    function group_delete ($id) {
      db_delete ('group', "`id`=$id");
      db_delete ('usergroup', "`group_id`=$id");
      hook_call ('CORE.Security.OnGroupDelete');
    }

    function group_update ($id) {
      $name = htmlspecialchars (trim ($_POST['name']));

      if ($name == '') {
        return;
      }

      if (db_count ('group', '(`name`="'.$name.'")  AND (`id`<>'.$id.')') > 0) {
        add_info ('Группа с таким именем уже существует.');
      } else {
        db_update ('group', array ('name' => '"'.$name.'"',
                                   'default' => (manage_setting_get_received ('default_group'))?(1):(0)),
                   "`id`=$id");
      }
    }

    function group_list () {
      return arr_from_query ('SELECT * FROM `group` ORDER BY `name`');
    }

    function group_users_inside ($id) {
      return db_field_value ('group', 'refcount', '`id`='.$id);
    }

    function group_default_list () {
      return arr_from_query ('SELECT * FROM `group` '.
                             'WHERE `default`=1 ORDER BY `name`');
    }
  }
?>
