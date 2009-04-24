<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Get checker for uploading
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

  if ($_WT_ipc_get_checker_included_ != '###WT_IPC_GetChecker_Inclided###') {
    $_WT_ipc_get_checker_included_ = '###WT_IPC_GetChecker_Inclided###';

    function WT_GetChecker () {
      if (!WT_IPC_CheckLogin ()) {
        return;
      }

      $r = db_row (db_select ('tester_checkers', array ('*'),
                              '`uploaded`=FALSE', 'LIMIT 1'));

      if ($r) {
        $s = unserialize ($r['settings']);
        $arr = array ('ID' => $r['id'], 'SRC' => $s['src'],
                      'COMPILERID' => $s['compiler_id']);
        print (db_pack ($arr));
      }
    }

    ipc_register_function ('get_checker', WT_GetChecker);
  }
?>
