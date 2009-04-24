<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * IPC main stuff
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

  if ($_WT_ipc_included_ != '###WT_IPC_Inclided###') {
    $_WT_ipc_included_ = '###WT_IPC_Inclided###';

    $dirs = array (
        '/inc/logick/tester/ipc'
      );

    linkage ($dirs);

    function WT_IPC_CheckLogin () {
      global $login, $pass1, $pass2;

      return
        $login == config_get ('WT-IPC-Login') &&
        $pass1 == config_get ('WT-IPC-Pass-1') &&
        $pass2 == config_get ('WT-IPC-Pass-2');
    }
  }
?>
