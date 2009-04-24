<?php 
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * IPC handlers
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

  if ($_ipc_included_ != '#ipc_Included#') {
    $_ipc_included_ = '#ipc_Included#';
    $ipc_functions = array ();

    function ipc_check_path_exists () {
      global $cpath;
      if (dir_exists (config_get ('site-root').config_get ('document-root').
                      '/'.$cpath)) {
        print ('+OK');
      } else {
        print ('-ERR');
      }
    }

    function ipc_check_login () {
      global $login, $skipId;

      if ($skipId == '') {
        $skipId = -1;
      }

      if (user_registered_with_login ($login, $skipId)) {
        print ('-ERR');
      } else {
        print ('+OK');
      }
    }

    function ipc_check_email () {
      global $email, $skipId;

      if ($skipId == '') {
        $skipId = -1;
      }

      $user_info = user_info_by_id (user_id ());
      if ($email == config_get ('null-email') &&
          $user_info['email'] != $email && !user_access_root ()) {
        print ('-ERR');
      } else {
        if (user_registered_with_email ($email, $skipId)) {
          print ('-ERR');
        } else {
          print ('+OK');
        }
      }
    }

    function ipc_check_wiki_node () {
      global $cpath, $skipId, $pid;

      if ($skipId == '') {
        $skipId = -1;
      }

      if (wiki_content_present_in_node ($pid, $cpath, $skipId)) {
        print ('-ERR');
      } else {
        print ('+OK');
      }
    }

    function ipc_register_function ($name, $entry) {
      global $ipc_functions;
      $ipc_functions[$name] = array ('entry' => $entry);
    }

    function ipc_initialize () {
      ipc_register_function ('check_login',       ipc_check_login);
      ipc_register_function ('check_email',       ipc_check_email);
      ipc_register_function ('check_wiki_node',   ipc_check_wiki_node);
      ipc_register_function ('check_path_exists', ipc_check_path_exists);
    }

    function ipc_exec ($func) {
      global $ipc_functions;
      if (isset ($ipc_functions[$func]) &&
          function_exists ($ipc_functions[$func]['entry'])) {
        $ipc_functions[$func]['entry'] ();
      }
    }
  }
?>
